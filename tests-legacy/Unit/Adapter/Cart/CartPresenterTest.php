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
                [$asString]
            )
        );
    }

    public function productAttributesProvider()
    {
        return [
            [
                'Taille : S- Couleur : Noir',
                [
                    'Taille' => 'S',
                    'Couleur' => 'Noir',
                ],
            ],
            [
                'Taille : L- Couleur : Noir', [
                    'Taille' => 'L',
                    'Couleur' => 'Noir',
                ],
            ],
            [
                'Taille : M- Couleur : Noir', [
                    'Taille' => 'M',
                    'Couleur' => 'Noir',
                ],
            ],
            [
                'Taille : S- Couleur : Blanc', [
                    'Taille' => 'S',
                    'Couleur' => 'Blanc',
                ],
            ],
            [
                'Taille : L- Couleur : Bleu', [
                    'Taille' => 'L',
                    'Couleur' => 'Bleu',
                ],
            ],
            [
                'Taille : M- Couleur : Taupe - Gris marrone', [
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gris marrone',
                ],
            ],
            [
                'Taille : M- Couleur : Taupe - Gri43s marrone', [
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gri43s marrone',
                ],
            ],
            [
                'Taille : M- Couleur : Taupe - Gris marroné', [
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gris marroné',
                ],
            ],
            [
                'Taille : M- Couleur : Taupe - Gris marronù', [
                    'Taille' => 'M',
                    'Couleur' => 'Taupe - Gris marronù',
                ],
            ],
            [
                'Taille : M-L- Couleur : Taupe', [
                    'Taille' => 'M-L',
                    'Couleur' => 'Taupe',
                ],
            ],
        ];
    }
}
