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

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchCombinationsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use Throwable;
use TypeError;

class SearchCombinationsForAssociationTest extends TestCase
{
    private const LANGUAGE_ID = 42;
    private const SHOP_ID = 51;

    /**
     * @dataProvider getValidParameters
     *
     * @param string $phrase
     * @param int $languageId
     * @param int $shopId
     * @param array $filters
     * @param int|null $limit
     *
     * @throws ProductConstraintException
     * @throws ShopException
     */
    public function testValidQuery(string $phrase, int $languageId, int $shopId, array $filters, ?int $limit): void
    {
        $query = new SearchCombinationsForAssociation($phrase, $languageId, $shopId, $filters, $limit);
        $this->assertNotNull($query);
        $this->assertEquals($phrase, $query->getPhrase());
        $this->assertEquals($languageId, $query->getLanguageId()->getValue());
        $this->assertEquals($shopId, $query->getShopId()->getValue());
        $this->assertEquals($filters, $filters);
        $this->assertEquals($limit, $query->getLimit());
    }

    public function getValidParameters(): iterable
    {
        yield 'mug_nofilter_nolimit' => [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            [],
            null,
        ];

        yield 'mug_packfilter_nolimit' => [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            [ProductType::TYPE_PACK],
            null,
        ];

        yield 'mug_nofilter_limit1' => [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            [],
            1,
        ];

        yield 'prettymug_nofilter_limit1' => [
            'pretty mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            [],
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
     * @param array $filters
     * @param string $exceptionClass
     * @param int $errorCode
     */
    public function testInvalidQuery(string $phrase, int $languageId, int $shopId, ?int $limit, ?array $filters, string $exceptionClass, int $errorCode): void
    {
        $caughtException = null;
        try {
            new SearchCombinationsForAssociation($phrase, $languageId, $shopId, $filters, $limit);
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
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
            [ProductType::TYPE_PACK],
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            'u',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
            [],
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            '',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
            [],
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            0,
            [],
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_LIMIT,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            -1,
            [],
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_LIMIT,
        ];

        yield [
            'mug',
            0,
            self::SHOP_ID,
            null,
            [],
            LanguageException::class,
            0,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            0,
            null,
            [],
            ShopException::class,
            0,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            0,
            null,
            null,
            TypeError::class,
            0,
        ];
    }
}
