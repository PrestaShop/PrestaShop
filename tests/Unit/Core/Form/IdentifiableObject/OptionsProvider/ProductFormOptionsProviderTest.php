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

namespace Tests\Unit\Core\Form\IdentifiableObject\OptionsProvider;

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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\ProductFormOptionsProvider;

class ProductFormOptionsProviderTest extends TestCase
{
    private const PRODUCT_ID = 42;
    private const VIRTUAL_PRODUCT_FILE_ID = 51;
    private const CATEGORY_TARGET_ID = 49;
    private const PRODUCT_TARGET_ID = 69;
    private const LANGUAGE_ID = 33;

    public function testGetDefaultOptions(): void
    {
        $provider = new ProductFormOptionsProvider($this->getRedirectTargetProvider());
        $defaultOptions = $provider->getDefaultOptions([]);
        $this->assertEquals([], $defaultOptions);
    }

    public function testVirtualProductOptions(): void
    {
        $provider = new ProductFormOptionsProvider($this->getRedirectTargetProvider());
        $options = $provider->getOptions(self::PRODUCT_ID, []);
        $this->assertArrayHasKey('virtual_product_file_id', $options);
        $this->assertEquals(null, $options['virtual_product_file_id']);

        $options = $provider->getOptions(self::PRODUCT_ID, [
            'stock' => [
                'virtual_product_file' => [
                    'virtual_product_file_id' => self::VIRTUAL_PRODUCT_FILE_ID,
                ],
            ],
        ]);
        $this->assertArrayHasKey('virtual_product_file_id', $options);
        $this->assertEquals(self::VIRTUAL_PRODUCT_FILE_ID, $options['virtual_product_file_id']);
    }

    /**
     * @dataProvider getRedirectData
     *
     * @param array|null $mockOptions
     * @param array|null $formData
     */
    public function testRedirectionOptions(?array $mockOptions, array $formData, ?array $expectedTarget): void
    {
        $provider = new ProductFormOptionsProvider($this->getRedirectTargetProvider($mockOptions));

        $options = $provider->getOptions(self::PRODUCT_ID, $formData);
        $this->assertArrayHasKey('redirect_target', $options);
        $this->assertEquals($expectedTarget, $options['redirect_target']);
    }

    public function getRedirectData(): Generator
    {
        $formData = [
            'seo' => [
                'redirect_option' => [
                    'target' => null,
                    'type' => RedirectType::TYPE_NOT_FOUND,
                ],
            ],
        ];

        yield [
            null,
            $formData,
            null,
        ];

        $breadcrumb = 'Category > Path';
        $categoryImage = 'path/to/c/1.jpg';
        $formData['seo']['redirect_option']['target'] = [
            'id' => self::CATEGORY_TARGET_ID,
        ];
        $formData['seo']['redirect_option']['type'] = RedirectType::TYPE_CATEGORY_PERMANENT;

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'category_id' => self::CATEGORY_TARGET_ID,
                'breadcrumb' => $breadcrumb,
                'category_image' => $categoryImage,
            ],
            $formData,
            [
                [
                    'id' => self::CATEGORY_TARGET_ID,
                    'name' => $breadcrumb,
                    'image' => $categoryImage,
                ],
            ],
        ];

        $formData['seo']['redirect_option']['type'] = RedirectType::TYPE_CATEGORY_TEMPORARY;

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'category_id' => self::CATEGORY_TARGET_ID,
                'breadcrumb' => $breadcrumb,
                'category_image' => $categoryImage,
            ],
            $formData,
            [
                [
                    'id' => self::CATEGORY_TARGET_ID,
                    'name' => $breadcrumb,
                    'image' => $categoryImage,
                ],
            ],
        ];

        $formData['seo']['redirect_option']['target'] = [
            'id' => self::PRODUCT_TARGET_ID,
        ];
        $formData['seo']['redirect_option']['type'] = RedirectType::TYPE_PRODUCT_PERMANENT;
        $productName = 'Product 1';
        $productImage = '/path/p/45.jpg';

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'product_id' => self::PRODUCT_TARGET_ID,
                'product_name' => $productName,
                'product_image' => $productImage,
            ],
            $formData,
            [
                [
                    'id' => self::PRODUCT_TARGET_ID,
                    'name' => $productName,
                    'image' => $productImage,
                ],
            ],
        ];

        $formData['seo']['redirect_option']['type'] = RedirectType::TYPE_PRODUCT_TEMPORARY;

        yield [
            [
                'language_id' => self::LANGUAGE_ID,
                'product_id' => self::PRODUCT_TARGET_ID,
                'product_name' => $productName,
                'product_image' => $productImage,
            ],
            $formData,
            [
                [
                    'id' => self::PRODUCT_TARGET_ID,
                    'name' => $productName,
                    'image' => $productImage,
                ],
            ],
        ];
    }

    private function getRedirectTargetProvider(?array $mockOptions = null): RedirectTargetProvider
    {
        return new RedirectTargetProvider(
            $this->getProductPreviewRepositoryMock($mockOptions),
            $this->getCategoryPreviewRepository($mockOptions),
            $this->getLegacyContext($mockOptions)
        );
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
