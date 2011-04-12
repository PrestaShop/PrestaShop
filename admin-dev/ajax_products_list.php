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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('PS_ADMIN_DIR', getcwd());
include(PS_ADMIN_DIR.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(dirname(__FILE__).'/init.php');

$query = Tools::getValue('q', false);
if (!$query OR $query == '' OR strlen($query) < 1)
	die();

/*
 * In the SQL request the "q" param is used entirely to match result in database.
 * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list, 
 * they are no return values just because string:"(ref : #ref_pattern#)" 
 * is not write in the name field of the product.
 * So the ref pattern will be cut for the search request.
 */
if($pos = strpos($query, ' (ref:'))
	$query = substr($query, 0, $pos);

$excludeIds = Tools::getValue('excludeIds', false);
if ($excludeIds && $excludeIds != 'NaN')
	$excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
else
	$excludeIds = '';
$items = Db::getInstance()->ExecuteS('
SELECT p.`id_product`, `reference`, pl.name
FROM `'._DB_PREFIX_.'product` p
LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product)
WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\') AND pl.id_lang = '.(int)($cookie->id_lang).
(!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ''));

if ($items)
	foreach ($items AS $item)
		echo trim($item['name']).(!empty($item['reference']) ? ' (ref: '.$item['reference'].')' : '').'|'.(int)($item['id_product'])."\n";
