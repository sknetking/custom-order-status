<?php
if (!defined('ABSPATH')) exit;

echo sprintf(__('Hello %s,', 'custom-order-status-lite'), $order->get_billing_first_name()) . "\n";
echo __('Your order status has been updated to: Custom Status', 'custom-order-status-lite') . "\n";
echo __('Thank you for shopping with us!', 'custom-order-status-lite');
