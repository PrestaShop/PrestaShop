<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 7307 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsBestManufacturers extends ModuleGrid
{
	private $_html = null;
	private $_query = null;
	private $_columns = null;
	private $_defaultSortColumn = null;
	private $_defaultSortDirection = null;
	private $_emptyMessage = null;
	private $_pagingMessage = null;

	public function __construct()
	{
		$this->name = 'statsbestmanufacturers';
		$this->tab = 'analytics_stats';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->_defaultSortColumn = 'sales';
		$this->_defaultSortDirection = 'DESC';
		$this->_emptyMessage = $this->l('Empty recordset returned');
		$this->_pagingMessage = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->_columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left',
				'width' => 200
			),
			array(
				'id' => 'quantity',
				'header' => $this->l('Quantity sold'),
				'dataIndex' => 'quantity',
				'width' => 60,
				'align' => 'right'
			),
			array(
				'id' => 'sales',
				'header' => $this->l('Total paid'),
				'dataIndex' => 'sales',
				'width' => 60,
				'align' => 'right'
			)
		);

		$this->displayName = $this->l('Best manufacturers');
		$this->description = $this->l('A list of the best manufacturers');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		$engineParams = array(
			'id' => 'id_category',
			'title' => $this->displayName,
			'columns' => $this->_columns,
			'defaultSortColumn' => $this->_defaultSortColumn,
			'defaultSortDirection' => $this->_defaultSortDirection,
			'emptyMessage' => $this->_emptyMessage,
			'pagingMessage' => $this->_pagingMessage
		);

		if (Tools::getValue('export'))
			$this->csvExport($engineParams);

		$this->_html = '
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			'.$this->engine($engineParams).'
			<br /><a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a>
		</fieldset>';
		return $this->_html;
	}

	public function getTotalCount()
	{
		$sql = 'SELECT COUNT(DISTINCT(m.id_manufacturer))
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
				LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_order = od.id_order)
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				WHERE o.invoice_date BETWEEN '.$this->getDate()
					.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').
					'AND o.valid = 1
					AND m.id_manufacturer IS NOT NULL';
		return Db::getInstance()->getValue($sql);
	}

	public function getData()
	{
		$this->_totalCount = $this->getTotalCount();

		$this->_query = 'SELECT m.name, SUM(od.product_quantity) as quantity, ROUND(SUM(od.product_quantity * od.product_price) / c.conversion_rate, 2) as sales
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
				LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_order = od.id_order)
				LEFT JOIN '._DB_PREFIX_.'currency c ON (c.id_currency = o.id_currency)
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				WHERE '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.invoice_date BETWEEN '.$this->getDate().'
					AND o.valid = 1
					AND m.id_manufacturer IS NOT NULL
				GROUP BY p.id_manufacturer';
		if (Validate::IsName($this->_sort))
		{
			$this->_query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->_query .= ' '.$this->_direction;
		}
		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->_query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance()->executeS($this->_query);
	}
}
