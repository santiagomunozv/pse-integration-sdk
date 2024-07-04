<?php

namespace PSEIntegration\services;

use Sop\JWX\JWE\EncryptionAlgorithm\A256GCMAlgorithm;
use Sop\JWX\JWE\KeyAlgorithm\RSAESKeyAlgorithm;
use Sop\JWX\JWE\KeyAlgorithm\DirectCEKAlgorithm;
use Sop\JWX\JWE\KeyAlgorithm\PBES2HS256A128KWAlgorithm;
use Sop\JWX\JWE\JWE;
use Sop\JWX\JWK\Symmetric\SymmetricKeyJWK;
use Sop\JWX\JWT\Header\Header;
use Sop\JWX\JWT\Parameter\AlgorithmParameter;
use Sop\JWX\JWT\Parameter\JWTParameter;
use Sop\JWX\JWA\JWA;
use Sop\JWX\JWT\Parameter\InitializationVectorParameter;
use Sop\JWX\JWE\KeyAlgorithm\AESGCMKWAlgorithm;
use Sop\JWX\JWE\KeyAlgorithm\KeyAlgorithmFactory;
use Sop\JWX\JWE\KeyManagementAlgorithm;
use Sop\JWX\JWK\JWKSet;

class JWEServices
{
    private const GCM_TAG_LENGTH = 16; // GCM tag length in bytes

    public static function processEncrypt(string $message, string $key, string $customerIV)
    {
        $encString = JWEServices::encrypt($message, $key, $customerIV);
        $jwe = JWEServices::generateTokenJWE($encString, $key);
        return $jwe;
    }

    public static function processDencrypt(string $message, string $key, string $customerIV)
    {
        $string = JWEServices::stringfyTokenJWE($message, $key);
        $stringFinal = JWEServices::decrypt($string, $key, $customerIV);
        return $stringFinal;
    }

    public static function stringfyTokenJWE(string $textoencriptado, string $chaveencriptacao)
    {
        $jwk = SymmetricKeyJWK::fromKey($chaveencriptacao);
        $jwe = JWE::fromCompact($textoencriptado);
        $payload = $jwe->decryptWithJWK($jwk);
        return $payload;
    }

    public static function generateTokenJWE(string $message, string $chaveencriptacao)
    {
        $jwk = SymmetricKeyJWK::fromKey($chaveencriptacao);
        $header = new Header(new AlgorithmParameter(JWA::ALGO_DIR));
        $key_algo = DirectCEKAlgorithm::fromJWK($jwk, $header);
        $enc_algo = new A256GCMAlgorithm();
        $jwe = JWE::encrypt($message, $key_algo, $enc_algo);
        return $jwe->toCompact();
    }

    public static function encrypt($plaintext, $key, $iv)
    {
        $sRet = "";
        $cipher = "aes-256-gcm";
        $tag = "";
        $encrypted = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "", self::GCM_TAG_LENGTH);
        $combined = $iv . $encrypted . $tag;
        $sRet = base64_encode($combined);
        return $sRet;
    }

    public static function decrypt($ivHashCiphertext, $password, $iv)
    {
        $sRet = "";
        $combined = base64_decode($ivHashCiphertext);
        $Iv = substr($combined, 0, 16);
        $encrypted = substr($combined, 16, -self::GCM_TAG_LENGTH);
        $tag = substr($combined, -self::GCM_TAG_LENGTH);
        $decrypted = openssl_decrypt($encrypted, "aes-256-gcm", $password, OPENSSL_RAW_DATA, $Iv, $tag);
        $sRet = $decrypted;
        return $sRet;
    }
}
