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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/sendtoafriend.php');
include_once(dirname(__FILE__).'/../../classes/Product.php');

$module = new SendToAFriend();

if (Tools::getValue('action') == 'sendToMyFriend' AND
		Tools::getValue('secure_key') == $module->secure_key
		AND Context::getContext()->cookie->id_customer)
{
		$friendInfos = Tools::jsonDecode(Tools::getValue('friend'));
		$friendName = "";
		$friendMail = "";
		$id_product = null;
		foreach ($friendInfos as $entry)
		{
			if ($entry->key == "friend_name")
				$friendName = $entry->value;
			elseif ($entry->key == "friend_email")
				$friendMail = $entry->value;
			elseif ($entry->key == "id_product")
				$id_product = $entry->value;
		}
		if (!$friendName OR !$friendMail OR !$id_product)
			die('0');

		/* Email generation */
		$product = new Product((int)$id_product, false, Context::getContext()->language->id);
		$productLink = Context::getContext()->link->getProductLink($product);
		$subject = (Context::getContext()->cookie->customer_firstname ? Context::getContext()->cookie->customer_firstname.' '.Context::getContext()->cookie->customer_lastname : $module->l('A friend')).' '.$module->l('sent you a link to').' '.$product->name;
		$templateVars = array(
					'{product}' => $product->name,
					'{product_link}' => $productLink,
					'{customer}' => (Context::getContext()->cookie->customer_firstname ? Context::getContext()->cookie->customer_firstname.' '.Context::getContext()->cookie->customer_lastname : $module->l('A friend')),
					'{name}' => Tools::safeOutput($friendName)
		);

		/* Email sending */
		if (!Mail::Send((int)Context::getContext()->cookie->id_lang,
				'send_to_a_friend',
				Mail::l('A friend sent you a link to', (int)Context::getContext()->cookie->id_lang).' '.$product->name,
				$templateVars, $friendMail,
				NULL,
				(Context::getContext()->cookie->email ? Context::getContext()->cookie->email : NULL),
				(Context::getContext()->cookie->customer_firstname ? Context::getContext()->cookie->customer_firstname.' '.Context::getContext()->cookie->customer_lastname : NULL),
				NULL,
				NULL,
				dirname(__FILE__).'/mails/'))
			die('0');
		die('1');
}
die('1');
