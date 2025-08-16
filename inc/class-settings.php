<?php
namespace MyContactForm;

if (!defined('ABSPATH')) { exit; }

class Settings {
    const OPTION_GROUP = 'mycf_options_group';
    const OPTION_NAME_EMAIL = 'mycf_recipient_email';
    const OPTION_NAME_SUCCESS = 'mycf_success_message';

    public static function register_menu(): void {
        add_options_page(
            __('My Contact Form', 'my-contact-form'),
            __('My Contact Form', 'my-contact-form'),
            'manage_options',
            'mycf-settings',
            [__CLASS__, 'render_page']
        );
    }

    public static function register_settings(): void {
        register_setting(self::OPTION_GROUP, self::OPTION_NAME_EMAIL, [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default' => get_option('admin_email'),
        ]);
        register_setting(self::OPTION_GROUP, self::OPTION_NAME_SUCCESS, [
            'type' => 'string',
            'sanitize_callback' => 'wp_kses_post',
            'default' => __('Thanks! Your message has been sent.', 'my-contact-form'),
        ]);

        add_settings_section('mycf_main', __('General', 'my-contact-form'), function () {
            echo '<p>' . esc_html__('Configure where messages are emailed and the success notice.', 'my-contact-form') . '</p>';
        }, 'mycf-settings');

        add_settings_field(
            self::OPTION_NAME_EMAIL,
            __('Recipient Email', 'my-contact-form'),
            function () {
                $val = esc_attr(get_option(self::OPTION_NAME_EMAIL, get_option('admin_email')));
                echo '<input type="email" name="' . esc_attr(self::OPTION_NAME_EMAIL) . '" value="' . $val . '" class="regular-text" />';
            },
            'mycf-settings',
            'mycf_main'
        );

        add_settings_field(
            self::OPTION_NAME_SUCCESS,
            __('Success Message', 'my-contact-form'),
            function () {
                $val = esc_textarea(get_option(self::OPTION_NAME_SUCCESS, __('Thanks! Your message has been sent.', 'my-contact-form')));
                echo '<textarea name="' . esc_attr(self::OPTION_NAME_SUCCESS) . '" rows="3" class="large-text">' . $val . '</textarea>';
            },
            'mycf-settings',
            'mycf_main'
        );
    }

    public static function render_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('My Contact Form Settings', 'my-contact-form') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields(self::OPTION_GROUP);
        do_settings_sections('mycf-settings');
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}
