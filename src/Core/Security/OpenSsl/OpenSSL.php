<?php

namespace PrestaShop\PrestaShop\Core\Security\OpenSsl;

use function openssl_random_pseudo_bytes;
use RuntimeException;

/**
 * Wrapper around the openssl_random_pseudo_bytes function so it can be tested.
 */
class OpenSSL implements OpenSSLInterface
{
    public function getBytes(int $length): string
    {
        // Try catch needed here because it can not work on some systems
        // @see https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
        try {
            return openssl_random_pseudo_bytes($length);
        } catch (\Throwable $e) {
            throw new RuntimeException('OpenSSL is not supported');
        }
    }
}
