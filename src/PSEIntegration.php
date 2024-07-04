<?php

namespace PSEIntegration;

use \PSEIntegration\services\ApigeeServices;
use \PSEIntegration\models\GetBankListRequest;
use \PSEIntegration\models\CreateTransactionPaymentRequest;
use \PSEIntegration\models\CreateTransactionPaymentResponse;
use \PSEIntegration\models\FinalizeTransactionPaymentRequest;
use \PSEIntegration\models\FinalizeTransactionPaymentResponse;
use \PSEIntegration\models\TransactionInformationRequest;
use \PSEIntegration\models\TransactionInformationResponse;

class PSEIntegration
{
    private $services;

    public function __construct(string $clientId, string $clientSecret, string $organizationProdUrl, string $encryptIV, string $encryptKey, string $apigeeDirectoryUrl)
    {
        $this->services = new ApigeeServices($clientId, $clientSecret, $organizationProdUrl, $encryptIV, $encryptKey, $apigeeDirectoryUrl);
    }

    public function setTimeout(int $timeout)
    {
        $this->services->apigeeDefaultTimeout = $timeout;
    }

    public function setCertificateIgnoreInvalid(bool $certificateIgnoreInvalid)
    {
        $this->services->certificateIgnoreInvalid = $certificateIgnoreInvalid;
    }

    public function setMutualTLSCertificate(string $certificateFile, string $certificatePassword)
    {
        $this->services->certificateFile = $certificateFile;
        $this->services->certificatePassword = $certificatePassword;
    }

    public function getBankList(GetBankListRequest $request)
    {
        return $this->services->getBankList($request);
    }

    public function createTransactionPayment(CreateTransactionPaymentRequest $request)
    {
        return $this->services->createTransactionPayment($request);
    }

    public function finalizeTransactionPayment(FinalizeTransactionPaymentRequest $request)
    {
        return $this->services->finalizeTransactionPayment($request);
    }

    public function getTransactionInformation(TransactionInformationRequest $request)
    {
        return $this->services->getTransactionInformation($request);
    }
}
