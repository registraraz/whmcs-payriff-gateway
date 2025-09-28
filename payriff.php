<?php
/**
 * Payriff Payment Gateway Module for WHMCS
 *
 * Bu modul WHMCS üçün Payriff ödəniş sistemini inteqrasiya edir.
 *
 * License: MIT
 * Author: Community contributors
 * Version: 1.0.0
 */



if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function payriff_MetaData() {
    return [
        'DisplayName' => 'Payriff Payment Gateway',
        'APIVersion' => '1.0',
    ];
}

function payriff_config() {
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'Payriff'
        ],
        'merchant_id' => [
            'FriendlyName' => 'Merchant ID',
            'Type' => 'text',
            'Size' => '30'
        ],
        'secret_key' => [
            'FriendlyName' => 'Secret Key',
            'Type' => 'text',
            'Size' => '50'
        ]
    ];
}

function payriff_link($params) {
    
    $secret_key = $params['secret_key'];

$invoice_id = $params['invoiceid'];
$system_url = rtrim($params['systemurl'], '/');
$amount = $params['amount'];
$currency = $params['currency'];
$description = "Payment for Invoice #$invoice_id";
$callback_url = $system_url . "/modules/gateways/callback/payriff.php";
$return_url   = $system_url . "/viewinvoice.php?id=" . $invoice_id;
$cancel_url   = $system_url . "/viewinvoice.php?id=" . $invoice_id;


$data = [
    "amount" => (float)$amount,
    "language" => "EN",
    "currency" => $params['currency'],
    "description" => $description,
    "callbackUrl" => $callback_url,
    "returnUrl" => $return_url,
    "cancelUrl" => $cancel_url,
    "cardSave" => false,
    "operation" => "PURCHASE",
    "metadata" => [ "invoice_id" => (string) $invoice_id ]
];

file_put_contents(__DIR__ . '/order_debug.log', "Callback URL: $callback_url\nReturn URL: $return_url\nCancel URL: $cancel_url\n", FILE_APPEND);



    $json_data = json_encode($data, JSON_UNESCAPED_SLASHES);

    $ch = curl_init("https://api.payriff.com/api/v3/orders");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: " . $secret_key
    ]);

    $response = curl_exec($ch);
    file_put_contents(__DIR__ . '/order_debug.log', "Order Response: " . $response . "\n", FILE_APPEND);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);




if ($httpCode == 200 && isset($decoded['payload']['paymentUrl'])) {

    
    $locale = isset($_SESSION['Language']) ? $_SESSION['Language'] : 'english';
    $buttonText = ($locale == 'azerbaijani') ? 'İndi ödəyin' : 'Pay Now';


    return '<a href="' . $decoded['payload']['paymentUrl'] . '" class="btn btn-primary">' . $buttonText . '</a>';
 }
 else {
        return '<div style="color:red;">Error: Unable to create payment link.<br>HTTP Code: ' . $httpCode . '<br>Response: ' . htmlspecialchars($response) . '</div>';
    }
}