<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Tests\Model\Product;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PrestaShopBundle\Model\Product\AdminModelAdapter;

class AdminModelAdapterTest extends KernelTestCase
{
    /* @var $adminModelAdapter AdminModelAdapter */
    private $adminModelAdapter;

    private $container;
    protected static $kernel;

    /* @var $product \ProductCore */
    private $product;

    protected function setUp()
    {
        self::$kernel = $this->createKernel();
        self::$kernel->boot();
        $this->container = self::$kernel->getContainer();
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $this->product = $productAdapter->getProduct(1, true);

        $this->adminModelAdapter = new AdminModelAdapter(
            $this->product,
            $this->container->get('prestashop.adapter.legacy.context'),
            $this->container->get('prestashop.adapter.admin.wrapper.product'),
            $this->container->get('prestashop.adapter.tools'),
            $this->container->get('prestashop.adapter.data_provider.product'),
            $this->container->get('prestashop.adapter.data_provider.supplier'),
            $this->container->get('prestashop.adapter.data_provider.warehouse'),
            $this->container->get('prestashop.adapter.data_provider.feature'),
            $this->container->get('prestashop.adapter.data_provider.pack'),
            $this->container->get('prestashop.adapter.shop.context')
        );
    }

    protected function tearDown()
    {
        unset($this->container, $this->product, $this->adminModelAdapter);
        self::$kernel = null;
    }

    private function fakeCombination()
    {
        return [
          "id_product_attribute" => "6",
          "id_product" => "1",
          "reference" => "",
          "supplier_reference" => "",
          "location" => "",
          "ean13" => "",
          "isbn" => "",
          "upc" => "",
          "wholesale_price" => "0.000000",
          "price" => "0.000000",
          "ecotax" => "0.000000",
          "quantity" => 300,
          "weight" => "0.000000",
          "unit_price_impact" => "0.000000",
          "default_on" => null,
          "minimal_quantity" => "1",
          "available_date" => "0000-00-00",
          "id_shop" => "1",
          "id_attribute_group" => "1",
          "is_color_group" => "0",
          "group_name" => "Taille",
          "attribute_name" => "L",
          "id_attribute" => "3"
        ];
    }

    /**
     * Checks that the construction of object still works as expected
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('PrestaShopBundle\Model\Product\AdminModelAdapter', $this->adminModelAdapter);
    }

    public function testGetFormData()
    {
        $this->markTestSkipped('PHPUnit will skip this test method');

        $this->assertInternalType('array', $this->adminModelAdapter->getFormData());
        $expectedReturn = [
            "id_product" => 1,
            "step1" => [
                "type_product" => 0,
                "inputPackItems" => [
                    "data" => []
                ],
                "name" => [
                    1 => "T-shirt délavé à manches courtes"
                ],
                "description" => [
                    1 => "<p>Fashion propose des vêtements de qualité depuis 2010. La marque propose une gamme féminine composée d'élégants vêtements à coordonner et de robes originales et offre désormais une collection complète de prêt-à-porter, regroupant toutes les pièces qu'une femme doit avoir dans sa garde-robe. Fashion se distingue avec des looks à la fois cool, simples et rafraîchissants, alliant élégance et chic, pour un style reconnaissable entre mille. Chacune des magnifiques pièces de la collection est fabriquée avec le plus grand soin en Italie. Fashion enrichit son offre avec une gamme d'accessoires incluant chaussures, chapeaux, ceintures et bien plus encore !</p>",
                ],
                "description_short" => [
                    1 => "<p>T-shirt délavé à manches courtes et col rond. Matière douce et extensible pour un confort inégalé. Pour un look estival, portez-le avec un chapeau de paille !</p>",
                ],
                "active" => true,
                "price_shortcut" => 16.51,
                "qty_0_shortcut" => 1799,
                "categories" => [
                    "tree" => [
                        0 => "2",
                        1 => "3",
                        2 => "4",
                        3 => "5"
                    ]
                ],
                "id_category_default" => "5",
                "related_products" => [
                    "data" => []
                ],
                "id_manufacturer" => "1",
                "features" => [
                    0 => [
                        "feature" => "5",
                        "value" => "5",
                        "custom_value" => null
                    ],
                    1 => [
                        "feature" => "6",
                        "value" => "11",
                        "custom_value" => null
                    ],
                    2 => [
                        "feature" => "7",
                        "value" => "17",
                        "custom_value" => null
                    ]
                ],
                "images" => [
                    0 => [
                        "id" => 1,
                        "id_product" => "1",
                        "position" => "1",
                        "cover" => true,
                        "legend" => [
                            1 => ""
                        ],
                        "format" => "jpg",
                        "base_image_url" => "/img/p/1/1"
                    ],
                    1 => [
                        "id" => 2,
                        "id_product" => "1",
                        "position" => "2",
                        "cover" => false,
                        "legend" => [
                            1 => ""
                        ],
                        "format" => "jpg",
                        "base_image_url" => "/img/p/2/2"
                    ],
                    2 => [
                        "id" => 3,
                        "id_product" => "1",
                        "position" => "3",
                        "cover" => false,
                        "legend" => [
                            1 => ""
                        ],
                        "format" => "jpg",
                        "base_image_url" => "/img/p/3/3"
                    ],
                    3 => [
                        "id" => 4,
                        "id_product" => "1",
                        "position" => "4",
                        "cover" => false,
                        "legend" => [
                            1 => ""
                        ],
                        "format" => "jpg",
                        "base_image_url" => "/img/p/4/4"
                    ]
                ]
            ],
            "step2" => [
                "price" => 16.51,
                "ecotax" => "0.000000",
                "id_tax_rules_group" => "1",
                "on_sale" => false,
                "wholesale_price" => "4.950000",
                "unit_price" => 0,
                "unity" => "",
                "specific_price" => [
                    "sp_from_quantity" => 1,
                    "sp_reduction" => 0,
                    "sp_reduction_tax" => 1,
                    "leave_bprice" => true,
                    "sp_id_shop" => 0,
                ],
                "specificPricePriority_0" => "id_shop",
                "specificPricePriority_1" => "id_currency",
                "specificPricePriority_2" => "id_country",
                "specificPricePriority_3" => "id_group"
            ],
            "step3" => [
                "advanced_stock_management" => false,
                "depends_on_stock" => "0",
                "qty_0" => 1799,
                "combinations" => [
                    0 => [
                        "id_product_attribute" => "1",
                        "attributes" => [
                            0 => "Taille",
                            1 => "S",
                            2 => "1"
                        ],
                        "attribute_reference" => "",
                        "attribute_ean13" => "",
                        "attribute_isbn" => "",
                        "attribute_upc" => "",
                        "attribute_wholesale_price" => "0.000000",
                        "attribute_price_impact" => 0,
                        "attribute_price" => "0.000000",
                        "attribute_price_display" => "0,00 €",
                        "attribute_priceTI" => "",
                        "attribute_ecotax" => "0.000000",
                        "attribute_weight_impact" => 0,
                        "attribute_weight" => "0.000000",
                        "attribute_unit_impact" => 0,
                        "attribute_unity" => "0.000000",
                        "attribute_minimal_quantity" => "1",
                        "available_date_attribute" => "0000-00-00",
                        "attribute_default" => true,
                        "attribute_quantity" => 299,
                        "name" => "Taille - S, Couleur - Orange"
                    ],
                    1 => [
                        "id_product_attribute" => "2",
                        "attributes" => [
                            0 => "Taille",
                            1 => "S",
                            2 => "1"
                        ],
                        "attribute_reference" => "",
                        "attribute_ean13" => "",
                        "attribute_isbn" => "",
                        "attribute_upc" => "",
                        "attribute_wholesale_price" => "0.000000",
                        "attribute_price_impact" => 0,
                        "attribute_price" => "0.000000",
                        "attribute_price_display" => "0,00 €",
                        "attribute_priceTI" => "",
                        "attribute_ecotax" => "0.000000",
                        "attribute_weight_impact" => 0,
                        "attribute_weight" => "0.000000",
                        "attribute_unit_impact" => 0,
                        "attribute_unity" => "0.000000",
                        "attribute_minimal_quantity" => "1",
                        "available_date_attribute" => "0000-00-00",
                        "attribute_default" => false,
                        "attribute_quantity" => 300,
                        "name" => "Taille - S, Couleur - Bleu"
                    ],
                    2 => [
                        "id_product_attribute" => "3",
                        "attributes" => [
                            0 => "Taille",
                            1 => "M",
                            2 => "2"
                        ],
                        "attribute_reference" => "",
                        "attribute_ean13" => "",
                        "attribute_isbn" => "",
                        "attribute_upc" => "",
                        "attribute_wholesale_price" => "0.000000",
                        "attribute_price_impact" => 0,
                        "attribute_price" => "0.000000",
                        "attribute_price_display" => "0,00 €",
                        "attribute_priceTI" => "",
                        "attribute_ecotax" => "0.000000",
                        "attribute_weight_impact" => 0,
                        "attribute_weight" => "0.000000",
                        "attribute_unit_impact" => 0,
                        "attribute_unity" => "0.000000",
                        "attribute_minimal_quantity" => "1",
                        "available_date_attribute" => "0000-00-00",
                        "attribute_default" => false,
                        "attribute_quantity" => 300,
                        "name" => "Taille - M, Couleur - Orange"
                    ],
                    3 => [
                        "id_product_attribute" => "4",
                        "attributes" => [
                            0 => "Taille",
                            1 => "M",
                            2 => "2"
                        ],
                        "attribute_reference" => "",
                        "attribute_ean13" => "",
                        "attribute_isbn" => "",
                        "attribute_upc" => "",
                        "attribute_wholesale_price" => "0.000000",
                        "attribute_price_impact" => 0,
                        "attribute_price" => "0.000000",
                        "attribute_price_display" => "0,00 €",
                        "attribute_priceTI" => "",
                        "attribute_ecotax" => "0.000000",
                        "attribute_weight_impact" => 0,
                        "attribute_weight" => "0.000000",
                        "attribute_unit_impact" => 0,
                        "attribute_unity" => "0.000000",
                        "attribute_minimal_quantity" => "1",
                        "available_date_attribute" => "0000-00-00",
                        "attribute_default" => false,
                        "attribute_quantity" => 300,
                        "name" => "Taille - M, Couleur - Bleu"
                    ],
                    4 => [
                        "id_product_attribute" => "5",
                        "attributes" => [
                            0 => "Taille",
                            1 => "L",
                            2 => "3"
                        ],
                        "attribute_reference" => "",
                        "attribute_ean13" => "",
                        "attribute_isbn" => "",
                        "attribute_upc" => "",
                        "attribute_wholesale_price" => "0.000000",
                        "attribute_price_impact" => 0,
                        "attribute_price" => "0.000000",
                        "attribute_price_display" => "0,00 €",
                        "attribute_priceTI" => "",
                        "attribute_ecotax" => "0.000000",
                        "attribute_weight_impact" => 0,
                        "attribute_weight" => "0.000000",
                        "attribute_unit_impact" => 0,
                        "attribute_unity" => "0.000000",
                        "attribute_minimal_quantity" => "1",
                        "available_date_attribute" => "0000-00-00",
                        "attribute_default" => false,
                        "attribute_quantity" => 300,
                        "name" => "Taille - L, Couleur - Orange"
                    ],
                    5 => [
                        "id_product_attribute" => "6",
                        "attributes" => [
                            0 => "Taille",
                            1 => "L",
                            2 => "3"
                        ],
                        "attribute_reference" => "",
                        "attribute_ean13" => "",
                        "attribute_isbn" => "",
                        "attribute_upc" => "",
                        "attribute_wholesale_price" => "0.000000",
                        "attribute_price_impact" => 0,
                        "attribute_price" => "0.000000",
                        "attribute_price_display" => "0,00 €",
                        "attribute_priceTI" => "",
                        "attribute_ecotax" => "0.000000",
                        "attribute_weight_impact" => 0,
                        "attribute_weight" => "0.000000",
                        "attribute_unit_impact" => 0,
                        "attribute_unity" => "0.000000",
                        "attribute_minimal_quantity" => "1",
                        "available_date_attribute" => "0000-00-00",
                        "attribute_default" => false,
                        "attribute_quantity" => 300,
                        "name" => "Taille - L, Couleur - Bleu"
                    ],
                ],
                "out_of_stock" => 2,
                "minimal_quantity" => "1",
                "available_now" => [
                    1 => "En stock"
                ],
                "available_later" => [
                    1 => ""
                ],
                "available_date" => "0000-00-00",
                "pack_stock_type" => "3",
                "virtual_product" => [
                    "is_virtual_file" => 0,
                    "nb_days" => 0
                ],
            ],
            "step4" => [
                "width" => "0.000000",
                "height" => "0.000000",
                "depth" => "0.000000",
                "weight" => "0.000000",
                "additional_shipping_cost" => "0.00",
                "selectedCarriers" => []
            ],
            "step5" => [
                "link_rewrite" => [
                    1 => "t-shirt-delave-manches-courtes"
                ],
                "meta_title" => [
                    1 => ""
                ],
                "meta_description" => [
                    1 => ""
                ],
            ],
            "step6" => [
                "redirect_type" => "404",
                "id_product_redirected" => [
                    "data" => [
                        0 => "0"
                    ]
                ],
                "visibility" => "both",
                "display_options" => [
                    "available_for_order" => true,
                    "show_price" => true,
                    "online_only" => false
                ],
                "upc" => "",
                "ean13" => "0",
                "isbn" => "",
                "reference" => "demo_1",
                "condition" => "new",
                "suppliers" => [
                    0 => "1"
                ],
                "default_supplier" => "1",
                "custom_fields" => [],
                "attachments" => [],
                "supplier_combination_1" => [
                    0 => [
                        "label" => "Taille - S, Couleur - Orange",
                        "supplier_reference" => "",
                        "product_price" => 0,
                        "product_price_currency" => 1,
                        "supplier_id" => "1",
                        "product_id" => 1,
                        "id_product_attribute" => "1",
                    ],
                    1 => [
                        "label" => "Taille - S, Couleur - Bleu",
                        "supplier_reference" => "",
                        "product_price" => 0,
                        "product_price_currency" => 1,
                        "supplier_id" => "1",
                        "product_id" => 1,
                        "id_product_attribute" => "2"
                    ],
                    2 => [
                        "label" => "Taille - M, Couleur - Orange",
                        "supplier_reference" => "",
                        "product_price" => 0,
                        "product_price_currency" => 1,
                        "supplier_id" => "1",
                        "product_id" => 1,
                        "id_product_attribute" => "3"
                    ],
                    3 => [
                        "label" => "Taille - M, Couleur - Bleu",
                        "supplier_reference" => "",
                        "product_price" => 0,
                        "product_price_currency" => 1,
                        "supplier_id" => "1",
                        "product_id" => 1,
                        "id_product_attribute" => "4"
                    ],
                    4 => [
                        "label" => "Taille - L, Couleur - Orange",
                        "supplier_reference" => "",
                        "product_price" => 0,
                        "product_price_currency" => 1,
                        "supplier_id" => "1",
                        "product_id" => 1,
                        "id_product_attribute" => "5"
                    ],
                    5 => [
                        "label" => "Taille - L, Couleur - Bleu",
                        "supplier_reference" => "",
                        "product_price" => 0,
                        "product_price_currency" => 1,
                        "supplier_id" => "1",
                        "product_id" => 1,
                        "id_product_attribute" => "6"
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedReturn, $this->adminModelAdapter->getFormData());
    }

    public function testGetModelData()
    {
        $this->markTestSkipped('PHPUnit will skip this test method');

        $this->assertInternalType('array', $this->adminModelAdapter->getModelData($this->adminModelAdapter->getFormData()));
    }

    public function testGetFormCombination()
    {
        $this->markTestSkipped('PHPUnit will skip this test method');

        $expectedReturn = [
            "id_product_attribute" => "6",
            "attributes" => [
                0 => "Taille",
                1 => "L",
                2 => "3"
            ],
            "attribute_reference" => "",
            "attribute_ean13" => "",
            "attribute_isbn" => "",
            "attribute_upc" => "",
            "attribute_wholesale_price" => "0.000000",
            "attribute_price_impact" => 0,
            "attribute_price" => "0.000000",
            "attribute_price_display" => "0,00 €",
            "attribute_priceTI" => "",
            "attribute_ecotax" => "0.000000",
            "attribute_weight_impact" => 0,
            "attribute_weight" => "0.000000",
            "attribute_unit_impact" => 0,
            "attribute_unity" => "0.000000",
            "attribute_minimal_quantity" => "1",
            "available_date_attribute" => "0000-00-00",
            "attribute_default" => false,
            "attribute_quantity" => 300,
            "name" => "Taille - L, Couleur - Bleu"
        ];

        $this->assertEquals($expectedReturn, $this->adminModelAdapter->getFormCombination($this->fakeCombination()));
    }
}
