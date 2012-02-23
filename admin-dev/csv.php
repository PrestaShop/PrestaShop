<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */

define('_PS_ADMIN_DIR_', getcwd());
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

if (!Context::getContext()->employee->id)
	Tools::redirectAdmin('index.php?controller=AdminLogin');

/*
 * Functions allowed
 */
$functions = array(
	'id_supply_order' => 'exportSupplyOrder',
);

/*
 * Depending on $_GET, call the corresponding function
 */
foreach ($functions as $var => $function)
{
	if (isset($_GET[$var]))
	{
		call_user_func($function);
		die;
	}
}

/**
 * Function used for SupplyOrder
 */
function exportSupplyOrder()
{
	//@TODO Checks if employee has enough access to export
	if (!isset($_GET['id_supply_order']))
		die (Tools::displayError('Missing supply order ID'));

	$id_supply_order = (int)$_GET['id_supply_order'];
	$supply_order = new SupplyOrder($id_supply_order);

	if (!Validate::isLoadedObject($supply_order))
		die(Tools::displayError('Cannot find this supply order in the database'));

    $details = $supply_order->getEntriesCollection($supply_order->id_lang);
    exportCSV($details, 'supply_order_'.$supply_order->reference.'_details');
}

/**
 * Main function to export CSV
 * @param object|Array $object
 * @param string $template
 */
function exportCSV(&$object, $filename)
{
    $csv = new CSV($object, $filename);
    $csv->export();
}
