<?php
require 'paypal_config.php';

use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];

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
            "return_url" => "http://localhost/Web_project/back_End/donation_success.php",
            "cancel_url" => "http://localhost/Web_project/back_End/donation_cancel.php"
        ]
    ];

    try {
        $response = $client->execute($request);
        foreach ($response->result->links as $link) {
            if ($link->rel === 'approve') {
                header("Location: " . $link->href);
                exit();
            }
        }
    } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
    }
}
?>
