<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Response code for product unavailable case.
 */
final class RedirectionPage implements RedirectionPageInterface
{
    /**
     * @var int
     */
    private $responseCode;

    /**
     * @param int $responseCode
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $responseCode)
    {
        $this->responseCode = new ResponseCode($responseCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseCode(): ResponseCode
    {
        return $this->responseCode;
    }
}
