<?php


namespace PrestaShop\PrestaShop\Tests\Unit\Classes\Tax;


use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use AverageTaxOfProductsTaxCalculator;
use Phake;

class AverageTaxOfProductsTaxCalculatorTest extends UnitTestCase
{
	public function test_tax_is_split_according_to_share_of_each_tax_rate()
	{
		$db = Phake::mock('Core_Foundation_Database_Database');
		$configuration = Phake::mock('Core_Business_Configuration');

		$taxCalculator = new AverageTaxOfProductsTaxCalculator($db, $configuration);

		Phake::when($db)->select(Phake::anyParameters())->thenReturn(array(
			array('id_tax' => 1, 'rate' => 10, 'total_price_tax_excl' => 20),
			array('id_tax' => 2, 'rate' => 20, 'total_price_tax_excl' => 10)
		));

		$amounts = $taxCalculator->getTaxesAmount(7, null, 2, PS_ROUND_HALF_UP);


		$expected = array(
			1 => round(7 * 20  / (20 + 10) * 0.1, 2),
			2 => round(7 * 10  / (20 + 10) * 0.2, 2)
		);

		$this->assertEquals($expected, $amounts);
	}
}
