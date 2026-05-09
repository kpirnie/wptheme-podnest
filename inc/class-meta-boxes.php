<?php

/**
 * Meta Box UI & Save Logic
 *
 * Renders and saves the custom meta box interfaces for the Feature,
 * Runtime, and Pricing post types. The Marquee CPT uses only the
 * default title field, so it has no custom meta box.
 *
 * Each post type gets its own render and save method pair, keeping the
 * field definitions co-located and easy to maintain.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Meta_Boxes
 *
 * Self-registers all hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_Meta_Boxes
{

    // -- Constructor -----------------------------------------------

    /**
     * Registers WordPress hooks for meta box registration, saving,
     * and admin-list custom columns.
     */
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post',      [$this, 'save']);

        /* Custom admin list columns for Feature */
        add_filter('manage_' . PodNest_CPTs::FEATURE . '_posts_columns',        [$this, 'feature_list_columns']);
        add_action('manage_' . PodNest_CPTs::FEATURE . '_posts_custom_column',   [$this, 'feature_list_column_content'], 10, 2);

        /* Custom admin list columns for Runtime */
        add_filter('manage_' . PodNest_CPTs::RUNTIME . '_posts_columns',        [$this, 'runtime_list_columns']);
        add_action('manage_' . PodNest_CPTs::RUNTIME . '_posts_custom_column',   [$this, 'runtime_list_column_content'], 10, 2);

        /* Custom admin list columns for Pricing */
        add_filter('manage_' . PodNest_CPTs::PRICING . '_posts_columns',        [$this, 'pricing_list_columns']);
        add_action('manage_' . PodNest_CPTs::PRICING . '_posts_custom_column',   [$this, 'pricing_list_column_content'], 10, 2);
    }

    // -- Meta box registration -------------------------------------

    /**
     * Adds meta boxes to the appropriate post type edit screens.
     *
     * @return void
     */
    public function register(): void
    {
        add_meta_box(
            'pn-feature-meta',
            __('Feature Settings', 'podnest'),
            [$this, 'render_feature'],
            PodNest_CPTs::FEATURE,
            'side',
            'high'
        );

        add_meta_box(
            'pn-runtime-meta',
            __('Runtime Settings', 'podnest'),
            [$this, 'render_runtime'],
            PodNest_CPTs::RUNTIME,
            'normal',
            'high'
        );

        add_meta_box(
            'pn-pricing-meta',
            __('Pricing Settings', 'podnest'),
            [$this, 'render_pricing'],
            PodNest_CPTs::PRICING,
            'normal',
            'high'
        );
    }

    // -- Render callbacks ------------------------------------------

    /**
     * Renders the Feature meta box UI.
     *
     * Fields: emoji icon, optional learn-more URL.
     * Description is stored in the post excerpt / content.
     *
     * @param  WP_Post $post The post being edited.
     * @return void
     */
    public function render_feature(WP_Post $post): void
    {
        $this->nonce_field('pn_feature_save', 'pn_feature_nonce');
        $icon = (string) get_post_meta($post->ID, '_pn_icon', true);
        $url  = (string) get_post_meta($post->ID, '_pn_learn_more_url', true);
?>
        <p>
            <label for="pn_icon" style="display:block;font-weight:600;margin-bottom:4px;">
                <?php esc_html_e('Icon (emoji or short text)', 'podnest'); ?>
            </label>
            <input type="text"
                id="pn_icon"
                name="pn_icon"
                value="<?php echo esc_attr($icon); ?>"
                style="width:100%;"
                placeholder="🛡️">
        </p>
        <p>
            <label for="pn_learn_more_url" style="display:block;font-weight:600;margin-bottom:4px;">
                <?php esc_html_e('Learn More URL (optional)', 'podnest'); ?>
            </label>
            <input type="url"
                id="pn_learn_more_url"
                name="pn_learn_more_url"
                value="<?php echo esc_attr($url); ?>"
                style="width:100%;"
                placeholder="https://…">
        </p>
        <p class="description">
            <?php esc_html_e('Title → card heading. Excerpt/Content → card description.', 'podnest'); ?>
        </p>
    <?php
    }

    /**
     * Renders the Runtime meta box UI.
     *
     * Fields: emoji icon, newline-separated version list, learn-more URL.
     *
     * @param  WP_Post $post The post being edited.
     * @return void
     */
    public function render_runtime(WP_Post $post): void
    {
        $this->nonce_field('pn_runtime_save', 'pn_runtime_nonce');
        $icon     = (string) get_post_meta($post->ID, '_pn_icon', true);
        $versions = (string) get_post_meta($post->ID, '_pn_versions', true);
        $url      = (string) get_post_meta($post->ID, '_pn_learn_more_url', true);
    ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="pn_icon"><?php esc_html_e('Icon (emoji)', 'podnest'); ?></label></th>
                <td>
                    <input type="text"
                        id="pn_icon"
                        name="pn_icon"
                        value="<?php echo esc_attr($icon); ?>"
                        class="small-text"
                        placeholder="🌐">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_versions"><?php esc_html_e('Versions', 'podnest'); ?></label></th>
                <td>
                    <textarea id="pn_versions"
                        name="pn_versions"
                        rows="5"
                        class="large-text"
                        placeholder="PHP 8.2&#10;PHP 8.3&#10;PHP 8.4"><?php echo esc_textarea($versions); ?></textarea>
                    <p class="description">
                        <?php esc_html_e('One version label per line. Displayed as chip badges on the card.', 'podnest'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_learn_more_url"><?php esc_html_e('Learn More URL', 'podnest'); ?></label></th>
                <td>
                    <input type="url"
                        id="pn_learn_more_url"
                        name="pn_learn_more_url"
                        value="<?php echo esc_attr($url); ?>"
                        class="large-text"
                        placeholder="https://…">
                </td>
            </tr>
        </table>
        <p class="description" style="margin-top:10px;">
            <?php esc_html_e('Title → runtime name. Excerpt → short card description. Content → full detail-page content.', 'podnest'); ?>
        </p>
    <?php
    }

    /**
     * Renders the Pricing meta box UI.
     *
     * Fields: tier label, price, unit, featured flag, badge, features list,
     * CTA text and URL.
     *
     * @param  WP_Post $post The post being edited.
     * @return void
     */
    public function render_pricing(WP_Post $post): void
    {
        $this->nonce_field('pn_pricing_save', 'pn_pricing_nonce');

        /* Retrieve saved values with sane defaults */
        $fields = [
            'price'         => (string) get_post_meta($post->ID, '_pn_price', true),
            'price_unit'    => (string) (get_post_meta($post->ID, '_pn_price_unit', true) ?: '/ hour'),
            'tier_label'    => (string) get_post_meta($post->ID, '_pn_tier_label', true),
            'badge_text'    => (string) get_post_meta($post->ID, '_pn_badge_text', true),
            'is_featured'   => (bool)   get_post_meta($post->ID, '_pn_is_featured', true),
            'features_list' => (string) get_post_meta($post->ID, '_pn_features_list', true),
            'cta_text'      => (string) (get_post_meta($post->ID, '_pn_cta_text', true) ?: 'Get in Touch'),
            'cta_url'       => (string) get_post_meta($post->ID, '_pn_cta_url', true),
        ];
    ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="pn_tier_label"><?php esc_html_e('Tier Label', 'podnest'); ?></label></th>
                <td>
                    <input type="text" id="pn_tier_label" name="pn_tier_label"
                        value="<?php echo esc_attr($fields['tier_label']); ?>"
                        class="regular-text" placeholder="<?php esc_attr_e('e.g. Hourly', 'podnest'); ?>">
                    <p class="description"><?php esc_html_e('Displayed in small caps above the price.', 'podnest'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_price"><?php esc_html_e('Price', 'podnest'); ?></label></th>
                <td>
                    <input type="text" id="pn_price" name="pn_price"
                        value="<?php echo esc_attr($fields['price']); ?>"
                        class="small-text" placeholder="49">
                    &nbsp;
                    <input type="text" id="pn_price_unit" name="pn_price_unit"
                        value="<?php echo esc_attr($fields['price_unit']); ?>"
                        class="regular-text" placeholder="/ hour">
                    <p class="description"><?php esc_html_e('Amount (no $ symbol) and unit label.', 'podnest'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Featured?', 'podnest'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" id="pn_is_featured" name="pn_is_featured"
                            value="1" <?php checked($fields['is_featured']); ?>>
                        <?php esc_html_e('Highlight this card as the recommended tier', 'podnest'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_badge_text"><?php esc_html_e('Badge Text', 'podnest'); ?></label></th>
                <td>
                    <input type="text" id="pn_badge_text" name="pn_badge_text"
                        value="<?php echo esc_attr($fields['badge_text']); ?>"
                        class="regular-text" placeholder="Most Popular">
                    <p class="description"><?php esc_html_e('Shown only when Featured is checked.', 'podnest'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_features_list"><?php esc_html_e('Included Features', 'podnest'); ?></label></th>
                <td>
                    <textarea id="pn_features_list" name="pn_features_list"
                        rows="8" class="large-text"><?php echo esc_textarea($fields['features_list']); ?></textarea>
                    <p class="description">
                        <?php esc_html_e('One item per line. Prefix with "x:" to mark as unavailable. Example: x:Priority response', 'podnest'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_cta_text"><?php esc_html_e('CTA Button', 'podnest'); ?></label></th>
                <td>
                    <input type="text" id="pn_cta_text" name="pn_cta_text"
                        value="<?php echo esc_attr($fields['cta_text']); ?>"
                        class="regular-text" placeholder="Get in Touch">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_cta_url"><?php esc_html_e('CTA URL', 'podnest'); ?></label></th>
                <td>
                    <input type="url" id="pn_cta_url" name="pn_cta_url"
                        value="<?php echo esc_attr($fields['cta_url']); ?>"
                        class="large-text" placeholder="https://…">
                </td>
            </tr>
        </table>
        <p class="description" style="margin-top:10px;">
            <?php esc_html_e('Title → card heading. Content → short description paragraph.', 'podnest'); ?>
        </p>
<?php
    }

    // -- Save handler ----------------------------------------------

    /**
     * Dispatches save logic to the appropriate private method based on
     * which nonce is present in $_POST.
     *
     * Bails early on autosave or when the current user cannot edit the post.
     *
     * @param  int $post_id The post being saved.
     * @return void
     */
    public function save(int $post_id): void
    {
        /* Never save during autosave */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        /* Verify the user has edit capability for this post */
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if ($this->verify_nonce('pn_feature_nonce', 'pn_feature_save')) {
            $this->save_feature($post_id);
        }

        if ($this->verify_nonce('pn_runtime_nonce', 'pn_runtime_save')) {
            $this->save_runtime($post_id);
        }

        if ($this->verify_nonce('pn_pricing_nonce', 'pn_pricing_save')) {
            $this->save_pricing($post_id);
        }
    }

    /**
     * Saves Feature meta fields.
     *
     * @param  int $post_id Target post ID.
     * @return void
     */
    private function save_feature(int $post_id): void
    {
        update_post_meta($post_id, '_pn_icon',          sanitize_text_field($_POST['pn_icon'] ?? ''));
        update_post_meta($post_id, '_pn_learn_more_url', esc_url_raw($_POST['pn_learn_more_url'] ?? ''));
    }

    /**
     * Saves Runtime meta fields.
     *
     * @param  int $post_id Target post ID.
     * @return void
     */
    private function save_runtime(int $post_id): void
    {
        update_post_meta($post_id, '_pn_icon',           sanitize_text_field($_POST['pn_icon'] ?? ''));
        update_post_meta($post_id, '_pn_versions',       sanitize_textarea_field($_POST['pn_versions'] ?? ''));
        update_post_meta($post_id, '_pn_learn_more_url', esc_url_raw($_POST['pn_learn_more_url'] ?? ''));
    }

    /**
     * Saves Pricing meta fields.
     *
     * @param  int $post_id Target post ID.
     * @return void
     */
    private function save_pricing(int $post_id): void
    {
        update_post_meta($post_id, '_pn_price',          sanitize_text_field($_POST['pn_price'] ?? ''));
        update_post_meta($post_id, '_pn_price_unit',     sanitize_text_field($_POST['pn_price_unit'] ?? ''));
        update_post_meta($post_id, '_pn_tier_label',     sanitize_text_field($_POST['pn_tier_label'] ?? ''));
        update_post_meta($post_id, '_pn_badge_text',     sanitize_text_field($_POST['pn_badge_text'] ?? ''));
        update_post_meta($post_id, '_pn_is_featured',    ! empty($_POST['pn_is_featured']));
        update_post_meta($post_id, '_pn_features_list',  sanitize_textarea_field($_POST['pn_features_list'] ?? ''));
        update_post_meta($post_id, '_pn_cta_text',       sanitize_text_field($_POST['pn_cta_text'] ?? ''));
        update_post_meta($post_id, '_pn_cta_url',        esc_url_raw($_POST['pn_cta_url'] ?? ''));
    }

    // -- Admin list columns ----------------------------------------

    /**
     * Inserts an Icon column into the Feature admin list.
     *
     * @param  array<string, string> $columns Default column map.
     * @return array<string, string>
     */
    public function feature_list_columns(array $columns): array
    {
        return $this->insert_after_title($columns, ['pn_icon' => __('Icon', 'podnest')]);
    }

    /**
     * Renders the Icon column content for a Feature row.
     *
     * @param  string $column  Current column name.
     * @param  int    $post_id Post ID for the current row.
     * @return void
     */
    public function feature_list_column_content(string $column, int $post_id): void
    {
        if ('pn_icon' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_pn_icon', true));
        }
    }

    /**
     * Inserts Icon and Versions columns into the Runtime admin list.
     *
     * @param  array<string, string> $columns Default column map.
     * @return array<string, string>
     */
    public function runtime_list_columns(array $columns): array
    {
        return $this->insert_after_title($columns, [
            'pn_icon'     => __('Icon', 'podnest'),
            'pn_versions' => __('Versions', 'podnest'),
        ]);
    }

    /**
     * Renders custom column content for a Runtime row.
     *
     * @param  string $column  Current column name.
     * @param  int    $post_id Post ID for the current row.
     * @return void
     */
    public function runtime_list_column_content(string $column, int $post_id): void
    {
        if ('pn_icon' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_pn_icon', true));
        }
        if ('pn_versions' === $column) {
            $raw = (string) get_post_meta($post_id, '_pn_versions', true);
            $lines = array_slice(array_filter(array_map('trim', explode("\n", $raw))), 0, 3);
            echo esc_html(implode(', ', $lines)) . (str_word_count($raw) > 3 ? '…' : '');
        }
    }

    /**
     * Inserts Price and Featured columns into the Pricing admin list.
     *
     * @param  array<string, string> $columns Default column map.
     * @return array<string, string>
     */
    public function pricing_list_columns(array $columns): array
    {
        return $this->insert_after_title($columns, [
            'pn_price'    => __('Price', 'podnest'),
            'pn_featured' => __('Featured', 'podnest'),
        ]);
    }

    /**
     * Renders custom column content for a Pricing row.
     *
     * @param  string $column  Current column name.
     * @param  int    $post_id Post ID for the current row.
     * @return void
     */
    public function pricing_list_column_content(string $column, int $post_id): void
    {
        if ('pn_price' === $column) {
            $price = get_post_meta($post_id, '_pn_price', true);
            $unit  = get_post_meta($post_id, '_pn_price_unit', true);
            printf('$%s %s', esc_html((string) $price), esc_html((string) $unit));
        }
        if ('pn_featured' === $column) {
            echo get_post_meta($post_id, '_pn_is_featured', true) ? '⭐ Yes' : '—';
        }
    }

    // -- Private utilities -----------------------------------------

    /**
     * Outputs a WordPress nonce field for a meta box.
     *
     * @param  string $action The nonce action string.
     * @param  string $name   The nonce field name in $_POST.
     * @return void
     */
    private function nonce_field(string $action, string $name): void
    {
        wp_nonce_field($action, $name);
    }

    /**
     * Verifies a nonce from $_POST.
     *
     * @param  string $field  The nonce field name in $_POST.
     * @param  string $action The expected nonce action.
     * @return bool           True when the nonce is valid.
     */
    private function verify_nonce(string $field, string $action): bool
    {
        return isset($_POST[$field])
            && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$field])), $action);
    }

    /**
     * Inserts new columns immediately after the 'title' column.
     *
     * @param  array<string, string> $columns  Existing columns.
     * @param  array<string, string> $new_cols Columns to insert.
     * @return array<string, string>
     */
    private function insert_after_title(array $columns, array $new_cols): array
    {
        $out = [];
        foreach ($columns as $key => $label) {
            $out[$key] = $label;
            if ('title' === $key) {
                foreach ($new_cols as $nk => $nl) {
                    $out[$nk] = $nl;
                }
            }
        }
        return $out;
    }
}
