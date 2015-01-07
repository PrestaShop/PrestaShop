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

class	ConfigurationCoreTest extends PrestaShopPHPUnit
{
	protected function setUp()
	{
		$configuration = array();
		$id_shops = array(1, 2);
		$id_shop_groups = array(1, 2);
		$id_langs = array(0, 1, 2);
		foreach ($id_langs as $id_lang)
		{
			$configuration['configuration'][$id_lang] = array(
				'global' => array(),
				'group' => array(),
				'shop' => array()
			);

			foreach ($id_shop_groups as $id_group)
				$configuration['configuration'][$id_lang]['group'][$id_group] = array();
			foreach ($id_shops as $id_shop)
				$configuration['configuration'][$id_lang]['shop'][$id_shop] = array();

		}

		$configuration['configuration'][0]['global']['PS_TEST_NOT_OVERRIDDEN'] = 'RESULT_NOT_OVERRIDDEN';

		$configuration['configuration'][0]['global']['PS_TEST_GROUP_OVERRIDDEN'] = 'RESULT_GROUP_OVERRIDDEN';
		foreach ($id_shop_groups as $id_group)
				$configuration['configuration'][0]['group'][$id_group]['PS_TEST_GROUP_OVERRIDDEN'] = 'RESULT_GROUP_OVERRIDDEN_'.$id_group;

		$configuration['configuration'][0]['global']['PS_TEST_SHOP_OVERRIDDEN'] = 'RESULT_SHOP_OVERRIDDEN';
		foreach ($id_shops as $id_shop)
				$configuration['configuration'][0]['shop'][$id_shop]['PS_TEST_SHOP_OVERRIDDEN'] = 'RESULT_SHOP_OVERRIDDEN_'.$id_shop;

		$configuration['configuration'][0]['global']['PS_TEST_GROUP_SHOP_OVERRIDDEN'] = 'RESULT_GROUP_SHOP_OVERRIDDEN';
		foreach ($id_shop_groups as $id_group)
				$configuration['configuration'][0]['group'][$id_group]['PS_TEST_GROUP_SHOP_OVERRIDDEN'] = 'RESULT_GROUP_SHOP_OVERRIDDEN_GROUP_'.$id_group;
		foreach ($id_shops as $id_shop)
				$configuration['configuration'][0]['shop'][$id_shop]['PS_TEST_GROUP_SHOP_OVERRIDDEN'] = 'RESULT_GROUP_SHOP_OVERRIDDEN_SHOP_'.$id_shop;				

		$this->setProperty(null, '_cache', $configuration);
	}

	public function testGetGlobalValue()
	{
		$this->assertEquals('RESULT_NOT_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_NOT_OVERRIDDEN'));
		$this->assertEquals('RESULT_GROUP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_GROUP_OVERRIDDEN'));
		$this->assertEquals('RESULT_SHOP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_SHOP_OVERRIDDEN'));
		$this->assertEquals('RESULT_GROUP_SHOP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_GROUP_SHOP_OVERRIDDEN'));
		$this->assertFalse(Configuration::getGlobalValue('PS_TEST_DOES_NOT_EXIST'));
	}
}
