<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\ResponseCode;
use Symfony\Component\HttpFoundation\Response;

class ResponseCodeTest extends TestCase
{
    public function testItDetectsInvalidResponseCodeProvided(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_RESPONSE_CODE);

        new ResponseCode(Response::HTTP_OK);
    }
}
