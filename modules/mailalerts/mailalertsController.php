<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 11894 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ModulemailalertsController extends ModuleController
{
	/**
	 * @var int
	 */
	public $id_product;

	public function init()
	{
		parent::init();

		require_once(dirname(__FILE__).'/MailAlert.php');
		$this->id_product = (int)Tools::getValue('id_product');
	}

	public function postProcess()
	{
		if ($this->process == 'remove')
			$this->processRemove();
		else if ($this->process == 'add')
			$this->processAdd();
		else if ($this->process == 'check')
			$this->processCheck();
	}

	/**
	 * Remove a favorite product
	 */
	public function processRemove()
	{
		// check if product exists
		$product = new Product($this->id_product);
		if (!Validate::isLoadedObject($product))
			die('0');

		$mailAlert = MailAlert::getMailAlert((int)Context::getContext()->customer->id, (int)$product->id);
		if ($mailAlert && $mailAlert->delete())
			die('0');
		die(1);
	}

	/**
	 * Add a favorite product
	 */
	public function processAdd()
	{
		if (Context::getContext()->customer->isLogged())
		{
		    $id_customer = (int)Context::getContext()->customer->id;
		    $customer = new Customer($id_customer);
		    $customer_email = strval($customer->email);
		}
		else
		{
		    $customer_email = strval(Tools::getValue('customer_email'));
		    $customer = Context::getContext()->customer->getByEmail($customer_email);
		    $id_customer = (isset($customer->id) && ($customer->id != null)) ? (int)$customer->id : null;
		}
		
		$id_product = (int)Tools::getValue('id_product');
		$id_product_attribute = (int)Tools::getValue('id_product_attribute');
		$id_shop = (int)Context::getContext()->shop->id;
		$product = new Product($id_product, null, null, $id_shop, Context::getContext());

		$mailAlert = MailAlert::customerHasNotification($id_customer, $id_product, $id_product_attribute, $id_shop);

		if ($mailAlert)
		    die('2');
		else if (!Validate::isLoadedObject($product))
		    die('0');

		$mailAlert = new MailAlert();

		$mailAlert->id_customer = (int)$id_customer;
		$mailAlert->customer_email = strval($customer_email);
		$mailAlert->id_product = (int)$id_product;
		$mailAlert->id_product_attribute = (int)$id_product_attribute;
		$mailAlert->id_shop = (int)$id_shop;

		if ($mailAlert->add() !== false)
			die('1');
		
		die('0');
	}

	/**
	 * Add a favorite product
	 */
	public function processCheck()
	{
		if (!(int)$this->context->customer->logged)
			die('0');

		$id_customer = (int)$this->context->customer->id;
		
		if (!$id_product = (int)(Tools::getValue('id_product')))
			die ('0');
		$id_product_attribute = (int)(Tools::getValue('id_product_attribute'));
		$id_shop = (int)Context::getContext()->shop->id;

		if (MailAlert::customerHasNotification((int)$id_customer, (int)$id_product, (int)$id_product_attribute, (int)$id_shop))
			die ('1');
		
		die('0');
	}

	public function initContent()
	{
		parent::initContent();
		if ($this->process == 'account')
			$this->assignAccount();
	}

	/**
	 * Prepare account page
	 */
	public function assignAccount()
	{
		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=auth&redirect=module&module=mailalerts&action=account');

		if (Context::getContext()->customer->id)
		{
			$this->context->smarty->assign('id_customer', Context::getContext()->customer->id);
			$this->context->smarty->assign('mailAlerts', MailAlert::getMailAlerts((int)Context::getContext()->customer->id, (int)Context::getContext()->language->id));
			
			$this->setTemplate('mailalerts-account.tpl');
		}
	}
}