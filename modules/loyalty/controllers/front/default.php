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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class LoyaltyDefaultModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function __construct()
	{
		parent::__construct();

		include_once($this->module->getLocalPath().'LoyaltyModule.php');
		include_once($this->module->getLocalPath().'LoyaltyStateModule.php');
		
		// Declare smarty function to render pagination link
		smartyRegisterFunction($this->context->smarty, 'function', 'summarypaginationlink', array('LoyaltyDefaultModuleFrontController', 'getSummaryPaginationLink'));
	}

	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::getValue('process') == 'transformpoints')
			$this->processTransformPoints();
	}
	
	/**
	 * Transform loyalty point to a voucher
	 */
	public function processTransformPoints()
	{
		$customerPoints = (int)LoyaltyModule::getPointsByCustomer((int)$this->context->customer->id);
		if ($customerPoints > 0)
		{
			/* Generate a voucher code */
			$voucherCode = null;
			do
				$voucherCode = 'FID'.rand(1000, 100000);
			while (CartRule::cartRuleExists($voucherCode));
		
			// Voucher creation and affectation to the customer
			$cartRule = new CartRule();
			$cartRule->code = $voucherCode;
			$cartRule->id_customer = (int)$this->context->cookie->id_customer;
			$cartRule->id_currency = (int)$this->context->cookie->id_currency;
			$cartRule->reduction_amount = LoyaltyModule::getVoucherValue((int)$customerPoints);
			$cartRule->quantity = 1;
			$cartRule->quantity_per_user = 1;
		
			// If merchandise returns are allowed, the voucher musn't be usable before this max return date
			$dateFrom = Db::getInstance()->getValue('
			SELECT UNIX_TIMESTAMP(date_add) n
			FROM '._DB_PREFIX_.'loyalty
			WHERE id_cart_rule = 0 AND id_customer = '.(int)$this->context->cookie->id_customer.'
			ORDER BY date_add DESC');
		
			if (Configuration::get('PS_ORDER_RETURN'))
				$dateFrom += 60 * 60 * 24 * (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS');
		
			$cartRule->date_from = date('Y-m-d H:i:s', $dateFrom);
			$cartRule->date_to = date('Y-m-d H:i:s', $dateFrom + 31536000); // + 1 year
		
			$cartRule->minimum_amount = (float)Configuration::get('PS_LOYALTY_MINIMAL');
			$cartRule->active = 1;
		
			$categories = Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY');
			if ($categories != '' && $categories != 0)
				$categories = explode(',', Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY'));
			else
				die (Tools::displayError());
		
			$languages = Language::getLanguages(true);
			$default_text = Configuration::get('PS_LOYALTY_VOUCHER_DETAILS', (int)Configuration::get('PS_LANG_DEFAULT'));
		
			foreach ($languages as $language)
			{
				$text = Configuration::get('PS_LOYALTY_VOUCHER_DETAILS', (int)$language['id_lang']);
				$cartRule->name[(int)$language['id_lang']] = $text ? strval($text) : strval($default_text);
			}
		
			if (is_array($categories) && count($categories))
				$cartRule->add(true, false, $categories);
			else
				$cartRule->add();
		
			// Register order(s) which contributed to create this voucher
			LoyaltyModule::registerDiscount($cartRule);
			
			Tools::redirect($this->context->link->getModuleLink('loyalty', 'default'));
		}
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
		$this->context->controller->addJqueryPlugin(array('dimensions', 'cluetip'));
		
		if (Tools::getValue('process') == 'summary')
			$this->assignSummaryExecution();
	}
	
	/**
	 * Render pagination link for summary
	 *
	 * @param (array) $params Array with to parameters p (for page number) and n (for nb of items per page)
	 * @return string link
	 */
	public static function getSummaryPaginationLink($params, &$smarty)
	{
		if (!isset($params['p']))
			$p = 1;
		else
			$p = $params['p'];
			
		if (!isset($params['n']))
			$n = 10;
		else
			$n = $params['n'];
		
		return Context::getContext()->link->getModuleLink(
			'loyalty',
			'default',
			array(
				'process' => 'summary',
				'p' => $p,
				'n' => $n,
			)
		);
	}
	
	/**
	 * Assign summary template
	 */
	public function assignSummaryExecution()
	{
		$customerPoints = (int)(LoyaltyModule::getPointsByCustomer((int)($this->context->customer->id)));
		$orders = LoyaltyModule::getAllByIdCustomer((int)($this->context->cookie->id_customer), (int)($this->context->cookie->id_lang));
		$displayorders = LoyaltyModule::getAllByIdCustomer((int)($this->context->cookie->id_customer), (int)($this->context->cookie->id_lang), false, true, ((int)(Tools::getValue('n')) > 0 ? (int)(Tools::getValue('n')) : 10), ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1));
		$this->context->smarty->assign(array(
			'orders' => $orders,
			'displayorders' => $displayorders,
			'totalPoints' => (int)$customerPoints,
			'voucher' => LoyaltyModule::getVoucherValue($customerPoints, (int)($this->context->cookie->id_currency)),
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
		if ($ids_discount = LoyaltyModule::getDiscountByIdCustomer((int)$this->context->cookie->id_customer))
		{
			$nbDiscounts = count($ids_discount);
			foreach ($ids_discount as $key => $discount)
			{
				$discounts[$key] = new CartRule((int)$discount['id_cart_rule'], (int)$this->context->cookie->id_lang);
				$discounts[$key]->orders = LoyaltyModule::getOrdersByIdDiscount((int)$discount['id_cart_rule']);
			}
		}
		
		$allCategories = Category::getSimpleCategories((int)($this->context->cookie->id_lang));
		$voucherCategories = Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY');
		if ($voucherCategories != '' && $voucherCategories != 0)
			$voucherCategories = explode(',', Configuration::get('PS_LOYALTY_VOUCHER_CATEGORY'));
		else
			die(Tools::displayError());
		
		if (count($voucherCategories) == count($allCategories))
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
		$this->context->smarty->assign(array(
			'nbDiscounts' => (int)$nbDiscounts,
			'discounts' => $discounts,
			'minimalLoyalty' => (float)Configuration::get('PS_LOYALTY_MINIMAL'),
			'categories' => $categoriesNames));

		$this->setTemplate('loyalty.tpl');
	}
}
