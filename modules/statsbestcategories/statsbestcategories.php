<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsBestCategories extends ModuleGrid
{
	private $_html;
	private $_query;
	private $_columns;
	private $_defaultSortColumn;
	private $_defaultSortDirection;
	private $_emptyMessage;
	private $_pagingMessage;

	public function __construct()
	{
		$this->name = 'statsbestcategories';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->_defaultSortColumn = 'totalPriceSold';
		$this->_defaultSortDirection = 'DESC';
		$this->_emptyMessage = $this->l('Empty recordset returned');
		$this->_pagingMessage = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->_columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left',
				'width' => 140
			),
			array(
				'id' => 'totalQuantitySold',
				'header' => $this->l('Total Quantity Sold'),
				'dataIndex' => 'totalQuantitySold',
				'width' => 20,
				'align' => 'right'
			),
			array(
				'id' => 'totalPriceSold',
				'header' => $this->l('Total Price'),
				'dataIndex' => 'totalPriceSold',
				'width' => 30,
				'align' => 'right'
			),
			array(
				'id' => 'totalPageViewed',
				'header' => $this->l('Total Viewed'),
				'dataIndex' => 'totalPageViewed',
				'width' => 30,
				'align' => 'right'
			)
		);

		$this->displayName = $this->l('Best categories');
		$this->description = $this->l('A list of the best categories');
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
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
			'.$this->engine($engineParams).'
			<br /><a class="button export-csv" href="'.htmlentities($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a>
		</div>';
		return $this->_html;
	}

	public function getData()
	{
		$dateBetween = $this->getDate();
		$id_lang = $this->getLang();

		// If a shop is selected, get all children categories for the shop
		$categories = array();
		if (Shop::getContext() != Shop::CONTEXT_ALL)
		{
			$sql = 'SELECT c.nleft, c.nright
					FROM '._DB_PREFIX_.'category c
					WHERE c.id_category IN (
						SELECT s.id_category
						FROM '._DB_PREFIX_.'shop s
						WHERE s.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')
					)';
			if ($result = Db::getInstance()->executeS($sql))
			{
				$ntreeRestriction = array();
				foreach ($result as $row)
					$ntreeRestriction[] = '(nleft >= '.$row['nleft'].' AND nright <= '.$row['nright'].')';
				
				if ($ntreeRestriction)
				{
					$sql = 'SELECT id_category
							FROM '._DB_PREFIX_.'category
							WHERE '.implode(' OR ', $ntreeRestriction);
					if ($result = Db::getInstance()->executeS($sql))
					{
						foreach ($result as $row)
							$categories[] = $row['id_category'];
					}
				}
			}
		}

		// Get best categories
		$this->_query = '
		SELECT SQL_CALC_FOUND_ROWS ca.`id_category`, CONCAT(parent.name, \' > \', calang.`name`) as name,
			IFNULL(SUM(t.`totalQuantitySold`), 0) AS totalQuantitySold,
			ROUND(IFNULL(SUM(t.`totalPriceSold`), 0), 2) AS totalPriceSold,
			(
				SELECT IFNULL(SUM(pv.`counter`), 0)
				FROM `'._DB_PREFIX_.'page` p
				LEFT JOIN `'._DB_PREFIX_.'page_viewed` pv ON p.`id_page` = pv.`id_page`
				LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
				LEFT JOIN `'._DB_PREFIX_.'product` pr ON CAST(p.`id_object` AS UNSIGNED INTEGER) = pr.`id_product`
				LEFT JOIN `'._DB_PREFIX_.'category_product` capr2 ON capr2.`id_product` = pr.`id_product`
				WHERE capr.`id_category` = capr2.`id_category`
				AND p.`id_page_type` = 1
				AND dr.`time_start` BETWEEN '.$dateBetween.'
				AND dr.`time_end` BETWEEN '.$dateBetween.'
			) AS totalPageViewed
		FROM `'._DB_PREFIX_.'category` ca
		LEFT JOIN `'._DB_PREFIX_.'category_lang` calang ON (ca.`id_category` = calang.`id_category` AND calang.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('calang').')
		LEFT JOIN `'._DB_PREFIX_.'category_lang` parent ON (ca.`id_parent` = parent.`id_category` AND parent.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('parent').')
		LEFT JOIN `'._DB_PREFIX_.'category_product` capr ON ca.`id_category` = capr.`id_category`
		LEFT JOIN (
			SELECT pr.`id_product`, t.`totalQuantitySold`, t.`totalPriceSold`
			FROM `'._DB_PREFIX_.'product` pr
			LEFT JOIN (
				SELECT pr.`id_product`,
					IFNULL(SUM(cp.`product_quantity`), 0) AS totalQuantitySold,
					IFNULL(SUM(cp.`product_price` * cp.`product_quantity`), 0) / o.conversion_rate AS totalPriceSold
				FROM `'._DB_PREFIX_.'product` pr
				LEFT OUTER JOIN `'._DB_PREFIX_.'order_detail` cp ON pr.`id_product` = cp.`product_id`
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = cp.`id_order`
				WHERE o.valid = 1
				AND o.invoice_date BETWEEN '.$dateBetween.'
				GROUP BY pr.`id_product`
			) t ON t.`id_product` = pr.`id_product`
		) t	ON t.`id_product` = capr.`id_product`
		'.(($categories) ? 'WHERE ca.id_category IN ('.implode(', ', $categories).')' : '').'
		GROUP BY ca.`id_category`
		HAVING ca.`id_category` != 1';
		if (Validate::IsName($this->_sort))
		{
			$this->_query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->_query .= ' '.$this->_direction;
		}
		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->_query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query);
		$this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');
	}
}