<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart;

use Cart;
use Configuration;
use Context;
use Customer;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use ReflectionClass;

class CartPresenterTest extends TestCase
{
    /**
     * @var CartPresenter
     */
    protected $cartPresenter;

    /**
     * @var false|string
     */
    private $previousSeparator;

    private ?Cart $cart = null;

    private ?Customer $previousCustomer = null;

    protected function setUp(): void
    {
        parent::setup();

        $this->previousSeparator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        Configuration::set('PS_ATTRIBUTE_ANCHOR_SEPARATOR', '-');
        $this->cartPresenter = new CartPresenter();
    }

    protected function tearDown(): void
    {
        Configuration::set('PS_ATTRIBUTE_ANCHOR_SEPARATOR', $this->previousSeparator);
        if ($this->cart) {
            $this->cart->delete();
            Context::getContext()->customer = $this->previousCustomer;
        }

        parent::tearDown();
    }

    /**
     * We check that our attributes are properly parsed from a string, EVEN IF their value or label also have the
     * separator in them.
     * See https://regex101.com/r/wlRNtX/1 for examples used
     *
     * @param string $asString
     * @param array $asArray
     *
     * @dataProvider providerProductAttributes
     */
    public function testProductAttributesAreProperlyConverted($asString, $asArray)
    {
        $this->assertSame(
            $asArray,
            $this->invokeMethod(
                $this->cartPresenter,
                'getAttributesArrayFromString',
                [$asString]
            )
        );
    }

    public function providerProductAttributes(): iterable
    {
        yield [
            'Taille : S- Couleur : Noir',
            [
                'Taille' => 'S',
                'Couleur' => 'Noir',
            ],
        ];
        yield [
            'Taille : L- Couleur : Noir',
            [
                'Taille' => 'L',
                'Couleur' => 'Noir',
            ],
        ];
        yield [
            'Taille : M- Couleur : Noir',
            [
                'Taille' => 'M',
                'Couleur' => 'Noir',
            ],
        ];
        yield [
            'Taille : S- Couleur : Blanc',
            [
                'Taille' => 'S',
                'Couleur' => 'Blanc',
            ],
        ];
        yield [
            'Taille : L- Couleur : Bleu',
            [
                'Taille' => 'L',
                'Couleur' => 'Bleu',
            ],
        ];
        yield [
            'Taille : M- Couleur : Taupe - Gris marrone',
            [
                'Taille' => 'M',
                'Couleur' => 'Taupe - Gris marrone',
            ],
        ];
        yield [
            'Taille : M- Couleur : Taupe - Gri43s marrone',
            [
                'Taille' => 'M',
                'Couleur' => 'Taupe - Gri43s marrone',
            ],
        ];
        yield [
            'Taille : M- Couleur : Taupe - Gris marroné',
            [
                'Taille' => 'M',
                'Couleur' => 'Taupe - Gris marroné',
            ],
        ];
        yield [
            'Taille : M- Couleur : Taupe - Gris marronù',
            [
                'Taille' => 'M',
                'Couleur' => 'Taupe - Gris marronù',
            ],
        ];
        yield [
            'Taille : M-L- Couleur : Taupe',
            [
                'Taille' => 'M-L',
                'Couleur' => 'Taupe',
            ],
        ];
    }

    public function testCustomizationDataIsReadOnceForProductsWithoutCustomization(): void
    {
        $shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        // The cart links of a customized product carry a token built from the context customer.
        $this->previousCustomer = Context::getContext()->customer;
        Context::getContext()->customer = new Customer();
        $this->cart = new Cart();
        $this->cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $this->cart->id_shop = $shopId;
        $this->assertTrue($this->cart->add());

        $db = Db::getInstance();
        $this->assertTrue($db->insert('customization', [
            'id_cart' => (int) $this->cart->id,
            'id_product' => 19,
            'id_product_attribute' => 0,
            'id_address_delivery' => 0,
            'quantity' => 1,
            'in_cart' => 1,
        ]));
        $customizationId = (int) $db->Insert_ID();
        $this->assertTrue($db->insert('customized_data', [
            'id_customization' => $customizationId,
            'type' => 1,
            'index' => 1,
            'value' => 'Engraved text',
        ]));
        $this->assertTrue($db->insert('cart_product', [
            'id_cart' => (int) $this->cart->id,
            'id_product' => 19,
            'id_product_attribute' => 0,
            'id_customization' => $customizationId,
            'id_shop' => $shopId,
            'quantity' => 1,
            'date_add' => date('Y-m-d H:i:s'),
        ]));

        $plainProduct = function (int $productId): array {
            return ['id_product' => $productId, 'id_product_attribute' => 0, 'id_customization' => 0];
        };
        $presented = $this->cartPresenter->addCustomizedData(
            [$plainProduct(6), ['id_product' => 19, 'id_product_attribute' => 0, 'id_customization' => $customizationId], $plainProduct(7)],
            $this->cart
        );

        $this->assertSame([], $presented[0]['customizations']);
        $this->assertSame('Engraved text', $presented[1]['customizations'][0]['fields'][0]['text']);
        $this->assertSame([], $presented[2]['customizations']);

        $queriesForOneProduct = $this->countQueries(function () use ($plainProduct) {
            $this->cartPresenter->addCustomizedData([$plainProduct(6)], $this->cart);
        });
        $queriesForThreeProducts = $this->countQueries(function () use ($plainProduct) {
            $this->cartPresenter->addCustomizedData([$plainProduct(6), $plainProduct(7), $plainProduct(8)], $this->cart);
        });
        $this->assertGreaterThan(0, $queriesForOneProduct);
        $this->assertSame($queriesForOneProduct, $queriesForThreeProducts);
    }

    private function countQueries(callable $callable): int
    {
        $questions = function (): int {
            return (int) Db::getInstance()->executeS('SHOW SESSION STATUS LIKE \'Questions\'', true, false)[0]['Value'];
        };
        $before = $questions();
        $callable();

        return $questions() - $before - 1;
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array $parameters array of parameters to pass into method
     *
     * @return mixed method return
     *
     * @see https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
     */
    protected function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);

        return $method->invokeArgs($object, $parameters);
    }
}
