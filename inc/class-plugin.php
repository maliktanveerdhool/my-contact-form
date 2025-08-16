<?php
namespace MyContactForm;

if (!defined('ABSPATH')) { exit; }

class Plugin {
    public static function init(): void {
        // Register hooks
        add_action('init', [__CLASS__, 'register_post_types']);

        // Admin settings page
        add_action('admin_menu', [Settings::class, 'register_menu']);
        add_action('admin_init', [Settings::class, 'register_settings']);

        // Shortcode
        add_action('init', [Form::class, 'register_shortcode']);

        // Form submission handlers (front + logged-in)
        add_action('admin_post_nopriv_mycf_submit', [Form::class, 'handle_submit']);
        add_action('admin_post_mycf_submit', [Form::class, 'handle_submit']);
    }

    public static function activate(): void {
        // Ensure CPT exists then flush rewrite rules
        self::register_post_types();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    public static function register_post_types(): void {
        CPT::register_submission_cpt();
    }
}
