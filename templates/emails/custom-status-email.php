<?php
if (!defined('ABSPATH')) exit;

echo '<p>' . sprintf(__('Hello %s,', 'custom-order-status-lite'), $order->get_billing_first_name()) . '</p>';
echo '<p>' . __('Your order status has been updated to:', 'custom-order-status-lite') . ' <strong>' . __('Custom Status', 'custom-order-status-lite') . '</strong></p>';
echo '<p>' . __('Thank you for shopping with us!', 'custom-order-status-lite') . '</p>';
