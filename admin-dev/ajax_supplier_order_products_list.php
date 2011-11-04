<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
/**
 * Used in AdminSupplierOrdersController in order to retrieves products, given a pattern
 * @since 1.5.0
 */
define('_PS_ADMIN_DIR_', getcwd());
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
/* Gets cookie or logout */
require_once(dirname(__FILE__).'/init.php');

/* @var string Given this pattern, it will return a list of products */
$pattern = pSQL(Tools::getValue('q', false));
if (!$pattern || $pattern == '' || strlen($pattern) < 1)
	die();

/* @var int Supplier id */
$id_supplier = (int)Tools::getValue('id_supplier', false);

/* @var int Lang used */
$id_lang = (int)Context::getContext()->language->id;

/* @var DbQuery query used */
$query = new DbQuery();
$query->select('
	CONCAT(p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\')) as id,
	p.reference,
	p.ean13,
	md5(CONCAT(\''._COOKIE_KEY_.'\', p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\'))) as checksum,
	IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.name, \' - \', al.name SEPARATOR \', \')), pl.name) as name
');
$query->from('product p');
$query->innerJoin('product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang = '.$id_lang.')');
$query->leftJoin('product_attribute pa ON (pa.id_product = p.id_product)');
$query->leftJoin('product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)');
$query->leftJoin('attribute atr ON (atr.id_attribute = pac.id_attribute)');
$query->leftJoin('attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$id_lang.')');
$query->leftJoin('attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$id_lang.')');
$query->where('pl.name LIKE \'%'.$pattern.'%\' OR p.reference LIKE \'%'.$pattern.'%\'');
$query->where('p.id_product NOT IN (SELECT pd.id_product FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))');
if ($id_supplier)
	$query->where('p.id_supplier = '.$id_supplier);
$query->groupBy('pa.id_product_attribute');

$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
if ($items)
	die(Tools::jsonEncode($items));
die();