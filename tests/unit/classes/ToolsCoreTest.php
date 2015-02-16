<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PHPUnit_Framework_TestCase;
use Tools;

class ToolsCoreTest extends PHPUnit_Framework_TestCase
{
	private function setPostAndGet(array $post = array(), array $get = array())
	{
		global $_POST;
		global $_GET;

		$_POST = $post;
		$_GET = $get;

		return $this;
	}

	public function testGetValueBaseCase()
	{
		$this->setPostAndGet(array('hello' => 'world'));
		$this->assertEquals('world', Tools::getValue('hello'));
	}

	public function testGetValueDefaultValueIsFalse()
	{
		$this->setPostAndGet();
		$this->assertEquals(false, Tools::getValue('hello'));
	}

	public function testGetValueUsesDefaultValue()
	{
		$this->setPostAndGet();
		$this->assertEquals('I AM DEFAULT', Tools::getValue('hello', 'I AM DEFAULT'));
	}

	public function testGetValuePrefersPost()
	{
		$this->setPostAndGet(array('hello' => 'world'), array('hello' => 'cruel world'));
		$this->assertEquals('world', Tools::getValue('hello'));
	}

	public function testGetValueAcceptsOnlyTruthyStringsAsKeys()
	{
		$this->setPostAndGet(array(
			'' => true,
			' ' => true,
			null => true
		));

		$this->assertEquals(false, Tools::getValue('', true));
		$this->assertEquals(true, Tools::getValue(' '));
		$this->assertEquals(false, Tools::getValue(null, true));
	}

	public function testGetValueStripsNullCharsFromReturnedStringsExamples()
	{
		return array(
			array("\0", ''),
			array("haxx\0r", 'haxxr'),
			array("haxx\0\0\0r", 'haxxr'),
		);
	}

	/**
	 * @dataProvider testGetValueStripsNullCharsFromReturnedStringsExamples
	 */
	public function testGetValueStripsNullCharsFromReturnedStrings($rawString, $cleanedString)
	{
		/**
		 * Check it cleans values stored in POST
		 */
		$this->setPostAndGet(array('rawString' => $rawString));
		$this->assertEquals($cleanedString, Tools::getValue('rawString'));

		/**
		 * Check it cleans values stored in GET
		 */
		$this->setPostAndGet(array(), array('rawString' => $rawString));
		$this->assertEquals($cleanedString, Tools::getValue('rawString'));

		/**
		 * Check it cleans default values too
		 */
		$this->setPostAndGet();
		$this->assertEquals($cleanedString, Tools::getValue('NON EXISTING KEY', $rawString));
	}
}
