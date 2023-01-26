<?php

namespace PrestaShop\PrestaShop\Core\Security\OpenSsl;

interface OpenSSLInterface
{
    public function getBytes(int $length): string;
}
