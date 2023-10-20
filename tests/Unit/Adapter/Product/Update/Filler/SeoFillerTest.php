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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\SeoFiller;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use Product;

class SeoFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param Product $product
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param Product $expectedProduct
     */
    public function testFillsUpdatableProperties(
        Product $product,
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        Product $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(true, true),
            $product,
            $command,
            $expectedUpdatableProperties,
            $expectedProduct
        );
    }

    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $localizedMetaTitles = [
            1 => 'Titre français recherche',
            2 => 'English title seo',
        ];

        $localizedMetaDescriptions = [
            1 => 'Description française recherche',
            2 => 'English description seo',
        ];

        $localizedLinkRewrites = [
            1 => 'produit-francais',
            2 => 'english-product',
        ];

        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setLocalizedMetaTitles($localizedMetaTitles)
            ->setLocalizedMetaDescriptions($localizedMetaDescriptions)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->meta_title = $localizedMetaTitles;
        $expectedProduct->meta_description = $localizedMetaDescriptions;

        yield [
            $product,
            $command,
            [
                'meta_description' => [1, 2],
                'meta_title' => [1, 2],
            ],
            $expectedProduct,
        ];

        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setLocalizedLinkRewrites($localizedLinkRewrites)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->link_rewrite = $localizedLinkRewrites;

        yield [
            $product,
            $command,
            [
                'link_rewrite' => [1, 2],
            ],
            $expectedProduct,
        ];

        $localizedNames = [
            1 => 'name1',
            2 => 'name2',
        ];
        $product = $this->mockDefaultProduct();
        $product->name = $localizedNames;
        $command = $this->getEmptyCommand()
            ->setLocalizedLinkRewrites([
                1 => '',
                2 => '',
            ])
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->name = $localizedNames;
        // we expect that filler filled name value as a link rewrite, because empty link rewrites were provided
        $expectedProduct->link_rewrite = $localizedNames;

        yield [
            $product,
            $command,
            [
                'link_rewrite' => [1, 2],
            ],
            $expectedProduct,
        ];

        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_PERMANENT, 1)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->redirect_type = RedirectType::TYPE_PRODUCT_PERMANENT;
        $expectedProduct->id_type_redirected = 1;

        yield [
            $product,
            $command,
            [
                'redirect_type',
                'id_type_redirected',
            ],
            $expectedProduct,
        ];

        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setRedirectOption(RedirectType::TYPE_CATEGORY_TEMPORARY, 1)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->redirect_type = RedirectType::TYPE_CATEGORY_TEMPORARY;
        $expectedProduct->id_type_redirected = 1;

        yield [
            $product,
            $command,
            [
                'redirect_type',
                'id_type_redirected',
            ],
            $expectedProduct,
        ];
    }

    public function testThrowsExceptionWhenNonExistingRedirectTargetIsProvided(): void
    {
        $seoFiller = $this->getFiller(false, false);

        $this->expectException(ProductNotFoundException::class);
        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 1)
        ;
        $seoFiller->fillUpdatableProperties($product, $command);

        $this->expectException(CategoryNotFoundException::class);
        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setRedirectOption(RedirectType::TYPE_CATEGORY_PERMANENT, 1)
        ;
        $seoFiller->fillUpdatableProperties($product, $command);
    }

    /**
     * @param bool $redirectProductExists
     * @param bool $redirectCategoryExists
     *
     * @return SeoFiller
     */
    private function getFiller(bool $redirectProductExists, bool $redirectCategoryExists): SeoFiller
    {
        return new SeoFiller(
            $this->mockProductRepository($redirectProductExists),
            $this->mockCategoryRepository($redirectCategoryExists),
            $this->mockTools()
        );
    }

    /**
     * @param bool $redirectProductExists
     *
     * @return ProductRepository
     */
    private function mockProductRepository(bool $redirectProductExists): ProductRepository
    {
        $mock = $this->createMock(ProductRepository::class);
        if (!$redirectProductExists) {
            $mock->method('assertProductExists')
                ->willThrowException(new ProductNotFoundException())
            ;
        }

        return $mock;
    }

    /**
     * @param bool $redirectCategoryExists
     *
     * @return CategoryRepository
     */
    private function mockCategoryRepository(bool $redirectCategoryExists): CategoryRepository
    {
        $mock = $this->createMock(CategoryRepository::class);
        if (!$redirectCategoryExists) {
            $mock->method('assertCategoryExists')
                // we don't assert the internals of assertCategoryExists method, so categoryId and exception string is not important
                ->willThrowException(new CategoryNotFoundException(new CategoryId(1)));
        }

        return $mock;
    }

    /**
     * @return Tools
     */
    private function mockTools(): Tools
    {
        $toolsMock = $this->getMockBuilder(Tools::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['linkRewrite'])
            ->getMock()
        ;

        $toolsMock
            ->method('linkRewrite')
            ->willReturnArgument(0)
        ;

        return $toolsMock;
    }
}
