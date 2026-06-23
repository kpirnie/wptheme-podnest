<?php

/**
 * Contact Form
 *
 * Rendered as the `podnest/contact-form` block via PodNest_Blocks::render_contact_form().
 * AJAX submission is handled by PodNest_Contact::handle_submission()
 * via wp_ajax_podnest_submit_contact.
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>
<div id="pn-contact-form-wrap" class="pn-contact-form-wrap">

    <div id="pn-contact-success" class="pn-contact-success" aria-live="polite" hidden>
        <span class="pn-success-icon" aria-hidden="true">✓</span>
        <p id="pn-contact-success-msg"></p>
    </div>

    <form id="pn-contact-form" class="pn-contact-form" novalidate aria-label="<?php esc_attr_e('Contact form', 'podnest'); ?>">

        <!-- Honeypot fields -->
        <div style="position:absolute;left:-9999px;" aria-hidden="true">
            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
            <input type="text" name="company_name" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="form_token" value="<?php echo esc_attr(base64_encode((string) time())); ?>">

        <div class="pn-grid-2">
            <div class="pn-form-group">
                <label class="pn-form-label" for="pn-first-name">
                    <?php esc_html_e('First Name', 'podnest'); ?>
                    <span aria-hidden="true" class="pn-required-text"> *</span>
                </label>
                <input type="text"
                    id="pn-first-name"
                    name="first_name"
                    class="pn-form-input"
                    required
                    autocomplete="given-name"
                    placeholder="<?php esc_attr_e('Jane', 'podnest'); ?>">
            </div>
            <div class="pn-form-group">
                <label class="pn-form-label" for="pn-last-name">
                    <?php esc_html_e('Last Name', 'podnest'); ?>
                    <span aria-hidden="true" class="pn-required-text"> *</span>
                </label>
                <input type="text"
                    id="pn-last-name"
                    name="last_name"
                    class="pn-form-input"
                    required
                    autocomplete="family-name"
                    placeholder="<?php esc_attr_e('Smith', 'podnest'); ?>">
            </div>
        </div>

        <div class="pn-grid-2">
            <div class="pn-form-group">
                <label class="pn-form-label" for="pn-email">
                    <?php esc_html_e('Email Address', 'podnest'); ?>
                    <span aria-hidden="true" class="pn-required-text"> *</span>
                </label>
                <input type="email"
                    id="pn-email"
                    name="email"
                    class="pn-form-input"
                    required
                    autocomplete="email"
                    placeholder="<?php esc_attr_e('jane@example.com', 'podnest'); ?>">
            </div>
            <div class="pn-form-group">
                <label class="pn-form-label" for="pn-phone">
                    <?php esc_html_e('Phone', 'podnest'); ?>
                </label>
                <input type="tel"
                    id="pn-phone"
                    name="phone"
                    class="pn-form-input"
                    autocomplete="tel"
                    placeholder="<?php esc_attr_e('+1 (555) 000-0000', 'podnest'); ?>">
            </div>
        </div>

        <div class="pn-form-group">
            <label class="pn-form-label" for="pn-subject"><?php esc_html_e('Subject', 'podnest'); ?></label>
            <input type="text"
                id="pn-subject"
                name="subject"
                class="pn-form-input"
                placeholder="<?php esc_attr_e('How can we help?', 'podnest'); ?>">
        </div>

        <div class="pn-form-group">
            <label class="pn-form-label" for="pn-message">
                <?php esc_html_e('Message', 'podnest'); ?>
                <span aria-hidden="true" class="pn-required-text"> *</span>
            </label>
            <textarea id="pn-message"
                name="message"
                class="pn-form-textarea"
                required
                placeholder="<?php esc_attr_e('Tell us about your project or question…', 'podnest'); ?>"></textarea>
        </div>

        <div id="pn-contact-error" class="pn-contact-error" role="alert" hidden></div>

        <div class="pn-contact-buttons">
            <button type="submit" id="pn-contact-submit" class="pn-btn-primary">
                <span class="pn-btn-label"><?php esc_html_e('Send Message', 'podnest'); ?></span>
                <span class="pn-btn-spinner" aria-hidden="true" hidden>⟳</span>
            </button>

            <?php if (get_theme_mod('podnest_recaptcha_site_key', '')) : ?>
                <p class="pn-muted" style="font-size:0.75rem;margin:0;">
                    <?php esc_html_e('Protected by reCAPTCHA.', 'podnest'); ?>
                </p>
            <?php endif; ?>
        </div>

    </form>
</div>