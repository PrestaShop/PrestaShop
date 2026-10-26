<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Product;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductPreviewProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductProvider;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\FooterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FooterTypeTest extends TestCase
{
    /**
     * @dataProvider getDeleteConfirmationCases
     */
    public function testDeleteConfirmation(array $options, string $expectedMessage): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static function (string $message, array $parameters, string $domain): string {
            return $domain . ':' . $message;
        });
        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnCallback(function (string $route, array $parameters): string {
            $this->assertNotSame('admin_products_delete_from_all_shops', $route);
            if ($route === 'admin_products_delete_from_shop') {
                $this->assertSame(['productId' => 42, 'shopId' => 2], $parameters);

                return '/42/delete-from-shop/2';
            }

            return '/';
        });
        $footer = new FooterType(
            $translator,
            [],
            $this->createMock(ProductProvider::class),
            $this->createMock(ProductPreviewProvider::class),
            $router,
            $this->createMock(FeatureInterface::class),
            2
        );
        $resolver = new OptionsResolver();
        $footer->configureOptions($resolver);
        $options = $resolver->resolve(array_merge(['product_id' => 42], $options));

        $deleteAttributes = [];
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnCallback(static function (string $name, string $type, array $options) use ($builder, &$deleteAttributes): FormBuilderInterface {
            if ($name === 'actions') {
                $deleteAttributes = $options['buttons']['delete']['options']['attr'];
            }

            return $builder;
        });

        $footer->buildForm($builder, $options);

        $this->assertSame('Admin.Catalog.Notification:' . $expectedMessage, $deleteAttributes['data-modal-title']);
        $this->assertSame('/42/delete-from-shop/2', $deleteAttributes['data-button-url']);
    }

    public static function getDeleteConfirmationCases(): iterable
    {
        yield 'default' => [[], 'Permanently delete this product?'];
        yield 'last shop' => [['has_multiple_shops' => false], 'Permanently delete this product?'];
        yield 'multiple shops' => [['has_multiple_shops' => true], 'Delete this product from the current shop?'];
    }
}
