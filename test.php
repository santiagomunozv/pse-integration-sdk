<?php

require __DIR__ . '/vendor/autoload.php';

use \PSEIntegration\PSEIntegration;
use \PSEIntegration\Models\GetBankListRequest;
use \PSEIntegration\Models\CreateTransactionPaymentRequest;
use \PSEIntegration\Models\CreateTransactionPaymentResponse;
use \PSEIntegration\Models\FinalizeTransactionPaymentRequest;
use \PSEIntegration\Models\FinalizeTransactionPaymentResponse;
use \PSEIntegration\Models\TransactionInformationRequest;
use \PSEIntegration\Models\TransactionInformationResponse;

/*
# Apigee data
apigeeOrganizationProdUrl = "apiprb.pse.com.co"
apigeeDirectoryUrl = "v2"
apiKey = "MPAQLCHPYNIGofBqqC4aRB78n7l0xYRv"
apiSecret = "wYQiAXl8D3w3M1zo"
encryptKey = "RD5KLZX86TD6MMVE348Z4Y8MPFLM4NYV"
encryptIV = "T0JFKG5TF5KTLKHU"

# Store data
entityCode = "0123456789"
serviceCode = "6101"
*/ 

// Apigee data unisys environment
// $apigeeOrganizationProdUrl = "https://fredyvanegas-eval-prod.apigee.net";
// $apigeeDirectoryUrl = "";
// $apiKey = "ReIgfAUglbuJCf0GkDFSZoZPRmKadv0i";
// $apiSecret = "cGv9VYuzKf3zxC8J";
// $encryptKey = "RGY44EL6X06TC3SUEUJ1WNTI1C726N6U";
// $encryptIV = "4IMWZFK30Q6OUQ7G";

// // Store data
// $entityCode = "10102020";
// $serviceCode = "1010";

// Apigee data
$apigeeOrganizationProdUrl = "https://apiprb.pse.com.co";
$apigeeDirectoryUrl = "v2";
$apiKey = "MPAQLCHPYNIGofBqqC4aRB78n7l0xYRv";
$apiSecret = "wYQiAXl8D3w3M1zo";
$encryptKey = "RD5KLZX86TD6MMVE348Z4Y8MPFLM4NYV";
$encryptIV = "T0JFKG5TF5KTLKHU";

// Store data
$entityCode = "0123456789";
$serviceCode = "6101";
$vat = 0.15;
$entityurl = "https://localhost/DemoStore/return?id=";

echo '=====================================================' . PHP_EOL;
echo 'Starting tests...' . PHP_EOL;
echo '=====================================================' . PHP_EOL . PHP_EOL;

$sdk = new PSEIntegration($apiKey, $apiSecret, $apigeeOrganizationProdUrl, $encryptIV, $encryptKey, $apigeeDirectoryUrl);
$sdk->setCertificateIgnoreInvalid(true);
$sdk->setMutualTLSCertificate("..\..\KeyStores\certificate-php.pem", "pioco1");

$getBankListRequest = new GetBankListRequest($entityCode);

var_dump($sdk->getBankList($getBankListRequest));

echo PHP_EOL . '-------------------' . PHP_EOL . PHP_EOL;

$createTransactionPaymentRequest = new CreateTransactionPaymentRequest(
    $entityCode,
    "1010",
    $serviceCode,
    100.00,
    $vat,
    1234,
    $entityurl . "1234",
    "natural",
    "ReferenceNumber1",
    "ReferenceNumber2",
    "ReferenceNumber3",
    "2020-09-30T19:44:54.092Z",
    "PaymentDescription",
    "RegistroCivilDeNacimiento",
    "11111",
    "John Doe",
    "1232131231",
    "Street",
    "user@domain.com",
    "RegistroCivilDeNacimiento",
    "11111"
);

$createTransactionPaymentResponse = $sdk->createTransactionPayment($createTransactionPaymentRequest);

var_dump($createTransactionPaymentResponse);

echo PHP_EOL . '-------------------' . PHP_EOL . PHP_EOL;

$transactionInformationRequest = new TransactionInformationRequest($entityCode, $createTransactionPaymentResponse->trazabilityCode);

$transactionInformationResponse = $sdk->getTransactionInformation($transactionInformationRequest);
var_dump($transactionInformationResponse);

echo PHP_EOL . '-------------------' . PHP_EOL . PHP_EOL;

$finalizeTransactionPaymentRequest = new FinalizeTransactionPaymentRequest($entityCode, $createTransactionPaymentResponse->trazabilityCode, "11111");

$finalizeTransactionPaymentResponse = $sdk->finalizeTransactionPayment($finalizeTransactionPaymentRequest);
var_dump($finalizeTransactionPaymentResponse);

echo PHP_EOL . '====> Tests finished' . PHP_EOL . PHP_EOL;