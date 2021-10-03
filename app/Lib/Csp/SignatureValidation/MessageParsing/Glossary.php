<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\MessageParsing;


/**
 * Инкапсулирует логику проверок строк
 */
final class Glossary
{

    public function signatureSuccess(string $haystack): bool
    {
        return str_icontains($haystack, "Signature's verified.");
    }


    public function signatureError(string $haystack): bool
    {
        return str_icontains_any($haystack, [
            'This certificate or one of the certificates in the certificate chain is not time valid.',
            'Error: Invalid algorithm specified.',
            'Trust for this certificate or one of the certificates in the certificate chain has been revoked.',
            'Error: Invalid Signature.'
        ]);
    }


    public function errorCode(string $haystack): bool
    {
        return str_icontains($haystack, 'ErrorCode:');
    }


    public function errorDescription(string $haystack): bool
    {
        return str_icontains($haystack, 'Error:');
    }


    public function commonErrorAboutInvalidValidatedFile(string $haystack): bool
    {
        return str_icontains_any($haystack, [
            'Error: Invalid cryptographic message type.',
            'Error: The parameter is incorrect.',
            'Error: Incorrect BASE64 conversion.'
        ]);
    }

}
