<?php


namespace PrestaShop\PrestaShop\Tests\Unit\Classes\Tax;

use Address;
use Phake;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Tax;
use TaxCalculator;
use TaxRulesTaxManager;

class TaxRulesTaxManagerCoreTest extends UnitTestCase {

	private $tax_rows = array(
		array(
			'id_tax' => 1,
			'behavior' => TaxCalculator::COMBINE_METHOD,
			'rate' => 20.6
		),
		array(
			'id_tax' => 2,
			'behavior' => TaxCalculator::ONE_AFTER_ANOTHER_METHOD,
			'rate' => 5.5
		)
	);

	public function test_getTaxCalculator_ShouldUseFirstComputationMethodFromTaxes()
	{
		// Given
		Phake::when($this->database)->executeS(Phake::anyParameters())->thenReturn($this->tax_rows);

		foreach($this->tax_rows as $field_values)
		{
			$tax = new Tax();
			$tax->id = $field_values['id_tax'];
			$tax->rate = $field_values['rate'];
			$this->entity_mapper->willReturn($tax)->forId($tax->id);
		}

		$fake_configuration = $this->setConfiguration(array(
			'PS_TAX' => 1
		));

		$tax_rules_tax_manager = new TaxRulesTaxManager(new Address(), null, $fake_configuration);

		//When
		$tax_calculator = $tax_rules_tax_manager->getTaxCalculator();

		// Then
		$this->assertEquals(TaxCalculator::COMBINE_METHOD, $tax_calculator->computation_method);
		$this->assertTrue(is_array($tax_calculator->taxes));

		foreach($tax_calculator->taxes as $key => $tax){
			$this->assertTrue($tax instanceof Tax);
			$this->assertEquals($this->tax_rows[$key]['id_tax'], $tax->id);
			$this->assertEquals($this->tax_rows[$key]['rate'], $tax->rate);
		}
	}

}
