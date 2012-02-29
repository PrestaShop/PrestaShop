<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
ing*
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9841 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAccountingRegisteredNumberControllerCore extends AdminController
{
	public $account_number_list = array();

	public function __construct()
	{
		parent::__construct();
		
		$this->className = 'Accounting';
		$id_lang = $this->context->language->id;

		// p. => main table, s. => join table
		// Contains rules to build sql query
		$this->account_number_list = array(
			// Product definition
			'product' => array(
				'table' => 'accounting_product_zone_shop',
				'fields' => array(
					'p.account_number' => $this->l('Account number'),
					'COUNT(*) AS total' => $this->l('Number of product associated to this account')
				),
				'group_by' => 'account_number',
				'condition' => 'p.account_number <> ""',
				'title' => $this->l('Product account number list'),
				'list' => array()),

			// Taxes definition
			'taxes' => array(
				'table' => 'tax',
				'fields' => array(
					'account_number' => $this->l('Account number'),
					'COUNT(*) AS total' => $this->l('Number of taxes associated to this account')
				),
				'group_by' => 'account_number',
				'condition' => 'account_number <> ""',
				'title' => $this->l('Taxes Account number list'),
				'list' => array()),

			// Gift wrapping definition, for now Only one available using Configuration
			'gift_wrapping' => array(
				'table' => '',
				'fields' => array(
					'account_number' => $this->l('Account number'),
					'total' => $this->l('Number of gift-wrapping associated to this account')
				),
				'condition' => 'account_number <> ""',
				'group_by' => 'account_number',
				'title' => $this->l('Gift wrapping account number list'),
				'list' => array()),

			// Customer definition
			'customer' => array(
				'table' => 'customer',
				'fields' => array(
					'account_number' => $this->l('Account number'),
					'firstname' => $this->l('First name'),
					'lastname' => $this->l('Last name')
				),
				'group_by' => 'account_number',
				'condition' => 'account_number <> ""',
				'title' => $this->l('Customer account number list'),
				'list' => array()),

			// Zone shop definition
			'zone_shop' => array(
				'table' => 'accounting_zone_shop',
				'fields' => array(
					'p.account_number' => $this->l('Account number'),
					'COUNT(*) AS total' => $this->l('Number of zone associated to this account')
				),
				'group_by' => 'account_number',
				'condition' => 'account_number <> ""',
				'title' => $this->l('Zone shop account number list'),
				'list' => array())
		);
	}

	public function initToolbar()
	{
		$this->initToolbarTitle();
		$this->toolbar_btn = array();

	}

	public function initContent()
	{
		$this->initToolbar();
		$this->initAccountNumberList();
	}

	public function initAccountNumberList()
	{
		foreach ($this->account_number_list as $name => &$detail)
		{
			if (isset($detail['table']) && !empty($detail['table']))
			{
				$join = '';

				if (isset($detail['left_join']))
				{
					$join = 'LEFT JOIN '._DB_PREFIX_.$detail['left_join']['table'].' s ON (';
					foreach ($detail['left_join']['on'] as $on)
						$join .= 'p.'.$on.' = s.'.$on.' AND ';
					$join = rtrim($join, '  AND ').')';
				}

				$query = 'SELECT '.implode(', ', array_keys($detail['fields'])).'
					FROM `'._DB_PREFIX_.$detail['table'].'` p '.$join.'
					WHERE '.$detail['condition'];

				if (isset($detail['group_by']))
					$query .= ' GROUP BY '.$detail['group_by'];
				
				$detail['list'] = Db::getInstance()->executeS($query);
			}
		}

		$num = Configuration::get('PS_GIFT_WRAPPING_ACCOUNT_NUMBER');
		if (!empty($num))
			$this->account_number_list['gift_wrapping']['list'][] = array($num, '1');

		$this->context->smarty->assign(array(
			'toolbar_btn' => $this->toolbar_btn,
			'title' => $this->l('Accounting Plan'),
			'account_number_list' => $this->account_number_list));

		parent::initContent();
	}
}
