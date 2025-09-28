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



require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../payriff.php';

function writeLog($message) {
    file_put_contents(__DIR__ . "/callback_debug.log", date("Y-m-d H:i:s") . " - " . $message . "\n", FILE_APPEND);
}

writeLog("Callback file is running...");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    writeLog("Raw POST Input: " . $rawData);

    if (empty($rawData)) {
        writeLog("Empty POST Body");
        exit("Empty Callback Response");
    }

    $data = json_decode($rawData, true);
    if (!isset($data['payload'])) {
        writeLog("Missing payload in POST");
        exit("Missing payload");
    }

    $metadataRaw = $data['payload']['metadata'] ?? '';
    writeLog("Raw Metadata: " . $metadataRaw);
    preg_match('/invoice_id\s*=\s*(\d+)/', $metadataRaw, $matches);
    $invoiceId = $matches[1] ?? null;

    if (!$invoiceId) {
        writeLog("Invoice ID Not Found in Metadata");
        exit("Invoice ID Not Found");
    }

    $paymentStatus = $data['payload']['paymentStatus'] ?? '';
    $transactionId = $data['payload']['transactions'][0]['uuid'] ?? uniqid('payriff_');
    $amount = $data['payload']['amount'] ?? 0;

    if (strtoupper($paymentStatus) === 'APPROVED') {
        writeLog("Payment APPROVED, marking invoice as paid...");

        $postData = [
            'invoiceid' => $invoiceId,
            'transactionid' => $transactionId,
            'amount' => $amount,
            'gateway' => 'payriff'
        ];

        $adminUsername = 'səninadminpanelistifadəçiadın'; // <- Burada WHMCS admin panel-də istifadə olunan istifadəçi adını daxil et

        try {
            $results = localAPI('AddInvoicePayment', $postData, $adminUsername);
            writeLog("AddInvoicePayment result: " . json_encode($results));
            exit("OK");
        } catch (Throwable $e) {
            writeLog("localAPI Error: " . $e->getMessage());
            exit("localAPI Error");
        }
    } else {
        writeLog("Payment not approved. Status: " . $paymentStatus);
        exit("Payment not approved");
    }
}


$invoiceId = $_GET['invoice_id'] ?? null;

if ($invoiceId) {
    writeLog("GET request with invoice_id={$invoiceId}, redirecting to invoice page...");
    echo "<script>window.location.href = '/viewinvoice.php?id={$invoiceId}';</script>";
} else {
    writeLog("GET request without invoice_id, redirecting to client area...");
    echo "<script>window.location.href = '/clientarea.php?action=invoices';</script>";
}
exit;
