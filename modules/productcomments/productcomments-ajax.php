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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/ProductComment.php');

if (Tools::getValue('action') AND Tools::getValue('id_product_comment') AND Context::getContext()->cookie->id_customer)
{
	if (Tools::getValue('action') == 'report')
	{
		if (!ProductComment::isAlreadyReport(Tools::getValue('id_product_comment'), Context::getContext()->cookie->id_customer) AND ProductComment::reportComment((int)Tools::getValue('id_product_comment'), (int)Context::getContext()->cookie->id_customer))
			die('0');
	}
	elseif (Tools::getValue('action') == 'usefulness' AND Tools::getValue('value') AND Tools::getValue('value'))
	{
		if (!ProductComment::isAlreadyUsefulness(Tools::getValue('id_product_comment'), Context::getContext()->cookie->id_customer) AND ProductComment::setCommentUsefulness((int)Tools::getValue('id_product_comment'), (bool)((int)Tools::getValue('value')), Context::getContext()->cookie->id_customer))
			die('0');
	}
}

die('1');

