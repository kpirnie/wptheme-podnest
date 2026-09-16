<?php

/**
 * Customizer Controls
 *
 * Registers all theme settings and their Customizer UI controls in
 * dedicated sections. Settings are organised into four sections:
 *
 *  - Hero          : Badge text, headline, description, CTAs.
 *  - Pricing       : Price amounts and contact URL.
 *  - Social Links  : GitHub, Discord, Twitter/X, Facebook URLs.
 *  - Footer        : Tagline text.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Customizer
 *
 * Self-registers hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_Customizer
{

    // -- Constructor -----------------------------------------------

    /**
     * Registers the customize_register hook.
     */
    public function __construct()
    {
        add_action('customize_register', [$this, 'register']);
    }

    // -- Registration entry point ----------------------------------

    /**
     * Adds all PodNest sections, settings, and controls to the Customizer.
     *
     * @param  WP_Customize_Manager $wp_customize WordPress Customizer manager instance.
     * @return void
     */
    public function register(WP_Customize_Manager $wp_customize): void
    {
        $this->hero_section($wp_customize);
        $this->pricing_section($wp_customize);
        $this->social_section($wp_customize);
        $this->footer_section($wp_customize);
        $this->contact_section($wp_customize);
    }

    // -- Section builders ------------------------------------------

    /**
     * Registers the Hero Section controls.
     *
     * @param  WP_Customize_Manager $c Customizer manager.
     * @return void
     */
    private function hero_section(WP_Customize_Manager $c): void
    {
        $c->add_section('podnest_hero', [
            'title'    => __('Hero Section', 'podnest'),
            'priority' => 30,
        ]);

        $this->add_text($c, 'hero_badge_text',        __('Badge text', 'podnest'),         'podnest_hero', 'Open Source · MIT License');
        $this->add_text($c, 'hero_title_line1',       __('Title line 1', 'podnest'),        'podnest_hero', 'Secure. Manage.');
        $this->add_text($c, 'hero_title_line2',       __('Title line 2 (gradient)', 'podnest'), 'podnest_hero', 'Deploy.');
        $this->add_textarea(
            $c,
            'hero_description',
            __('Description', 'podnest'),
            'podnest_hero',
            'PodNest provisions and manages isolated, production-hardened website pods using Podman — no shell required after initial setup.'
        );
        $this->add_text($c, 'hero_cta_primary',       __('Primary CTA text', 'podnest'),    'podnest_hero', 'Get Started Free');
        $this->add_url($c, 'hero_cta_primary_url',   __('Primary CTA URL', 'podnest'),     'podnest_hero', 'https://github.com/kpirnie/podnest');
        $this->add_text($c, 'hero_cta_secondary',     __('Secondary CTA text', 'podnest'),  'podnest_hero', 'View Features');
        $this->add_url($c, 'hero_cta_secondary_url', __('Secondary CTA URL', 'podnest'),   'podnest_hero', '#features');
    }

    /**
     * Registers the Pricing Section controls.
     *
     * These values seed the front-page pricing section defaults when no
     * Pricing CPT posts exist, and provide the contact page URL.
     *
     * @param  WP_Customize_Manager $c Customizer manager.
     * @return void
     */
    private function pricing_section(WP_Customize_Manager $c): void
    {
        $c->add_section('podnest_pricing_defaults', [
            'title'    => __('Pricing Defaults', 'podnest'),
            'priority' => 40,
        ]);

        $this->add_url(
            $c,
            'support_contact_url',
            __('Contact page URL', 'podnest'),
            'podnest_pricing_defaults',
            'https://kevinpirnie.com/about-kevin-pirnie/lets-talk/'
        );
    }

    /**
     * Registers Social Link URL controls.
     *
     * @param  WP_Customize_Manager $c Customizer manager.
     * @return void
     */
    private function social_section(WP_Customize_Manager $c): void
    {
        $c->add_section('podnest_social', [
            'title'    => __('Social Links', 'podnest'),
            'priority' => 45,
        ]);

        $this->add_url($c, 'social_github',   __('GitHub URL', 'podnest'),    'podnest_social', 'https://github.com/kpirnie/podnest');
        $this->add_url($c, 'social_discord',  __('Discord URL', 'podnest'),   'podnest_social', '');
        $this->add_url($c, 'social_twitter',  __('X / Twitter URL', 'podnest'), 'podnest_social', '');
        $this->add_url($c, 'social_facebook', __('Facebook URL', 'podnest'),  'podnest_social', '');
    }

    /**
     * Registers Footer controls.
     *
     * @param  WP_Customize_Manager $c Customizer manager.
     * @return void
     */
    private function footer_section(WP_Customize_Manager $c): void
    {
        $c->add_section('podnest_footer', [
            'title'    => __('Footer', 'podnest'),
            'priority' => 50,
        ]);

        $this->add_text(
            $c,
            'footer_tagline',
            __('Tagline under logo', 'podnest'),
            'podnest_footer',
            'Hardened. Automated. Production-Ready.'
        );
    }

    // -- DRY helpers -----------------------------------------------

    /**
     * Adds a text setting + control pair to the Customizer.
     *
     * @param  WP_Customize_Manager $c       Customizer manager.
     * @param  string               $key     Mod key without 'podnest_' prefix.
     * @param  string               $label   Human-readable control label.
     * @param  string               $section Section ID this control belongs to.
     * @param  string               $default Default value.
     * @return void
     */
    private function add_text(
        WP_Customize_Manager $c,
        string $key,
        string $label,
        string $section,
        string $default = ''
    ): void {
        $c->add_setting('podnest_' . $key, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $c->add_control('podnest_' . $key, [
            'label'   => $label,
            'section' => $section,
            'type'    => 'text',
        ]);
    }

    /**
     * Adds a textarea setting + control pair to the Customizer.
     *
     * @param  WP_Customize_Manager $c       Customizer manager.
     * @param  string               $key     Mod key without 'podnest_' prefix.
     * @param  string               $label   Human-readable control label.
     * @param  string               $section Section ID this control belongs to.
     * @param  string               $default Default value.
     * @return void
     */
    private function add_textarea(
        WP_Customize_Manager $c,
        string $key,
        string $label,
        string $section,
        string $default = ''
    ): void {
        $c->add_setting('podnest_' . $key, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_textarea_field',
        ]);
        $c->add_control('podnest_' . $key, [
            'label'   => $label,
            'section' => $section,
            'type'    => 'textarea',
        ]);
    }

    /**
     * Adds a URL setting + control pair to the Customizer.
     *
     * @param  WP_Customize_Manager $c       Customizer manager.
     * @param  string               $key     Mod key without 'podnest_' prefix.
     * @param  string               $label   Human-readable control label.
     * @param  string               $section Section ID this control belongs to.
     * @param  string               $default Default URL.
     * @return void
     */
    private function add_url(
        WP_Customize_Manager $c,
        string $key,
        string $label,
        string $section,
        string $default = ''
    ): void {
        $c->add_setting('podnest_' . $key, [
            'default'           => $default,
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $c->add_control('podnest_' . $key, [
            'label'   => $label,
            'section' => $section,
            'type'    => 'url',
        ]);
    }

    // Add this method alongside the other section methods:
    private function contact_section(WP_Customize_Manager $c): void
    {
        $c->add_section('podnest_contact_form', [
            'title'    => __('Contact Form', 'podnest'),
            'priority' => 48,
        ]);

        $this->add_text($c, 'recaptcha_site_key',   __('reCAPTCHA v3 Site Key',   'podnest'), 'podnest_contact_form');
        $this->add_text($c, 'recaptcha_secret_key', __('reCAPTCHA v3 Secret Key', 'podnest'), 'podnest_contact_form');
    }
}
