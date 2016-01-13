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
            $this->container->get('prestashop.adapter.data_provider.pack')
        );
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

    protected function tearDown()
    {
        unset($this->container, $this->product, $this->adminModelAdapter);
        self::$kernel = null;
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('PrestaShopBundle\Model\Product\AdminModelAdapter', $this->adminModelAdapter);
    }

    public function testGetFormData()
    {
        $this->assertInternalType('array', $this->adminModelAdapter->getFormData());
    }

    public function testGetModelData()
    {
        $this->assertInternalType('array', $this->adminModelAdapter->getModelData($this->adminModelAdapter->getFormData()));
    }

    public function testGetFormCombination()
    {
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
