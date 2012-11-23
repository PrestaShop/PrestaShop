<?php 

use PrestaShop\Test\Utils as PrestaShop;

class ToolsTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		
		define('_DB_SERVER_', 	'');
		define('_DB_NAME_', 	'');
		define('_DB_USER_', 	'');
		define('_DB_PASSWD_', 	'');
		define('_DB_PREFIX_', 	'');
		define('_PS_DEBUG_SQL_', 0);
		
		PrestaShop\MockUtils :: mockDb();
		
	}
	
	public function testConvertPriceWithOneParameterAndDefaultCurrency() {
		
		PrestaShop\MockUtils :: mockConfiguration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		PrestaShop\MockUtils :: mockContext(array(
			'currency' => array(
				'id_currency' 		=> 1,
				'conversion_rate'	=> 1.000000
			)
		));
		
		$actual = Tools :: convertPrice(10.00);
		
		$this->assertEquals(10.00, $actual);
		
	}
	
	public function testConvertPriceWithOneParameter() {
		
		PrestaShop\MockUtils :: mockConfiguration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		PrestaShop\MockUtils :: mockContext(array(
			'currency' => array(
				'id_currency' 		=> 2,
				'conversion_rate'	=> 1.500000
			)
		));
		
		$actual = Tools :: convertPrice(10.00);
		
		$this->assertEquals(15.00, $actual);
		
	}
	
	public function testConvertPriceWithCurrencyArray() {
		
		PrestaShop\MockUtils :: mockConfiguration(array(
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
		
		PrestaShop\MockUtils :: mockContext(array(
			'shop' => $shop
		));
		
		PrestaShop\MockUtils :: mockConfiguration(array(
			'PS_CURRENCY_DEFAULT' => 1
		));
		
		$currency = new Currency();
		$currency->id = 2;
		$currency->conversion_rate = 1.500000;
		
		$actual = Tools :: convertPrice(10.00, $currency);
		
		$this->assertEquals(15.00, $actual);
		
	}
	
}