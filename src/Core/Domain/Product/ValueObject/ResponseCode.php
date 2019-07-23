<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response code for product unavailable case.
 */
class ResponseCode
{
    public const AVAILABLE_RESPONSE_CODES = [
        Response::HTTP_MOVED_PERMANENTLY,
        Response::HTTP_FOUND,
        Response::HTTP_NOT_FOUND,
    ];

    public function __construct(int $responseCode)
    {
        if (!in_array($responseCode, self::AVAILABLE_RESPONSE_CODES, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid response code for redirection type %d given. Available codes are "%s"',
                    $responseCode,
                    implode(',', self::AVAILABLE_RESPONSE_CODES)
                ),
                ProductConstraintException::INVALID_RESPONSE_CODE
            );
        }

        $this->responseCode = $responseCode;
    }

    public function getValue(): int
    {
        return $this->responseCode;
    }
}
