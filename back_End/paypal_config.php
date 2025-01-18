<?php
require __DIR__ . '/../vendor/autoload.php'; // Ensure this path points to Composer's autoload

use PayPal\Utils\Environment;
use PayPal\Utils\PaypalServerSdkClientBuilder;

class PayPalConfig
{
    private static $clientId = 'YOUR_CLIENT_ID';
    private static $clientSecret = 'YOUR_CLIENT_SECRET';

    public static function getClient()
    {
        // Instantiate the environment (adjust based on the Environment class implementation)
        $environment = new Environment(self::$clientId, self::$clientSecret, "sandbox");

        // Create a new PayPal Client
        return PaypalServerSdkClientBuilder::create($environment)->build();
    }
}
