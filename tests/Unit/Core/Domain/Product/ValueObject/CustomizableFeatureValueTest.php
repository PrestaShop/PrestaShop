<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\CustomizableFeatureValue;

class CustomizableFeatureValueTest extends TestCase
{
    public function testItDetectsThatNameIsTooLong(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::CUSTOMIZABLE_FEATURE_VALUE_TOO_LONG);

        $tooLongName = str_repeat('a', CustomizableFeatureValue::MAX_SIZE + 1);

        new CustomizableFeatureValue(1, $tooLongName);
    }

    /**
     * @dataProvider provideInvalidNames
     */
    public function testItDetectsInvalidName(string $invalidName): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_CUSTOMIZABLE_FEATURE_VALUE);

        new CustomizableFeatureValue(1, $invalidName);
    }

    public function provideInvalidNames(): ?\Generator
    {
        yield [
            '<something>',
        ];

        yield [
            '{object}',
        ];

        yield [
            '=hashtag=',
        ];
    }
}
