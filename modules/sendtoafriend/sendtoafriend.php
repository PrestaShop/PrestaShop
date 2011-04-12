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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class sendToAFriend extends Module
{
 	function __construct()
 	{
 	 	$this->name = 'sendtoafriend';
 	 	$this->version = '1.1';
		$this->author = 'PrestaShop';
 	 	$this->tab = 'front_office_features';

		parent::__construct();

		$this->displayName = $this->l('Send to a Friend module');
		$this->description = $this->l('Allows customers to send a product link to a friend.');
 	}

	function install()
	{
	 	if (!parent::install() OR !$this->registerHook('extraLeft'))
	 		return false;
		return true;
	}

	function hookExtraLeft($params)
	{
		global $smarty;
		$smarty->assign('this_path', $this->_path);
		return $this->display(__FILE__, 'product_page.tpl');
	}

	public function displayFrontForm()
	{
		global $smarty;
		$error = false;
		$confirm = false;

		if (isset($_POST['submitAddtoafriend']))
		{
			global $cookie, $link;
			/* Product informations */
			$product = new Product((int)(Tools::getValue('id_product')), false, (int)($cookie->id_lang));
			$productLink = $link->getProductLink($product);

			/* Fields verifications */
			if (empty($_POST['email']) OR empty($_POST['name']))
				$error = $this->l('You must fill in all fields.');
			elseif (empty($_POST['email']) OR !Validate::isEmail($_POST['email']))
				$error = $this->l('The e-mail given is invalid.');
			elseif (!Validate::isName($_POST['name']))
				$error = $this->l('The name given is invalid.');
			elseif (!isset($_GET['id_product']) OR !is_numeric($_GET['id_product']))
				$error = $this->l('An error occurred during the process.');
			else
			{
				/* Email generation */
				$subject = ($cookie->customer_firstname ? $cookie->customer_firstname.' '.$cookie->customer_lastname : $this->l('A friend')).' '.$this->l('sent you a link to').' '.$product->name;
				$templateVars = array(
					'{product}' => $product->name,
					'{product_link}' => $productLink,
					'{customer}' => ($cookie->customer_firstname ? $cookie->customer_firstname.' '.$cookie->customer_lastname : $this->l('A friend')),
					'{name}' => Tools::safeOutput($_POST['name'])
				);

				/* Email sending */
				if (!Mail::Send((int)($cookie->id_lang), 'send_to_a_friend', Mail::l('A friend sent you a link to').' '.$product->name, $templateVars, $_POST['email'], NULL, ($cookie->email ? $cookie->email : NULL), ($cookie->customer_firstname ? $cookie->customer_firstname.' '.$cookie->customer_lastname : NULL), NULL, NULL, dirname(__FILE__).'/mails/'))
					$error = $this->l('An error occurred during the process.');
				else
					Tools::redirect(_MODULE_DIR_.'/'.$this->name.'/sendtoafriend-form.php?id_product='.$product->id.'&submited');
			}
		}
		else
		{
			global $cookie, $link;
			/* Product informations */
			$product = new Product((int)(Tools::getValue('id_product')), false, (int)($cookie->id_lang));
			$productLink = $link->getProductLink($product);
		}

		/* Image */
		$images = $product->getImages((int)($cookie->id_lang));
		foreach ($images AS $k => $image)
			if ($image['cover'])
			{
				$cover['id_image'] = (int)($product->id).'-'.(int)($image['id_image']);
				$cover['legend'] = $image['legend'];
			}

		if (!isset($cover))
			$cover = array('id_image' => Language::getIsoById((int)($cookie->id_lang)).'-default', 'legend' => 'No picture');

		$smarty->assign(array(
			'cover' => $cover,
			'errors' => $error,
			'confirm' => $confirm,
			'product' => $product,
			'productLink' => $productLink
		));

		return $this->display(__FILE__, 'sendtoafriend.tpl');
	}
}

