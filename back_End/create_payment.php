<?php
require 'paypal_config.php';

use PayPal\Checkout\Orders\OrdersCreateRequest;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount']; // Amount entered by the user

    try {
        $client = PayPalConfig::getClient();
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => $amount
                ]
            ]],
            "application_context" => [
                "return_url" => "http://localhost/Web_project/back_End/payment_success.php",
                "cancel_url" => "http://localhost/Web_project/back_End/payment_cancel.php"
            ]
        ];

        $response = $client->execute($request);
        foreach ($response->result->links as $link) {
            if ($link->rel === 'approve') {
                // Redirect the user to PayPal approval page
                header("Location: " . $link->href);
                exit();
            }
        }
    } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
    }
}
?>
