<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Redirection page which displays certain response code.
 */
interface RedirectionPageInterface
{
    /**
     * @return ResponseCode
     */
    public function getResponseCode(): ResponseCode;
}
