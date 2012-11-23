<?php 

use PrestaShop as PrestaShop;

class ToolsTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		PrestaShop\Test\Runtime :: disableDb();
	}
	
	public function testConvertPriceWithOneParameterAndDefaultCurrency() {
		
		PrestaShop\Test\Runtime :: configuration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		PrestaShop\Test\Runtime :: context(array(
			'currency' => array(
				'id_currency' 		=> 1,
				'conversion_rate'	=> 1.000000
			)
		));
		
		$actual = Tools :: convertPrice(10.00);
		$this->assertEquals(10.00, $actual);
		
	}
	
	public function testConvertPriceWithOneParameter() {
		
		PrestaShop\Test\Runtime :: configuration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		PrestaShop\Test\Runtime :: context(array(
			'currency' => array(
				'id_currency' 		=> 2,
				'conversion_rate'	=> 1.500000
			)
		));
		
		$actual = Tools :: convertPrice(10.00);
		$this->assertEquals(15.00, $actual);
		
	}
	
	public function testConvertPriceWithCurrencyArray() {
		
		PrestaShop\Test\Runtime :: configuration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		$currency = array(
			'id_currency' 		=> 2,
			'conversion_rate'	=> 1.500000
		);
		
		$actual = Tools :: convertPrice(10.00, $currency);
		$this->assertEquals(15.00, $actual);
		
	}
	
	public function testConvertPriceWithCurrencyObject() {
		
		$shop = new Shop();
		$shop->hydrate(array(
			'id_shop' => 1
		));
		
		PrestaShop\Test\Runtime :: context(array(
			'shop' => $shop
		));
		
		PrestaShop\Test\Runtime :: configuration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		$currency = new Currency();
		$currency->id = 2;
		$currency->conversion_rate = 1.500000;
		
		$actual = Tools :: convertPrice(10.00, $currency);
		$this->assertEquals(15.00, $actual);
		
	}
	
}