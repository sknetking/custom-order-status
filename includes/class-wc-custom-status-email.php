<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WC_Custom_Status_Email extends WC_Email {

    public function __construct() {
        $this->id             = 'wc_custom_status_email';
        $this->title          = __('Custom Status Email', 'custom-order-status-lite');
        $this->description    = __('This email is sent when an order reaches the custom status.', 'custom-order-status-lite');
        $this->heading        = __('Your Order Status Has Changed', 'custom-order-status-lite');
        $this->subject        = __('Order #{order_number} Status Changed', 'custom-order-status-lite');

        $this->template_html  = 'emails/custom-status-email.php';
        $this->template_plain = 'emails/plain/custom-status-email.php';

        add_action('woocommerce_order_status_wc-custom-status', array($this, 'trigger'), 10, 2);

        parent::__construct();

        //$this->template_base = WC()->template_path();
		$this->template_base = plugin_dir_path(__FILE__) . '../templates/';

    }

    public function trigger($order_id, $order = false) {
        if (!$order_id) return;

        $this->object = wc_get_order($order_id);
        $this->recipient = $this->object->get_billing_email();

        if (!$this->is_enabled() || !$this->get_recipient()) return;

        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    public function get_content_html() {
        ob_start();
        wc_get_template($this->template_html, array(
            'order' => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text' => false,
            'email' => $this,
        ), '', plugin_dir_path(__FILE__) . 'templates/');
        return ob_get_clean();
    }

    public function get_content_plain() {
        ob_start();
        wc_get_template($this->template_plain, array(
            'order' => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text' => true,
            'email' => $this,
        ), '', plugin_dir_path(__FILE__) . 'templates/');
        return ob_get_clean();
    }
}