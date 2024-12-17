<?php
/**
 * Plugin Name: Custom Order Status Lite
 * Description: Adds a custom WooCommerce order status with email notifications and a settings page.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: custom-order-status-lite
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// 1. Initialize Plugin
function cosl_init() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . __('WooCommerce is required for Custom Order Status Lite plugin.', 'custom-order-status-lite') . '</p></div>';
        });
        return;
    }
    new Custom_Order_Status_Lite();
}
add_action('plugins_loaded', 'cosl_init');

// 2. Main Plugin Class
class Custom_Order_Status_Lite {

    private $status_slug = 'wc-custom-status';
    private $option_key = 'cosl_options';

    public function __construct() {
        // Register Custom Order Status
        add_action('init', array($this, 'register_custom_status'));

        // Add to WooCommerce Order Status List
        add_filter('wc_order_statuses', array($this, 'add_custom_status'));

        // Add Settings Page
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_plugin_settings'));

        // Trigger Email Notification
        //add_action('woocommerce_order_status_' . $this->status_slug, array($this, 'send_custom_email'), 10, 2);
    }

    // Register Custom Status
    public function register_custom_status() {
        register_post_status($this->status_slug, array(
            'label'                     => $this->get_status_name(),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop($this->get_status_name() . ' <span class="count">(%s)</span>', $this->get_status_name() . ' <span class="count">(%s)</span>'),
        ));
    }

    // Add to Order Status List
    public function add_custom_status($statuses) {
        $statuses[$this->status_slug] = $this->get_status_name();
        return $statuses;
    }

    // Get Status Name from Options
    private function get_status_name() {
        $options = get_option($this->option_key, array());
        return !empty($options['status_name']) ? esc_html($options['status_name']) : __('Custom Status', 'custom-order-status-lite');
    }

    // Add Admin Settings Page
    public function register_settings_page() {
        add_submenu_page('woocommerce', __('Custom Status Settings', 'custom-order-status-lite'), __('Custom Status', 'custom-order-status-lite'), 'manage_options', 'custom-status-lite', array($this, 'settings_page_html'));
    }

    // Settings Page HTML
    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php _e('Custom Order Status Settings', 'custom-order-status-lite'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->option_key);
                do_settings_sections($this->option_key);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Register Settings
    public function register_plugin_settings() {
        register_setting($this->option_key, $this->option_key);

        add_settings_section('general_settings', __('General Settings', 'custom-order-status-lite'), null, $this->option_key);

        add_settings_field('status_name', __('Custom Status Name', 'custom-order-status-lite'), function() {
            $options = get_option($this->option_key);
            $value = isset($options['status_name']) ? esc_attr($options['status_name']) : '';
            echo '<input type="text" name="cosl_options[status_name]" value="' . $value . '" placeholder="Custom Status">';
        }, $this->option_key, 'general_settings');

        add_settings_field('enable_email', __('Enable Email Notification', 'custom-order-status-lite'), function() {
            $options = get_option($this->option_key);
            $checked = isset($options['enable_email']) ? checked($options['enable_email'], 1, false) : '';
            echo '<input type="checkbox" name="cosl_options[enable_email]" value="1" ' . $checked . '> ' . __('Send email when order reaches this status.', 'custom-order-status-lite');
        }, $this->option_key, 'general_settings');
    }

    // Send Email Notification
//     public function send_custom_email($order_id, $order) {
// 		// Register Custom Email Class

//         $options = get_option($this->option_key);
//         if (empty($options['enable_email'])) return;

//         $mailer = WC()->mailer()->get_emails();
//         $subject = __('Your Order Status Has Changed', 'custom-order-status-lite');
//         $message = __('Your order status has been updated to: ' . $this->get_status_name(), 'custom-order-status-lite');

//         // Send the email
//         wp_mail($order->get_billing_email(), $subject, $message);
//     }
	
	
}

function cosl_register_custom_email_class($email_classes) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-custom-status-email.php';
    $email_classes['WC_Custom_Status_Email'] = new WC_Custom_Status_Email();
    return $email_classes;
}
add_filter('woocommerce_email_classes', 'cosl_register_custom_email_class');


