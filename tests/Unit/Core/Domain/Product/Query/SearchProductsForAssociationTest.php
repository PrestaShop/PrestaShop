<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Query;

use PHPStan\Testing\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use Throwable;

class SearchProductsForAssociationTest extends TestCase
{
    private const LANGUAGE_ID = 42;
    private const SHOP_ID = 51;

    /**
     * @dataProvider getValidParameters
     *
     * @param string $phrase
     * @param int $languageId
     * @param int $shopId
     * @param int|null $limit
     */
    public function testValidQuery(string $phrase, int $languageId, int $shopId, ?int $limit): void
    {
        $query = new SearchProductsForAssociation($phrase, $languageId, $shopId, $limit);
        $this->assertNotNull($query);
        $this->assertEquals($phrase, $query->getPhrase());
        $this->assertEquals($languageId, $query->getLanguageId()->getValue());
        $this->assertEquals($shopId, $query->getShopId()->getValue());
        $this->assertEquals($limit, $query->getLimit());
    }

    public function getValidParameters(): iterable
    {
        yield [
            'mug',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            null,
        ];

        yield [
            'mug',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            1,
        ];

        yield [
            'pretty mug',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            1,
        ];
    }

    /**
     * @dataProvider getInvalidParameters
     *
     * @param string $phrase
     * @param int $languageId
     * @param int $shopId
     * @param int|null $limit
     * @param int $errorCode
     */
    public function testInvalidQuery(string $phrase, int $languageId, int $shopId, ?int $limit, string $exceptionClass, int $errorCode): void
    {
        $caughtException = null;
        try {
            new SearchProductsForAssociation($phrase, $languageId, $shopId, $limit);
        } catch (Throwable $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf($exceptionClass, $caughtException);
        $this->assertEquals($errorCode, $caughtException->getCode());
    }

    public function getInvalidParameters(): iterable
    {
        yield [
            'mu',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            null,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            'u',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            null,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            '',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            null,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            'mug',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            0,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_LIMIT,
        ];

        yield [
            'mug',
            static::LANGUAGE_ID,
            static::SHOP_ID,
            -1,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_LIMIT,
        ];

        yield [
            'mug',
            0,
            static::SHOP_ID,
            null,
            LanguageException::class,
            0,
        ];

        yield [
            'mug',
            static::LANGUAGE_ID,
            0,
            null,
            ShopException::class,
            0,
        ];
    }
}
