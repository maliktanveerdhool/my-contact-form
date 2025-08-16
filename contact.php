<?php
/**
 * Plugin Name: MTD
 * Description: Simple contact form plugin with GitHub-driven auto-updates.
 * Version: mtd 2
 * Author: Tanveer
 * Author URI: https://yourwebsite.com
 * Text Domain: my-contact-form
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
if (!defined('MYCF_PLUGIN_FILE')) {
    define('MYCF_PLUGIN_FILE', __FILE__);
}
if (!defined('MYCF_PLUGIN_DIR')) {
    define('MYCF_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('MYCF_PLUGIN_BASENAME')) {
    define('MYCF_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

// 1) Prefer Composer autoload if available (recommended: commit vendor/ for live hosting environments like Hostinger)
$composerAutoload = MYCF_PLUGIN_DIR . 'vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// 2) Fallback to bundled Plugin Update Checker library if present at plugin-update-checker/
$bundledPuc = MYCF_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';
if (!class_exists('YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory') && file_exists($bundledPuc)) {
    require_once $bundledPuc;
}

// Bootstrap Plugin Update Checker if available.
if (class_exists('YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory')) {
    /** @var \YahnisElsts\PluginUpdateChecker\v5\Plugin\UpdateChecker $mycf_update_checker */
    $mycf_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        // Public repo URL
        'https://github.com/maliktanveerdhool/my-contact-form',
        MYCF_PLUGIN_FILE,
        'my-contact-form'
    );

    // If you define GITHUB_ACCESS_TOKEN in wp-config.php, it will be used automatically.
    if (defined('GITHUB_ACCESS_TOKEN') && GITHUB_ACCESS_TOKEN) {
        $mycf_update_checker->setAuthentication(GITHUB_ACCESS_TOKEN);
    }

    // Prefer tagged releases (v1.0.1, etc.) and GitHub release assets for download URLs.
    $api = $mycf_update_checker->getVcsApi();
    if ($api) {
        $api->enableReleaseAssets();
    }
}

// Load plugin core.
require_once MYCF_PLUGIN_DIR . 'inc/class-plugin.php';
require_once MYCF_PLUGIN_DIR . 'inc/class-settings.php';
require_once MYCF_PLUGIN_DIR . 'inc/class-cpt.php';
require_once MYCF_PLUGIN_DIR . 'inc/class-form.php';

// Initialize plugin.
MyContactForm\Plugin::init();

// Activation/Deactivation hooks
register_activation_hook(MYCF_PLUGIN_FILE, ['MyContactForm\Plugin', 'activate']);
register_deactivation_hook(MYCF_PLUGIN_FILE, ['MyContactForm\Plugin', 'deactivate']);