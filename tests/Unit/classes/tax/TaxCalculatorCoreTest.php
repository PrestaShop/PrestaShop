<?php


namespace PrestaShop\PrestaShop\Tests\Unit\Classes\Tax;


use PHPUnit_Framework_TestCase;
use Tax;
use TaxCalculator;

class TaxCalculatorCoreTest extends PHPUnit_Framework_TestCase {

	public function test_getTotalRate_OK()
	{
		$tax = new Tax();
		$tax->rate = 20.6;
		$tax2 = new Tax();
		$tax2->rate = 5.5;

		$tax_calculator = new TaxCalculator(array(
			$tax,$tax2
		), TaxCalculator::COMBINE_METHOD);

		$totalRate = $tax_calculator->getTotalRate();

		$this->assertEquals(26.1, $totalRate);
	}

	public function test_getTotalRate_Bug()
	{
		$tax = new Tax();
		$tax->rate = 20.6;
		$tax2 = new Tax();
		$tax2->rate = 5.5;

		$tax_calculator = new TaxCalculator(array(
			$tax,$tax2
		), TaxCalculator::ONE_AFTER_ANOTHER_METHOD);

		$totalRate = $tax_calculator->getTotalRate();

		$this->assertEquals(27.233, $totalRate);
	}
}
