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

/* SSL Management */
$useSSL = true;

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

include_once(dirname(__FILE__).'/LoyaltyModule.php');
include_once(dirname(__FILE__).'/LoyaltyStateModule.php');
include_once(dirname(__FILE__).'/loyalty.php');

Tools::displayFileAsDeprecated();

$context = Context::getContext();
if (!$context->customer->isLogged())
	Tools::redirect('index.php?controller=authentication&back=modules/loyalty/loyalty-program.php');

$context->controller->addJqueryPlugin(array('dimensions', 'cluetip'));

$customerPoints = (int)(LoyaltyModule::getPointsByCustomer((int)($cookie->id_customer)));

/* transform point into voucher if needed */
if (Tools::getValue('transform-points') == 'true' AND $customerPoints > 0)
{
	/* Generate a voucher code */
	$voucherCode = NULL;
	do
		$voucherCode = 'FID'.rand(1000, 100000);
	while (CartRule::cartRuleExists($voucherCode));

	/* Voucher creation and affectation to the customer */
	$cartRule = new CartRule();
	$cartRule->code = $voucherCode;
	$cartRule->id_customer = (int)$context->customer->id;
	$cartRule->id_currency = (int)$context->currency->id;
	$cartRule->reduction_amount = LoyaltyModule::getVoucherValue((int)$customerPoints);
	$cartRule->quantity = 1;
	$cartRule->quantity_per_user = 1;

	/* If merchandise returns are allowed, the voucher musn't be usable before this max return date */
	$dateFrom = Db::getInstance()->getValue('
	SELECT UNIX_TIMESTAMP(date_add) n
	FROM '._DB_PREFIX_.'loyalty
	WHERE id_cart_rule = 0 AND id_customer = '.(int)$cookie->id_customer.'
	ORDER BY date_add DESC');

	if (Configuration::get('PS_ORDER_RETURN'))
		$dateFrom += 60 * 60 * 24 * (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS');

	$cartRule->date_from = date('Y-m-d H:i:s', $dateFrom);
	$cartRule->date_to = date('Y-m-d H:i:s', $dateFrom + 31536000); // + 1 year

	$cartRule->minimum_amount = (float)Configuration::get('PS_LOYALTY_MINIMAL');
	$cartRule->active = 1;

	$categories = Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY');
	if ($categories != '' AND $categories != 0)
		$categories = explode(',', Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY'));
	else
		die (Tools::displayError());

	$languages = Language::getLanguages(true);
	$default_text = Configuration::get('PS_LOYALTY_VOUCHER_DETAILS', (int)Configuration::get('PS_LANG_DEFAULT'));

	foreach ($languages AS $language)
	{
		$text = Configuration::get('PS_LOYALTY_VOUCHER_DETAILS', (int)$language['id_lang']);
		$cartRule->name[(int)$language['id_lang']] = $text ? strval($text) : strval($default_text);
	}

	if (is_array($categories) AND sizeof($categories))
		$cartRule->add(true, false, $categories);
	else
		$cartRule->add();

	/* Register order(s) which contributed to create this voucher */
	LoyaltyModule::registerDiscount($cartRule);

	Tools::redirect('modules/loyalty/loyalty-program.php');
}

include(dirname(__FILE__).'/../../header.php');

$orders = LoyaltyModule::getAllByIdCustomer((int)($cookie->id_customer), (int)($cookie->id_lang));
$displayorders = LoyaltyModule::getAllByIdCustomer((int)($cookie->id_customer), (int)($cookie->id_lang), false, true, ((int)(Tools::getValue('n')) > 0 ? (int)(Tools::getValue('n')) : 10), ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1));
$smarty->assign(array(
	'orders' => $orders,
	'displayorders' => $displayorders,
	'pagination_link' => __PS_BASE_URI__.'modules/loyalty/loyalty-program.php',
	'totalPoints' => (int)$customerPoints,
	'voucher' => LoyaltyModule::getVoucherValue($customerPoints, (int)$context->currency->id),
	'validation_id' => LoyaltyStateModule::getValidationId(),
	'transformation_allowed' => $customerPoints > 0,
	'page' => ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1),
	'nbpagination' => ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10),
	'nArray' => array(10, 20, 50),
	'max_page' => floor(sizeof($orders) / ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10))
));

/* Discounts */
$nbDiscounts = 0;
$discounts = array();
if ($ids_discount = LoyaltyModule::getDiscountByIdCustomer((int)$cookie->id_customer))
{
	$nbDiscounts = count($ids_discount);
	foreach ($ids_discount AS $key => $discount)
	{
		$discounts[$key] = new Discount((int)$discount['id_cart_rule'], (int)$cookie->id_lang);
		$discounts[$key]->orders = LoyaltyModule::getOrdersByIdDiscount((int)$discount['id_cart_rule']);
	}
}

$allCategories = Category::getSimpleCategories((int)($cookie->id_lang));
$voucherCategories = Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY');
if ($voucherCategories != '' AND $voucherCategories != 0)
	$voucherCategories = explode(',', Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY'));
else
	die(Tools::displayError());

if (sizeof($voucherCategories) == sizeof($allCategories))
	$categoriesNames = null;
else
{
	$categoriesNames = array();
	foreach ($allCategories AS $k => $allCategory)
		if (in_array($allCategory['id_category'], $voucherCategories))
			$categoriesNames[$allCategory['id_category']] = trim($allCategory['name']);
	if (!empty($categoriesNames))
		$categoriesNames = Tools::truncate(implode(', ', $categoriesNames), 100).'.';
	else
		$categoriesNames = null;
}
$smarty->assign(array(
	'nbDiscounts' => (int)$nbDiscounts,
	'discounts' => $discounts,
	'minimalLoyalty' => (float)Configuration::get('PS_LOYALTY_MINIMAL'),
	'categories' => $categoriesNames));

$loyalty = new Loyalty();
echo $loyalty->display(dirname(__FILE__).'/loyalty.php', 'loyalty.tpl');

include(dirname(__FILE__).'/../../footer.php');
