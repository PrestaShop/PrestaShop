<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject\CustomizableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\FriendlyUrl;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaTitle;

class FriendlyUrlTest extends TestCase
{
    public function testItDetectsThatNameIsTooLong(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::FRIENDLY_URL_TOO_LONG);

        $tooLongName = str_repeat('a', FriendlyUrl::MAX_SIZE + 1);

        new FriendlyUrl($tooLongName);
    }
}
