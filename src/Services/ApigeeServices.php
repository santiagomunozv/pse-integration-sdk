<?php

namespace PSEIntegration\Services;

use PSEIntegration\Exceptions\UnauthorizedException;
use \PSEIntegration\Services\RequestServices;
use \PSEIntegration\Services\JWEServices;
use \PSEIntegration\Models\GetBankListRequest;
use \PSEIntegration\Models\CreateTransactionPaymentRequest;
use \PSEIntegration\Models\CreateTransactionPaymentResponse;
use \PSEIntegration\Models\FinalizeTransactionPaymentRequest;
use \PSEIntegration\Models\FinalizeTransactionPaymentResponse;
use \PSEIntegration\Models\TransactionInformationRequest;
use \PSEIntegration\Models\TransactionInformationResponse;

use \PSEIntegration\models\Bank;

class ApigeeServices
{
    private $apigeeClientId;

    private $apigeeClientSecret;

    private $apigeeOrganizationProdUrl;

    private $apigeeDirectoryUrl;

    private $apigeeEncryptKey;

    private $apigeeEncryptIV;

    private static $apigeeToken;

    private static $apigeeLoginAttemps = 0;

    public $apigeeDefaultTimeout = 30000;

    public $certificateFile = "";

    public $certificatePassword = "";

    public $certificateIgnoreInvalid = false;

    private $auth;

    public function __construct(string $apigeeClientId, string $apigeeClientSecret, string $apigeeOrganizationProdUrl, string $apigeeEncryptIV, string $apigeeEncryptKey, string $apigeeDirectoryUrl)
    {
        $this->apigeeClientId = $apigeeClientId;
        $this->apigeeClientSecret = $apigeeClientSecret;
        $this->apigeeOrganizationProdUrl = $apigeeOrganizationProdUrl;
        $this->apigeeDirectoryUrl = $apigeeDirectoryUrl;
        $this->apigeeEncryptIV = $apigeeEncryptIV;
        $this->apigeeEncryptKey = $apigeeEncryptKey;
    }

    public function setApigeeDefaultTimeout(int $apigeeDefaultTimeout)
    {
        $this->apigeeDefaultTimeout = $apigeeDefaultTimeout;
    }

    public function setCertificateFile(string $certificateFile)
    {
        $this->certificateFile = $certificateFile;
    }

    public function setCertificatePassword(string $certificatePassword)
    {
        $this->certificatePassword = $certificatePassword;
    }

    public function setCertificateIgnoreInvalid(bool $certificateIgnoreInvalid)
    {
        $this->certificateIgnoreInvalid = $certificateIgnoreInvalid;
    }

    public function login($forceLogin = false)
    {
        if (ApigeeServices::$apigeeToken != null && !$forceLogin) {
            return;
        }

        $path = "oauth/client_credential/accesstoken?grant_type=client_credentials";

        $form = [
            "grant_type" => "client_credentials",
            "client_id" => $this->apigeeClientId,
            "client_secret" => $this->apigeeClientSecret
        ];

        $response = RequestServices::doPostFormAPICall($this->apigeeDefaultTimeout, $this->apigeeOrganizationProdUrl, $path, $form, "", $this->certificateFile, $this->certificatePassword, $this->certificateIgnoreInvalid);
        $response = json_decode($response);

        ApigeeServices::$apigeeToken = $response->access_token;
    }

    private function post(string $method, string $content)
    {
        $subDirectorio = ($this->apigeeDirectoryUrl == "") ? "" : $this->apigeeDirectoryUrl . "/";
        $path = $subDirectorio . "psewebapinf/api/" . $method . "?apikey=" . $this->apigeeClientId;
        $this->auth = "Bearer " . ApigeeServices::$apigeeToken;

        try {
            return RequestServices::doPostAPICall($this->apigeeDefaultTimeout, $this->apigeeOrganizationProdUrl, $path, $content, $this->auth, $this->certificateFile, $this->certificatePassword, $this->certificateIgnoreInvalid);
        } catch (UnauthorizedException $e) {
            // Try login
            ApigeeServices::$apigeeLoginAttemps++;

            if (ApigeeServices::$apigeeLoginAttemps <= 3) {
                login(true);

                // Try again
                return post($method, $content);
            } else {
                throw e;
            }
        } catch (Exception $e) {
            throw e;
        }
    }


    private function sendRequest(string $method, Object $message, $type)
    {
        // Create JWE with AES
        $jwe = JWEServices::processEncrypt(json_encode($message), $this->apigeeEncryptKey, $this->apigeeEncryptIV);

        // Make request
        $responsestring = $this->post($method, $jwe);

        // Decrypt and verify JWE
        $responseJWE = JWEServices::processDencrypt($responsestring, $this->apigeeEncryptKey, $this->apigeeEncryptIV);

        $mapper = new \JsonMapper();
        $mapper->bStrictNullTypes = false;
        if (gettype($type) == 'string') {
            return $mapper->mapArray(
                json_decode($responseJWE),
                new \ArrayObject,
                $type
            );
        } else {
            return $mapper->map(json_decode($responseJWE), $type);
        }
    }

    public function getBankList(GetBankListRequest $request)
    {
        $this->login();
        return $this->sendRequest("GetBankListNF", $request, "\PSEIntegration\Models\Bank");
    }

    public function createTransactionPayment(CreateTransactionPaymentRequest $request)
    {
        $this->login();
        return $this->sendRequest("CreateTransactionPaymentNF", $request, new CreateTransactionPaymentResponse());
    }

    public function finalizeTransactionPayment(FinalizeTransactionPaymentRequest $request)
    {
        $this->login();
        return $this->sendRequest("FinalizeTransactionPaymentNF", $request, new FinalizeTransactionPaymentResponse());
    }

    public function getTransactionInformation(TransactionInformationRequest $request)
    {
        $this->login();
        return $this->sendRequest("GetTransactionInformationNF", $request, new TransactionInformationResponse());
    }
}
