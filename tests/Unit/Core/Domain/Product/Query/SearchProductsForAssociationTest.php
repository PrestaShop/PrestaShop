<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Query;

use PHPUnit\Framework\TestCase;
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
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            1,
        ];

        yield [
            'pretty mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
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
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            'u',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            '',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            null,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            0,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_LIMIT,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            self::SHOP_ID,
            -1,
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SEARCH_LIMIT,
        ];

        yield [
            'mug',
            0,
            self::SHOP_ID,
            null,
            LanguageException::class,
            0,
        ];

        yield [
            'mug',
            self::LANGUAGE_ID,
            0,
            null,
            ShopException::class,
            0,
        ];
    }
}
