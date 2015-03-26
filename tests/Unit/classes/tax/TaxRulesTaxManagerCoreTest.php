<?php


namespace PrestaShop\PrestaShop\Tests\Unit\Classes\Tax;


use Address;
use ConfigurationManager;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Tax;
use TaxCalculator;
use TaxRulesTaxManager;

class TaxRulesTaxManagerCoreTest extends UnitTestCase {
	/**
	 * @var ConfigurationManager
	 */
	private $configurationManager;

	public function test_Exploratory()
	{

		$rows = array(
			array(
				'id_tax' => 1,
				'behavior' => TaxCalculator::COMBINE_METHOD
			),
			array(
				'id_tax' => 2,
				'behavior' => TaxCalculator::ONE_AFTER_ANOTHER_METHOD
			)
		);

		$this->setUpCommonStaticMocks();

		$this->database->method('executeS')
			->willReturn($rows);

		$this->cache->method('store')
			->with('objectmodel_Tax_1_0_0')
			->willReturn(array());
		$this->cache->method('isStored')
			->with('objectmodel_Tax_1_0_0')
			->willReturn(true);

		$this->configurationManager = $this->getMockBuilder('ConfigurationManager')->getMock();
		$this->configurationManager->method('get')
			->with('PS_TAX')
			->willReturn('1');

		$address = new Address();
		$taxRulesTaxManager = new TaxRulesTaxManager($address, null, $this->configurationManager);

		$taxCalculator = $taxRulesTaxManager->getTaxCalculator();

		$this->assertEquals(TaxCalculator::COMBINE_METHOD, $taxCalculator->computation_method);
		$this->assertTrue(is_array($taxCalculator->taxes));

		foreach($taxCalculator->taxes as $key => $tax){
			$this->assertTrue($tax instanceof Tax);
			// TODO Separate Database load responsibility from ObjectModel __construct
//			$this->assertEquals($rows[$key]['id_tax'], $tax->id);
		}

		$this->tearDownCommonStaticMocks();
	}

}
