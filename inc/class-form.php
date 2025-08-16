<?php
namespace MyContactForm;

if (!defined('ABSPATH')) { exit; }

class Form {
    const NONCE_ACTION = 'mycf_submit_nonce_action';
    const NONCE_FIELD = 'mycf_nonce';

    public static function register_shortcode(): void {
        add_shortcode('my_contact_form', [__CLASS__, 'render_shortcode']);
    }

    public static function render_shortcode($atts = []): string {
        $atts = shortcode_atts([], $atts, 'my_contact_form');

        $success = isset($_GET['mycf']) && $_GET['mycf'] === 'success';
        $error = isset($_GET['mycf']) && $_GET['mycf'] === 'error';

        ob_start();
        if ($success) {
            echo '<div class="notice notice-success" style="padding:10px;margin-bottom:10px;background:#ecf7ed;border-left:4px solid #46b450;">' . wp_kses_post(self::get_success_message()) . '</div>';
        } elseif ($error) {
            $msg = get_transient('mycf_last_error');
            delete_transient('mycf_last_error');
            if (!$msg) { $msg = __('There was a problem sending your message. Please try again.', 'my-contact-form'); }
            echo '<div class="notice notice-error" style="padding:10px;margin-bottom:10px;background:#fbeaea;border-left:4px solid #dc3232;">' . esc_html($msg) . '</div>';
        }

        $action = esc_url(admin_url('admin-post.php'));
        $nonce = wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD, true, false);

        ?>
        <form method="post" action="<?php echo $action; ?>" class="mycf-form">
            <p>
                <label for="mycf_name"><?php echo esc_html__('Name', 'my-contact-form'); ?></label><br />
                <input type="text" id="mycf_name" name="mycf_name" required />
            </p>
            <p>
                <label for="mycf_email"><?php echo esc_html__('Email', 'my-contact-form'); ?></label><br />
                <input type="email" id="mycf_email" name="mycf_email" required />
            </p>
            <p>
                <label for="mycf_message"><?php echo esc_html__('Message', 'my-contact-form'); ?></label><br />
                <textarea id="mycf_message" name="mycf_message" rows="6" required></textarea>
            </p>
            <input type="hidden" name="action" value="mycf_submit" />
            <?php echo $nonce; ?>
            <p>
                <button type="submit"><?php echo esc_html__('Send', 'my-contact-form'); ?></button>
            </p>
        </form>
        <?php
        return (string)ob_get_clean();
    }

    public static function handle_submit(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::redirect_with('error', __('Invalid request method.', 'my-contact-form'));
        }

        if (!isset($_POST[self::NONCE_FIELD]) || !wp_verify_nonce($_POST[self::NONCE_FIELD], self::NONCE_ACTION)) {
            self::redirect_with('error', __('Security check failed. Please try again.', 'my-contact-form'));
        }

        $name = isset($_POST['mycf_name']) ? sanitize_text_field(wp_unslash($_POST['mycf_name'])) : '';
        $email = isset($_POST['mycf_email']) ? sanitize_email(wp_unslash($_POST['mycf_email'])) : '';
        $message = isset($_POST['mycf_message']) ? wp_kses_post(wp_unslash($_POST['mycf_message'])) : '';

        if (empty($name) || empty($email) || empty($message) || !is_email($email)) {
            self::redirect_with('error', __('Please fill in all fields with a valid email.', 'my-contact-form'));
        }

        // Store as CPT
        $post_id = wp_insert_post([
            'post_type' => CPT::POST_TYPE,
            'post_status' => 'publish',
            'post_title' => sprintf(__('%s — %s', 'my-contact-form'), $name, current_time('mysql')),
            'post_content' => $message,
            'meta_input' => [
                'mycf_name' => $name,
                'mycf_email' => $email,
            ],
        ], true);

        if (is_wp_error($post_id)) {
            self::redirect_with('error', __('Could not save your message. Please try again later.', 'my-contact-form'));
        }

        // Send email
        $to = get_option(Settings::OPTION_NAME_EMAIL, get_option('admin_email'));
        $subject = sprintf(__('New contact message from %s', 'my-contact-form'), $name);
        $body = sprintf("Name: %s\nEmail: %s\n\nMessage:\n%s", $name, $email, wp_strip_all_tags($message));
        $headers = [ 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' ];
        wp_mail($to, $subject, $body, $headers);

        self::redirect_with('success');
    }

    private static function redirect_with(string $status, string $error_message = ''): void {
        $url = wp_get_referer();
        if (!$url) { $url = home_url('/'); }
        $url = remove_query_arg('mycf', $url);
        $url = add_query_arg('mycf', $status, $url);
        if ($status === 'error' && $error_message) {
            set_transient('mycf_last_error', $error_message, 60);
        }
        wp_safe_redirect($url);
        exit;
    }

    private static function get_success_message(): string {
        $msg = get_option(Settings::OPTION_NAME_SUCCESS, __('Thanks! Your message has been sent.', 'my-contact-form'));
        return (string)$msg;
    }
}
