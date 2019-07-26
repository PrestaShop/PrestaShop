<?php

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject\CustomizableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaKeywords;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaTitle;

class MetaKeywordsTest extends TestCase
{
    public function testItDetectsThatNameIsTooLong(): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::META_KEYWORDS_NAME_TOO_LONG);

        $tooLongName = str_repeat('a', MetaKeywords::MAX_SIZE + 1);

        new MetaKeywords($tooLongName);
    }

    /**
     * @dataProvider provideInvalidNames
     */
    public function testItDetectsInvalidName(string $invalidName): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_META_KEYWORDS);

        new MetaKeywords($invalidName);
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
