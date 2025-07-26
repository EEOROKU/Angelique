<?php

require_once '../vendor/autoload.php';
require_once '../secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');

// Stripe domain for success and cancel URLs
$YOUR_DOMAIN = 'http://localhost:4242';

// Read JSON input from JavaScript
$input = file_get_contents('php://input');
$cart = json_decode($input, true);

// Validate the cart data
if (!is_array($cart) || empty($cart)) {
    error_log('Invalid or empty cart data received');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or empty cart data.']);
    exit;
}

// Prepare line_items for Stripe Checkout
$line_items = [];
foreach ($cart as $item) {
    if (
        isset($item['product_id'], $item['quantity']) &&
        !empty($item['product_id']) &&
        is_numeric($item['quantity']) &&
        $item['quantity'] > 0
    ) {
        $line_items[] = [
            'price' => $item['product_id'], // Stripe Price ID
            'quantity' => $item['quantity'],
        ];
    } else {
        error_log('Invalid item in cart: ' . print_r($item, true));
    }
}

// Check if line_items is still valid
if (empty($line_items)) {
    error_log('No valid items found in the cart');
    http_response_code(400);
    echo json_encode(['error' => 'No valid items in the cart.']);
    exit;
}

// Create a Stripe Checkout session
try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.html',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        'automatic_tax' => ['enabled' => true],
    ]);

    // Send the Checkout session URL to the frontend
    echo json_encode(['url' => $checkout_session->url]);
} catch (Exception $e) {
    error_log('Stripe Checkout session creation failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create Stripe Checkout session.']);
    exit;
}
