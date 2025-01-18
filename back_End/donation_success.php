<?php
require 'paypal_config.php';

use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

if (isset($_GET['token'])) {
    $client = PayPalConfig::getClient();
    $token = $_GET['token'];

    $request = new OrdersCaptureRequest($token);

    try {
        $response = $client->execute($request);

        $transactionId = $response->result->id;
        $status = $response->result->status;
        $payerEmail = $response->result->payer->email_address;
        $amount = $response->result->purchase_units[0]->amount->value;

        // Save transaction details to the database
        // Replace this with your database logic
        echo "Payment successful! Transaction ID: $transactionId, Amount: $amount, Payer Email: $payerEmail, Status: $status";
    } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
    }
}
?>
