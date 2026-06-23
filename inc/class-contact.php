<?php

/**
 * Contact Form CPT, AJAX Handler & Admin UI
 *
 * Stores front-end contact submissions as private CPT posts.
 * Sends an email notification on each submission.
 * Optionally verifies reCAPTCHA v3 when keys are configured in the Customizer.
 *
 * Spam protection layers:
 *  - Honeypot fields (website_url, company_name)
 *  - Time-based token check (< 3 s = bot)
 *  - IP rate limiting via transient (3 / hour)
 *  - Content pattern matching (URLs, keywords, Cyrillic)
 *  - reCAPTCHA v3 score threshold (>= 0.5)
 *  - Manual spam / not-spam toggle in the admin list
 *
 * @package PodNest
 * @since   1.2.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Contact
 */
final class PodNest_Contact
{

    /** CPT slug for contact submissions. */
    public const CPT = 'podnest_contact';

    // -- Constructor -----------------------------------------------

    public function __construct()
    {
        add_action('init',               [$this, 'register_post_type']);
        add_action('init',               [$this, 'register_meta']);
        add_action('admin_head',         [$this, 'hide_add_new_button']);
        add_action('add_meta_boxes',     [$this, 'add_meta_box']);
        add_action('wp_enqueue_scripts', [$this, 'localize_config']);

        add_action('wp_ajax_nopriv_podnest_submit_contact', [$this, 'handle_submission']);
        add_action('wp_ajax_podnest_submit_contact',        [$this, 'handle_submission']);

        add_action('admin_action_podnest_spam_toggle', [$this, 'handle_spam_toggle']);
        add_action('restrict_manage_posts',            [$this, 'spam_filter_dropdown']);
        add_filter('post_row_actions',                 [$this, 'spam_row_actions'], 10, 2);

        add_filter('manage_' . self::CPT . '_posts_columns',       [$this, 'admin_columns']);
        add_action('manage_' . self::CPT . '_posts_custom_column', [$this, 'admin_column_content'], 10, 2);
    }

    // -- CPT registration ------------------------------------------

    /**
     * Registers the contact submissions CPT and the custom 'spam' post status.
     *
     * @return void
     */
    public function register_post_type(): void
    {
        register_post_type(self::CPT, [
            'labels'      => [
                'name'          => __('Form Submissions', 'podnest'),
                'singular_name' => __('Submission', 'podnest'),
                'menu_name'     => __('Form Items', 'podnest'),
                'edit_item'     => __('View Submission', 'podnest'),
                'all_items'     => __('All Submissions', 'podnest'),
            ],
            'public'             => false,
            'show_ui'       => true,
            'show_in_menu'  => true,
            'menu_icon'     => 'dashicons-email',
            'menu_position' => 27,
            'show_in_rest'       => false,
            'capability_type'    => 'post',
            'capabilities'       => ['create_posts' => 'do_not_allow'],
            'map_meta_cap'       => true,
            'supports'           => ['title'],
            'rewrite'            => false,
            'has_archive'        => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
        ]);

        register_post_status('spam', [
            'label'                     => _x('Spam', 'post status', 'podnest'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop(
                'Spam <span class="count">(%s)</span>',
                'Spam <span class="count">(%s)</span>',
                'podnest'
            ),
        ]);
    }

    /**
     * Registers post meta fields for contact submissions.
     *
     * @return void
     */
    public function register_meta(): void
    {
        $auth   = static fn() => current_user_can('edit_posts');
        $fields = [
            '_pn_first_name'   => 'string',
            '_pn_last_name'    => 'string',
            '_pn_sender_email' => 'string',
            '_pn_phone'        => 'string',
            '_pn_subject'      => 'string',
            '_pn_message'      => 'string',
            '_pn_status'       => 'string', /* new | read */
            '_pn_ip'           => 'string',
            '_pn_user_agent'   => 'string',
        ];

        foreach ($fields as $key => $type) {
            register_post_meta(self::CPT, $key, [
                'show_in_rest'  => false,
                'single'        => true,
                'type'          => $type,
                'auth_callback' => $auth,
            ]);
        }
    }

    // -- Admin UI --------------------------------------------------

    /**
     * Hides the "Add New" button on the contact CPT admin screen
     * since submissions are created only via the front-end form.
     *
     * @return void
     */
    public function hide_add_new_button(): void
    {
        global $typenow;
        if (self::CPT === $typenow) {
            echo '<style>.page-title-action{display:none!important}</style>';
        }
    }

    /**
     * Registers the submission detail meta box.
     *
     * @return void
     */
    public function add_meta_box(): void
    {
        add_meta_box(
            'pn-contact-view',
            __('Submission Details', 'podnest'),
            [$this, 'render_meta_box'],
            self::CPT,
            'normal',
            'high'
        );
    }

    /**
     * Renders the read-only submission detail meta box.
     * Automatically marks the submission as read on first view.
     *
     * @param  WP_Post $post Current post.
     * @return void
     */
    public function render_meta_box(WP_Post $post): void
    {
        $first   = (string) get_post_meta($post->ID, '_pn_first_name',   true);
        $last    = (string) get_post_meta($post->ID, '_pn_last_name',    true);
        $email   = (string) get_post_meta($post->ID, '_pn_sender_email', true);
        $phone   = (string) get_post_meta($post->ID, '_pn_phone',        true);
        $subject = (string) get_post_meta($post->ID, '_pn_subject',      true);
        $message = (string) get_post_meta($post->ID, '_pn_message',      true);
        $status  = (string) (get_post_meta($post->ID, '_pn_status',      true) ?: 'new');
        $ip      = (string) get_post_meta($post->ID, '_pn_ip',           true);
        $ua      = (string) get_post_meta($post->ID, '_pn_user_agent',   true);

        /* Auto-mark as read on first view */
        if ('new' === $status) {
            update_post_meta($post->ID, '_pn_status', 'read');
        }
?>
        <table class="form-table" role="presentation">
            <tr>
                <th><?php esc_html_e('Name', 'podnest'); ?></th>
                <td><?php echo esc_html($first . ' ' . $last); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Email', 'podnest'); ?></th>
                <td><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></td>
            </tr>
            <?php if ($phone) : ?>
                <tr>
                    <th><?php esc_html_e('Phone', 'podnest'); ?></th>
                    <td><?php echo esc_html($phone); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th><?php esc_html_e('Subject', 'podnest'); ?></th>
                <td><?php echo esc_html($subject); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Message', 'podnest'); ?></th>
                <td>
                    <pre style="white-space:pre-wrap;font-family:inherit;background:#f6f7f7;padding:12px;border-radius:4px;"><?php echo esc_html($message); ?></pre>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('IP Address', 'podnest'); ?></th>
                <td><?php echo esc_html($ip); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('User Agent', 'podnest'); ?></th>
                <td style="font-size:0.85em;color:#666;"><?php echo esc_html($ua); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Received', 'podnest'); ?></th>
                <td><?php echo esc_html(get_the_date('F j, Y \a\t g:i a', $post)); ?></td>
            </tr>
        </table>
    <?php
    }

    /**
     * Custom admin list columns for the submissions screen.
     *
     * @param  array<string, string> $columns Default columns.
     * @return array<string, string>
     */
    public function admin_columns(array $columns): array
    {
        return [
            'cb'         => $columns['cb'] ?? '',
            'title'      => __('Name', 'podnest'),
            'pn_email'   => __('Email', 'podnest'),
            'pn_subject' => __('Subject', 'podnest'),
            'pn_spam'    => __('Spam', 'podnest'),
            'pn_read'    => __('Read', 'podnest'),
            'date'       => __('Date', 'podnest'),
        ];
    }

    /**
     * Renders custom column content for each submission row.
     *
     * @param  string $column  Column name.
     * @param  int    $post_id Post ID.
     * @return void
     */
    public function admin_column_content(string $column, int $post_id): void
    {
        switch ($column) {
            case 'pn_email':
                $email = (string) get_post_meta($post_id, '_pn_sender_email', true);
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                break;

            case 'pn_subject':
                echo esc_html((string) get_post_meta($post_id, '_pn_subject', true));
                break;

            case 'pn_spam':
                $spam = get_post_status($post_id) === 'spam';
                printf(
                    '<span style="color:%s;font-weight:600;">%s</span>',
                    $spam ? '#dc3232' : '#46b450',
                    $spam
                        ? '⚠ ' . esc_html__('Spam', 'podnest')
                        : '✓ ' . esc_html__('Clean', 'podnest')
                );
                break;

            case 'pn_read':
                $status = (string) (get_post_meta($post_id, '_pn_status', true) ?: 'new');
                printf(
                    '<span style="color:%s;font-weight:600;text-transform:capitalize;">%s</span>',
                    'new' === $status ? '#00e5a0' : '#6b8cae',
                    esc_html($status)
                );
                break;
        }
    }

    /**
     * Adds Mark as Spam / Not Spam row actions to the submissions list.
     *
     * @param  array<string, string> $actions Existing row actions.
     * @param  WP_Post               $post    Current post.
     * @return array<string, string>
     */
    public function spam_row_actions(array $actions, WP_Post $post): array
    {
        if ($post->post_type !== self::CPT) {
            return $actions;
        }

        $nonce = wp_create_nonce('podnest_spam_toggle_' . $post->ID);

        if (get_post_status($post->ID) === 'spam') {
            $actions['not_spam'] = sprintf(
                '<a href="%s" style="color:#46b450;">%s</a>',
                esc_url(admin_url('admin.php?action=podnest_spam_toggle&post_id=' . $post->ID . '&mark=clean&_wpnonce=' . $nonce)),
                esc_html__('Not Spam', 'podnest')
            );
        } else {
            $actions['mark_spam'] = sprintf(
                '<a href="%s" style="color:#dc3232;">%s</a>',
                esc_url(admin_url('admin.php?action=podnest_spam_toggle&post_id=' . $post->ID . '&mark=spam&_wpnonce=' . $nonce)),
                esc_html__('Mark as Spam', 'podnest')
            );
        }

        return $actions;
    }

    /**
     * Renders the spam/clean status filter dropdown on the submissions list screen.
     *
     * @return void
     */
    public function spam_filter_dropdown(): void
    {
        global $typenow;
        if ($typenow !== self::CPT) {
            return;
        }

        $current = sanitize_text_field($_GET['post_status'] ?? '');
    ?>
        <select name="post_status">
            <option value=""><?php esc_html_e('All Statuses', 'podnest'); ?></option>
            <option value="publish" <?php selected($current, 'publish'); ?>><?php esc_html_e('Clean', 'podnest'); ?></option>
            <option value="spam" <?php selected($current, 'spam');    ?>><?php esc_html_e('Spam',  'podnest'); ?></option>
        </select>
<?php
    }

    /**
     * Handles the spam toggle admin action.
     *
     * @return void
     */
    public function handle_spam_toggle(): void
    {
        if (! current_user_can('edit_posts')) {
            wp_die(__('Unauthorized', 'podnest'));
        }

        $post_id = (int) ($_GET['post_id'] ?? 0);
        $mark    = sanitize_text_field($_GET['mark'] ?? '');

        if (! wp_verify_nonce($_GET['_wpnonce'] ?? '', 'podnest_spam_toggle_' . $post_id)) {
            wp_die(__('Security check failed', 'podnest'));
        }

        if ($post_id && get_post_type($post_id) === self::CPT) {
            wp_update_post([
                'ID'          => $post_id,
                'post_status' => $mark === 'spam' ? 'spam' : 'publish',
            ]);
        }

        wp_safe_redirect(admin_url('edit.php?post_type=' . self::CPT));
        exit;
    }

    // -- Frontend --------------------------------------------------

    /**
     * Outputs localised config for the contact form JS module.
     * Also conditionally enqueues the reCAPTCHA v3 library when the
     * contact form block is present on the current page.
     *
     * @return void
     */
    public function localize_config(): void
    {
        $site_key = get_theme_mod('podnest_recaptcha_site_key', '');

        if ($site_key && has_block('podnest/contact-form')) {
            wp_enqueue_script(
                'google-recaptcha',
                'https://www.google.com/recaptcha/api.js?render=' . rawurlencode($site_key),
                [],
                null,
                true
            );
        }

        wp_add_inline_script(
            'podnest-app',
            'window.podnestContact=' . wp_json_encode([
                'ajaxUrl'      => admin_url('admin-ajax.php'),
                'nonce'        => wp_create_nonce('podnest_contact_form'),
                'recaptchaKey' => $site_key,
            ]) . ';',
            'before'
        );
    }

    // -- AJAX handler ----------------------------------------------

    /**
     * Handles the contact form AJAX submission.
     *
     * Protection layers applied in order:
     *  1. Nonce verification
     *  2. Honeypot fields
     *  3. Time-based token (< 3 s = bot)
     *  4. IP rate limiting (3 / hour via transient)
     *  5. Input validation
     *  6. Spam content pattern matching
     *  7. Excessive URL count check
     *  8. Cyrillic character detection
     *  9. reCAPTCHA v3 score (optional, >= 0.5)
     *
     * @return void
     */
    public function handle_submission(): void
    {
        if (! check_ajax_referer('podnest_contact_form', 'nonce', false)) {
            wp_send_json_error(['message' => __('Security check failed. Please refresh and try again.', 'podnest')]);
        }

        /* 1. Honeypot — silently succeed to avoid revealing detection */
        if (! empty($_POST['website_url']) || ! empty($_POST['company_name'])) {
            wp_send_json_success(['message' => __("Thank you! Your message has been sent. We'll be in touch soon.", 'podnest')]);
        }

        /* 2. Time-based token — submissions under 3 seconds are bots */
        $form_time = (int) base64_decode(sanitize_text_field($_POST['form_token'] ?? ''));
        if ($form_time && (time() - $form_time) < 3) {
            wp_send_json_success(['message' => __("Thank you! Your message has been sent. We'll be in touch soon.", 'podnest')]);
        }

        /* 3. IP rate limiting — max 3 submissions per hour */
        $ip       = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
        $rate_key = 'pn_contact_limit_' . md5($ip);
        $hits     = (int) get_transient($rate_key);
        if ($hits >= 3) {
            wp_send_json_error(['message' => __('Too many submissions. Please try again later.', 'podnest')]);
        }
        set_transient($rate_key, $hits + 1, HOUR_IN_SECONDS);

        /* 4. Sanitise inputs */
        $first   = sanitize_text_field($_POST['first_name'] ?? '');
        $last    = sanitize_text_field($_POST['last_name']  ?? '');
        $email   = sanitize_email($_POST['email']           ?? '');
        $phone   = sanitize_text_field($_POST['phone']      ?? '');
        $subject = sanitize_text_field($_POST['subject']    ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');

        /* 5. Required field validation */
        if (empty($first) || empty($last) || empty($email) || empty($message)) {
            wp_send_json_error(['message' => __('Please fill in all required fields.', 'podnest')]);
        }

        if (! is_email($email)) {
            wp_send_json_error(['message' => __('Please enter a valid email address.', 'podnest')]);
        }

        /* 6. Spam content patterns */
        $combined = $first . ' ' . $last . ' ' . $message;
        $patterns = [
            '/\[url=/i',
            '/\[link=/i',
            '/<a\s+href/i',
            '/https?:\/\/[^\s]+\s+https?:\/\//i',
            '/viagra|cialis|casino|lottery|cryptocurrency|bitcoin|crypto wallet/i',
            '/click here.*https?:\/\//i',
            '/dear\s+(sir|madam|friend)/i',
            '/\b(SEO|backlink|rank|traffic)\b.*\b(service|offer|guarantee)/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $combined)) {
                wp_send_json_success(['message' => __("Thank you! Your message has been sent. We'll be in touch soon.", 'podnest')]);
            }
        }

        /* 7. Excessive URLs */
        if (preg_match_all('/https?:\/\//i', $message) > 2) {
            wp_send_json_success(['message' => __("Thank you! Your message has been sent. We'll be in touch soon.", 'podnest')]);
        }

        /* 8. Cyrillic content */
        if (preg_match('/[\x{0400}-\x{04FF}]/u', $combined)) {
            wp_send_json_success(['message' => __("Thank you! Your message has been sent. We'll be in touch soon.", 'podnest')]);
        }

        /* 9. Optional reCAPTCHA v3 */
        $secret = get_theme_mod('podnest_recaptcha_secret_key', '');
        if ($secret) {
            $token = sanitize_text_field($_POST['recaptcha_token'] ?? '');
            if (! $this->verify_recaptcha($token, $secret)) {
                wp_send_json_error(['message' => __('Verification failed. Please try again.', 'podnest')]);
            }
        }

        /* Store submission */
        $name    = $first . ' ' . $last;
        $post_id = wp_insert_post([
            'post_type'   => self::CPT,
            'post_title'  => $name,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => __('Could not save your message. Please try again.', 'podnest')]);
        }

        update_post_meta($post_id, '_pn_first_name',   $first);
        update_post_meta($post_id, '_pn_last_name',    $last);
        update_post_meta($post_id, '_pn_sender_email', $email);
        update_post_meta($post_id, '_pn_phone',        $phone);
        update_post_meta($post_id, '_pn_subject',      $subject ?: __('Contact Form Submission', 'podnest'));
        update_post_meta($post_id, '_pn_message',      $message);
        update_post_meta($post_id, '_pn_status',       'new');
        update_post_meta($post_id, '_pn_ip',           $ip);
        update_post_meta($post_id, '_pn_user_agent',   sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? '')));

        $this->send_notification($first, $last, $email, $phone, $subject, $message, $post_id);

        wp_send_json_success(['message' => __("Thank you! Your message has been sent. We'll be in touch soon.", 'podnest')]);
    }

    /**
     * Verifies a reCAPTCHA v3 token against Google's API.
     * Returns false if the score is below 0.5 (likely a bot).
     *
     * @param  string $token  Token from front-end grecaptcha.execute().
     * @param  string $secret reCAPTCHA secret key from the Customizer.
     * @return bool
     */
    private function verify_recaptcha(string $token, string $secret): bool
    {
        if (empty($token)) {
            return false;
        }

        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body'    => ['secret' => $secret, 'response' => $token],
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return ! empty($body['success']) && ((float) ($body['score'] ?? 0)) >= 0.5;
    }

    /**
     * Sends a plain-text notification email to the admin address.
     *
     * @param  string $first   Sender's first name.
     * @param  string $last    Sender's last name.
     * @param  string $email   Sender's email address.
     * @param  string $phone   Sender's phone number (may be empty).
     * @param  string $subject Submission subject.
     * @param  string $message Submission message body.
     * @param  int    $post_id Saved submission post ID for the admin link.
     * @return void
     */
    private function send_notification(
        string $first,
        string $last,
        string $email,
        string $phone,
        string $subject,
        string $message,
        int $post_id
    ): void {
        $admin = get_option('admin_email');
        $site  = get_bloginfo('name');
        $subj  = $subject
            ? sprintf('[%s] %s', $site, $subject)
            : sprintf('[%s] New Contact Submission', $site);

        $body  = "Name:    {$first} {$last}\n";
        $body .= "Email:   {$email}\n";
        if ($phone) {
            $body .= "Phone:   {$phone}\n";
        }
        $body .= "Subject: {$subject}\n\n";
        $body .= "Message:\n{$message}\n\n";
        $body .= "---\n";
        $body .= 'View submission: ' . admin_url('post.php?post=' . $post_id . '&action=edit');

        wp_mail(
            $admin,
            $subj,
            $body,
            [
                'Reply-To: ' . $first . ' ' . $last . ' <' . $email . '>',
                'Content-Type: text/plain; charset=UTF-8',
            ]
        );
    }
}
