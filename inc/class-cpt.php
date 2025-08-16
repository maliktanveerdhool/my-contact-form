<?php
namespace MyContactForm;

if (!defined('ABSPATH')) { exit; }

class CPT {
    const POST_TYPE = 'mycf_submission';

    public static function register_submission_cpt(): void {
        $labels = [
            'name' => __('Contact Submissions', 'my-contact-form'),
            'singular_name' => __('Contact Submission', 'my-contact-form'),
            'menu_name' => __('Contact Submissions', 'my-contact-form'),
            'add_new' => __('Add New', 'my-contact-form'),
            'add_new_item' => __('Add New Submission', 'my-contact-form'),
            'edit_item' => __('Edit Submission', 'my-contact-form'),
            'new_item' => __('New Submission', 'my-contact-form'),
            'view_item' => __('View Submission', 'my-contact-form'),
            'search_items' => __('Search Submissions', 'my-contact-form'),
            'not_found' => __('No submissions found', 'my-contact-form'),
            'not_found_in_trash' => __('No submissions found in Trash', 'my-contact-form'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'options-general.php',
            'supports' => ['title', 'editor', 'custom-fields'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'manage_options',
            ],
            'map_meta_cap' => true,
        ]);
    }
}
