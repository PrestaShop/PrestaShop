<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject\CustomizableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaDescription;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaKeywords;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaTitle;

class MetaDescriptionTest extends TestCase
{
    public function testItDetectsThatNameIsTooLong(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::META_DESCRIPTION_NAME_TOO_LONG);

        $tooLongName = str_repeat('a', MetaDescription::MAX_SIZE + 1);

        new MetaDescription($tooLongName);
    }

    /**
     * @dataProvider provideInvalidNames
     */
    public function testItDetectsInvalidName(string $invalidName): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_META_DESCRIPTION);

        new MetaDescription($invalidName);
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
