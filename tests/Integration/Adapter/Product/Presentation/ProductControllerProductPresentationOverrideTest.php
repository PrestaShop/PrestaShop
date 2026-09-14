<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Presentation;

use Context;
use Controller;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use Product;
use ProductControllerCore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Utility\ContextMockerTrait;

require_once _PS_ROOT_DIR_ . '/controllers/front/ProductController.php';

/**
 * Test controller used to verify that legacy ProductController overrides are still
 * invoked by the product-page presentation pipeline after the provider extraction.
 */
final class ProductControllerWithMinimalQuantityOverride extends ProductControllerCore
{
    public bool $minimalQuantityOverrideCalled = false;

    public ProductLazyArray|array|null $minimalQuantityProduct = null;

    public function configureForTest(
        Product $product,
        Context $context,
        ContainerInterface $container,
    ): void {
        $this->product = $product;
        $this->id_product = (int) $product->id;
        $this->id_product_attribute = null;
        $this->context = $context;
        $this->container = $container;
        $this->templateVarProductCache = null;
    }

    protected function getProductMinimalQuantity(ProductLazyArray|array $product)
    {
        $this->minimalQuantityOverrideCalled = true;

        if ($this->minimalQuantityProduct === null) {
            $this->minimalQuantityProduct = $product;
        }

        return 99;
    }
}

final class ProductControllerProductPresentationOverrideTest extends KernelTestCase
{
    use ContextMockerTrait;

    protected function setUp(): void
    {
        parent::setUp();

        self::mockContext();

        $context = self::getMockedContext();

        $localeRepository = self::getContainer()->get(
            Controller::SERVICE_LOCALE_REPOSITORY
        );

        $context->currentLocale = $localeRepository->getLocale(
            $context->language->getLocale()
        );

        // Boot the Symfony container before instantiating the legacy controller.
        self::getContainer();
    }

    public function testLegacyMinimalQuantityOverrideIsUsedByProductPresentationPipeline(): void
    {
        $product = new Product(null, false, 1);
        $product->name = 'Product controller override test';
        $product->link_rewrite = 'product-controller-override-test';
        $product->price = 12.34;
        $product->minimal_quantity = 1;

        self::assertTrue($product->save());

        $previousGet = $_GET;
        $previousPost = $_POST;

        try {
            $_GET = [
                'quantity_wanted' => 1,
            ];
            $_POST = [];

            $controller = new ProductControllerWithMinimalQuantityOverride();
            $controller->configureForTest(
                $product,
                self::getMockedContext(),
                self::getContainer(),
            );

            $presentedProduct = $controller->getTemplateVarProduct();

            self::assertTrue(
                $controller->minimalQuantityOverrideCalled,
                'The legacy getProductMinimalQuantity() override was not called.'
            );

            self::assertNotNull($controller->minimalQuantityProduct);
            self::assertArrayHasKey('id', $controller->minimalQuantityProduct);
            self::assertArrayHasKey('id_product_attribute', $controller->minimalQuantityProduct);
            self::assertSame(
                (int) $product->id,
                (int) $controller->minimalQuantityProduct['id']
            );

            self::assertSame(99, (int) $presentedProduct['minimal_quantity']);
            self::assertSame(99, (int) $presentedProduct['quantity_wanted']);
            self::assertSame(99, (int) $presentedProduct['quantity_required']);
        } finally {
            $_GET = $previousGet;
            $_POST = $previousPost;

            $product->delete();
        }
    }
}
