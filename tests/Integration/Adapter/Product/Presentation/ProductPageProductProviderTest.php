<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Presentation;

use Controller;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Product\Presentation\ProductPageProductProvider;
use Product;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;

class ProductPageProductProviderTest extends KernelTestCase
{
    use ContextMockerTrait;

    private ProductPageProductProvider $provider;

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

        $this->provider = self::getContainer()->get(ProductPageProductProvider::class);
    }

    public function testItDoesNotRestrictFilterProductContentHookReturnType(): void
    {
        $method = new ReflectionMethod($this->provider, 'filterProductContent');

        $this->assertNull($method->getReturnType());
    }

    public function testItBuildsProductPageProductWithExplicitRequestedQuantity(): void
    {
        $product = new Product(null, false, 1);
        $product->name = 'Product page provider product';
        $product->link_rewrite = 'product-page-provider-product';
        $product->price = 12.34;
        $this->assertTrue($product->save());

        try {
            $presentedProduct = $this->provider->getProduct(
                $product,
                self::getMockedContext(),
                null,
                3
            );

            $this->assertInstanceOf(ProductLazyArray::class, $presentedProduct);
            $this->assertSame(3, (int) $presentedProduct['quantity_wanted']);
        } finally {
            $product->delete();
        }
    }
}
