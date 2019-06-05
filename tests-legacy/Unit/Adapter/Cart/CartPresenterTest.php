<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Adapter\Cart;

use Configuration;
use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;

class CartPresenterTest extends UnitTestCase
{
    /**
     * @var CartPresenter
     */
    protected $cartPresenter;

    private $previousSeparator;

    protected function setup()
    {
        parent::setup();
        $this->previousSeparator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        Configuration::set('PS_ATTRIBUTE_ANCHOR_SEPARATOR', '-');
        $this->cartPresenter = new CartPresenter();
    }

    protected function tearDown()
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
     * @dataProvider productAttributesProvider
     */
    public function testProductAttributesAreProperlyConverted($asString, $asArray)
    {
        $this->assertSame(
            $asArray,
            $this->invokeMethod(
                $this->cartPresenter,
                'getAttributesArrayFromString',
                array($asString)
            )
        );
    }

    public function productAttributesProvider()
    {
        return array(
            array(
                'Taille : S- Couleur : Noir',
                array(
                    'Taille' => 'S',
                    'Couleur' => 'Noir',
                ),
            ),
            array(
                'Taille : L- Couleur : Noir', array(
                    'Taille' => 'L',
                    'Couleur' => 'Noir',
                ),
            ),
            array(
                'Taille : M- Couleur : Noir', array(
                    'Taille' => 'M',
                    'Couleur' => 'Noir',
                ),
            ),
            array(
                'Taille : S- Couleur : Blanc', array(
                    'Taille' => 'S',
                    'Couleur' => 'Blanc',
                ),
            ),
            array(
                'Taille : L- Couleur : Bleu', array(
                    'Taille' => 'L',
                    'Couleur' => 'Bleu',
                ),
            ),
            array(
                'Taille : M- Couleur : Taupe - Gris marrone', array(
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gris marrone',
                ),
            ),
            array(
                'Taille : M- Couleur : Taupe - Gri43s marrone', array(
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gri43s marrone',
                ),
            ),
            array(
                'Taille : M- Couleur : Taupe - Gris marroné', array(
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gris marroné',
                ),
            ),
            array(
                'Taille : M- Couleur : Taupe - Gris marronù', array(
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gris marronù',
                ),
            ),
            array(
                'Taille : M-L- Couleur : Taupe', array(
                    'Taille' => 'M-L',
                    'Couleur' => 'Taupe',
                ),
            ),
        );
    }
}
