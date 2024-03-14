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

namespace Tests\Unit\Adapter\Product\Options;

use Generator;
use Language;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryPreviewRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Options\RedirectTargetProvider;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductPreviewRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryPreview;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPreview;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

class RedirectTargetProviderTest extends TestCase
{
    private const CATEGORY_TARGET_ID = 42;
    private const PRODUCT_TARGET_ID = 51;
    private const LANGUAGE_ID = 69;

    /**
     * @dataProvider getRedirectTargetData
     *
     * @param array|null $mockOptions
     * @param string $redirectType
     * @param int $redirectTargetId
     * @param RedirectTargetInformation|null $expectedTarget
     */
    public function testGetRedirectTarget(?array $mockOptions, string $redirectType, int $redirectTargetId, ?RedirectTargetInformation $expectedTarget): void
    {
        $provider = new RedirectTargetProvider(
            $this->getProductPreviewRepositoryMock($mockOptions),
            $this->getCategoryPreviewRepository($mockOptions),
            $this->getLegacyContext($mockOptions)
        );

        $redirectTarget = $provider->getRedirectTarget($redirectType, $redirectTargetId);
        $this->assertEquals($expectedTarget, $redirectTarget);
    }

    public function getRedirectTargetData(): Generator
    {
        $breadcrumb = 'Category > Path';
        $categoryImage = 'path/to/c/1.jpg';
        $redirectTarget = new RedirectTargetInformation(
            self::CATEGORY_TARGET_ID,
            RedirectTargetInformation::CATEGORY_TYPE,
            $breadcrumb,
            $categoryImage
        );

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'category_id' => self::CATEGORY_TARGET_ID,
                'breadcrumb' => $breadcrumb,
                'category_image' => $categoryImage,
            ],
            RedirectType::TYPE_CATEGORY_PERMANENT,
            self::CATEGORY_TARGET_ID,
            $redirectTarget,
        ];

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'category_id' => self::CATEGORY_TARGET_ID,
                'breadcrumb' => $breadcrumb,
                'category_image' => $categoryImage,
            ],
            RedirectType::TYPE_CATEGORY_TEMPORARY,
            self::CATEGORY_TARGET_ID,
            $redirectTarget,
        ];

        $productName = 'Product 1';
        $productImage = '/path/p/45.jpg';
        $redirectTarget = new RedirectTargetInformation(
            self::PRODUCT_TARGET_ID,
            RedirectTargetInformation::PRODUCT_TYPE,
            $productName,
            $productImage
        );

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'product_id' => self::PRODUCT_TARGET_ID,
                'product_name' => $productName,
                'product_image' => $productImage,
            ],
            RedirectType::TYPE_PRODUCT_PERMANENT,
            self::PRODUCT_TARGET_ID,
            $redirectTarget,
        ];

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'product_id' => self::PRODUCT_TARGET_ID,
                'product_name' => $productName,
                'product_image' => $productImage,
            ],
            RedirectType::TYPE_PRODUCT_TEMPORARY,
            self::PRODUCT_TARGET_ID,
            $redirectTarget,
        ];

        yield [
            [],
            RedirectType::TYPE_CATEGORY_TEMPORARY,
            0,
            null,
        ];

        yield [
            [],
            RedirectType::TYPE_CATEGORY_PERMANENT,
            0,
            null,
        ];

        yield [
            [],
            RedirectType::TYPE_NOT_FOUND,
            0,
            null,
        ];
    }

    private function getLegacyContext(?array $mockOptions = null): LegacyContext
    {
        $legacyContext = $this
            ->getMockBuilder(LegacyContext::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $languageId = $mockOptions['language_id'] ?? null;
        if ($languageId) {
            $language = $this
                ->getMockBuilder(Language::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $language->id = $languageId;

            $legacyContext
                ->expects($this->once())
                ->method('getLanguage')
                ->willReturn($language)
            ;
        } else {
            $legacyContext
                ->expects($this->never())
                ->method('getLanguage')
            ;
        }

        return $legacyContext;
    }

    private function getCategoryPreviewRepository(?array $mockOptions = null): CategoryPreviewRepository
    {
        $categoryPreviewRepository = $this
            ->getMockBuilder(CategoryPreviewRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $languageId = $mockOptions['language_id'] ?? null;
        $categoryId = $mockOptions['category_id'] ?? null;
        $breadcrumb = $mockOptions['breadcrumb'] ?? '';
        $categoryImage = $mockOptions['category_image'] ?? null;
        if ($languageId && $categoryId) {
            $categoryPreviewRepository
                ->expects($this->once())
                ->method('getPreview')
                ->with(
                    $this->callback(function ($categoryParam) use ($categoryId) {
                        $this->assertInstanceOf(CategoryId::class, $categoryParam);
                        $this->assertEquals($categoryId, $categoryParam->getValue());

                        return true;
                    }),
                    $this->callback(function ($languageParam) use ($languageId) {
                        $this->assertInstanceOf(LanguageId::class, $languageParam);
                        $this->assertEquals($languageId, $languageParam->getValue());

                        return true;
                    })
                )
                ->willReturn(new CategoryPreview(
                    $categoryId,
                    $breadcrumb,
                    $breadcrumb,
                    $categoryImage
                ))
            ;
        } else {
            $categoryPreviewRepository
                ->expects($this->never())
                ->method('getPreview')
            ;
        }

        return $categoryPreviewRepository;
    }

    private function getProductPreviewRepositoryMock(?array $mockOptions = null): ProductPreviewRepository
    {
        $productPreviewRepository = $this
            ->getMockBuilder(ProductPreviewRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $productId = $mockOptions['product_id'] ?? null;
        $languageId = $mockOptions['language_id'] ?? null;
        $productName = $mockOptions['product_name'] ?? null;
        $productImage = $mockOptions['product_image'] ?? null;
        if ($productId) {
            $productPreviewRepository
                ->expects($this->once())
                ->method('getPreview')
                ->with(
                    $this->callback(function ($productIdVO) use ($productId) {
                        $this->assertInstanceOf(ProductId::class, $productIdVO);
                        $this->assertEquals($productId, $productIdVO->getValue());

                        return true;
                    }),
                    $this->callback(function ($languageIdVO) use ($languageId) {
                        $this->assertInstanceOf(LanguageId::class, $languageIdVO);
                        $this->assertEquals($languageId, $languageIdVO->getValue());

                        return true;
                    })
                )
                ->willReturn(new ProductPreview(
                    $productId,
                    $productName,
                    $productImage
                ));
        } else {
            $productPreviewRepository
                ->expects($this->never())
                ->method('getPreview')
            ;
        }

        return $productPreviewRepository;
    }
}
