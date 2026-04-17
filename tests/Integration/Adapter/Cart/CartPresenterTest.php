<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart;

use Configuration;
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
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
