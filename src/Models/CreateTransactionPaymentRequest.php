<?php

namespace PSEIntegration\Models;

class CreateTransactionPaymentRequest
{
    public $entityCode;

    public $financialInstitutionCode;

    public $serviceCode;

    public $transactionValue;

    public $vatValue;

    public $ticketId;

    public $entityurl;

    public $userType;

    public $referenceNumber1;

    public $referenceNumber2;

    public $referenceNumber3;

    public $soliciteDate;

    public $paymentDescription;

    public $identificationType;

    public $identificationNumber;

    public $fullName;

    public $cellphoneNumber;

    public $address;

    public $email;

    public $beneficiaryEntityIdentificationType;

    public $beneficiaryEntityIdentification;

    public $beneficiaryEntityName;

    public $beneficiaryEntityCIIUCategory;

    public $beneficiaryIdentificationType;

    public $beneficiaryIdentification;

    public $indicator4per1000;

    public function __construct(
        string $entityCode,
        string $financialInstitutionCode,
        string $serviceCode,
        float $transactionValue,
        float $vatValue,
        int $ticketId,
        string $entityurl,
        string $userType,
        string $referenceNumber1,
        string $referenceNumber2,
        string $referenceNumber3,
        string $soliciteDate,
        string $paymentDescription,
        string $identificationType,
        string $identificationNumber,
        string $fullName,
        string $cellphoneNumber,
        string $address,
        string $email,
        string $beneficiaryIdentificationType,
        string $beneficiaryIdentification,
        string $beneficiaryEntityIdentificationType,
        string $beneficiaryEntityIdentification,
        string $beneficiaryEntityName,
        string $beneficiaryEntityCIIUCategory
    ) {
        $this->entityCode = $entityCode;
        $this->financialInstitutionCode = $financialInstitutionCode;
        $this->serviceCode = $serviceCode;
        $this->transactionValue = $transactionValue;
        $this->vatValue = $vatValue;
        $this->ticketId = $ticketId;
        $this->entityurl = $entityurl;
        $this->userType = $userType;
        $this->referenceNumber1 = $referenceNumber1;
        $this->referenceNumber2 = $referenceNumber2;
        $this->referenceNumber3 = $referenceNumber3;
        $this->soliciteDate = $soliciteDate;
        $this->paymentDescription = $paymentDescription;
        $this->identificationType = $identificationType;
        $this->identificationNumber = $identificationNumber;
        $this->fullName = $fullName;
        $this->cellphoneNumber = $cellphoneNumber;
        $this->address = $address;
        $this->email = $email;
        $this->beneficiaryIdentificationType = $beneficiaryIdentificationType;
        $this->beneficiaryIdentification = $beneficiaryIdentification;
        $this->beneficiaryEntityIdentificationType = $beneficiaryEntityIdentificationType;
        $this->beneficiaryEntityIdentification = $beneficiaryEntityIdentification;
        $this->beneficiaryEntityName = $beneficiaryEntityName;
        $this->beneficiaryEntityCIIUCategory = $beneficiaryEntityCIIUCategory;
    }
}
