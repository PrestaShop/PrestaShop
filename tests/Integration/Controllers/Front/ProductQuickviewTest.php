<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controllers\Front;

use Configuration;
use Context;
use Currency;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use ProductControllerCore;
use Smarty_Internal_Data;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The quickview is rendered through FrontController::render(), which assigns every key of the array it
 * is handed as a template variable. Passing the serialised product therefore overwrote the globals of
 * the same name: $link stopped being the Link service inside the quickview and everything it includes,
 * and $category became a slug.
 */
class ProductQuickviewTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // getRoundedDisplayPrice() reads the context currency, which the CLI context leaves unset.
        Context::getContext()->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
    }

    public function testTheQuickviewIsRenderedWithTheProductAlone(): void
    {
        $this->assertSame(['product'], array_keys($this->renderQuickview()['params']));
    }

    /**
     * @dataProvider globalsTheProductWouldHaveShadowed
     */
    public function testTheProductDoesNotShadowAGlobal(string $variable): void
    {
        $params = $this->renderQuickview()['params'];

        $this->assertArrayNotHasKey($variable, $params);
    }

    /**
     * @return array<string, array{string}>
     */
    public function globalsTheProductWouldHaveShadowed(): array
    {
        return [
            'link' => ['link'],
            'category' => ['category'],
            'id' => ['id'],
        ];
    }

    public function testTheGlobalLinkServiceSurvivesInTheQuickviewScope(): void
    {
        $scope = $this->scopeAsRenderBuildsIt($this->renderQuickview()['params']);

        $this->assertInstanceOf(
            Link::class,
            $scope->getTemplateVars('link'),
            '$link must stay the Link service, not the product URL'
        );
    }

    public function testTheQuickviewScopeStillCarriesTheProduct(): void
    {
        $scope = $this->scopeAsRenderBuildsIt($this->renderQuickview()['params']);

        $this->assertSame(1, (int) $scope->getTemplateVars('product')['id_product']);
    }

    /**
     * The JSON payload the theme's JavaScript reads must not shrink: getEmbeddedAttributes() only
     * returns whitelisted properties that have already been resolved on the lazy array. Until now that
     * resolution happened as a side effect of serialising the product for render(); this guards the
     * explicit call that replaced it. Green on both sides of the fix by design.
     */
    public function testTheEmbeddedAttributesAreResolvedBeforeTheyAreRead(): void
    {
        $embedded = $this->renderQuickview()['embedded'];

        // The three that disappear if nothing resolves the lazy array before it is read.
        foreach (['features', 'attachments', 'attribute_price'] as $attribute) {
            $this->assertArrayHasKey($attribute, $embedded);
        }
    }

    /**
     * Runs displayAjaxQuickview() against a real presented product, capturing what it hands to
     * render() and what it puts in the JSON payload.
     *
     * @return array{params: array<string, mixed>, embedded: array<string, mixed>}
     */
    private function renderQuickview(): array
    {
        $product = $this->presentedProduct();

        $controller = new class($product) extends ProductControllerCore {
            /** @var array<string, mixed> */
            public array $renderParameters = [];

            /** @var array<string, mixed> */
            public array $payload = [];

            public function __construct(private ProductLazyArray $presented)
            {
                parent::__construct();
            }

            public function getTemplateVarProduct(): ProductLazyArray
            {
                return $this->presented;
            }

            protected function render($template, array $params = [])
            {
                $this->renderParameters = $params;

                return '';
            }

            protected function ajaxRender($value = null, $controller = null, $method = null)
            {
                $this->payload = json_decode((string) $value, true) ?? [];

                return true;
            }
        };

        $level = ob_get_level();
        ob_start();
        $controller->displayAjaxQuickview();
        while (ob_get_level() > $level) {
            ob_end_clean();
        }

        return [
            'params' => $controller->renderParameters,
            'embedded' => $controller->payload['product'] ?? [],
        ];
    }

    /**
     * Builds the template scope the same way FrontController::render() does, so the assertions are made
     * on the variables the template actually sees.
     *
     * @param array<string, mixed> $params
     */
    private function scopeAsRenderBuildsIt(array $params): Smarty_Internal_Data
    {
        $smarty = Context::getContext()->smarty;
        $smarty->assign('link', Context::getContext()->link);
        $scope = $smarty->createData($smarty);
        $scope->assign($params);

        return $scope;
    }

    private function presentedProduct(): ProductLazyArray
    {
        $settings = new ProductPresentationSettings();
        $settings->catalog_mode = false;
        $settings->restricted_country_mode = false;
        $settings->showPrices = true;

        $imageRetriever = $this->createMock(ImageRetriever::class);
        $imageRetriever->method('getAllProductImages')->withAnyParameters()->willReturn([
            ['id_image' => 0, 'associatedVariants' => []],
        ]);

        $link = $this->createMock(Link::class);
        $link->method('getAddToCartURL')->withAnyParameters()->willReturn('http://add-to-cart.url');
        $link->method('getProductLink')->withAnyParameters()->willReturn('http://product.url');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->withAnyParameters()->willReturn('some label');

        $priceFormatter = $this->createMock(PriceFormatter::class);
        $priceFormatter->method('convertAmount')->withAnyParameters()->willReturnArgument(0);
        $priceFormatter->method('format')->withAnyParameters()->willReturnCallback(
            fn (?float $price) => '#' . $price
        );

        $presenter = new ProductPresenter(
            $imageRetriever,
            $link,
            $priceFormatter,
            $this->createMock(ProductColorsRetriever::class),
            $translator
        );

        return $presenter->present(
            $settings,
            [
                'available_for_order' => true,
                'id_product' => 1,
                'id_product_attribute' => 0,
                'link_rewrite' => 'hummingbird-printed-t-shirt',
                'category' => 'clothes',
                'reference' => 'demo_1',
                'price' => null,
                'price_without_reduction' => null,
                'price_without_reduction_without_tax' => null,
                'price_tax_exc' => null,
                'specific_prices' => null,
                'customizable' => false,
                'is_virtual' => false,
                'id_category_default' => 2,
                'minimal_quantity' => 1,
                'additional_delivery_times' => 0,
                'quantity' => 1,
                'allow_oosp' => false,
                'online_only' => false,
                'reduction' => false,
                'on_sale' => false,
                'new' => false,
                'pack' => false,
                'show_price' => true,
                'active' => true,
            ],
            $this->createMock(Language::class)
        );
    }
}
