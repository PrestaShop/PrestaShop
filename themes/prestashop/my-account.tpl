{*
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
*}

<script type="text/javascript">
<!--
	var baseDir = '{$base_dir_ssl}';
-->
</script>

{capture name=path}{l s='My account'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='My account'}</h1>
<h4>{l s='Welcome to your account. Here you can manage your addresses and orders.'}</h4>
<ul>
	<li><a href="{$link->getPageLink('history.php', true)}" title="{l s='Orders'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Orders'}" class="icon" /></a><a href="{$link->getPageLink('history.php', true)}" title="{l s='Orders'}">{l s='History and details of my orders'}</a></li>
	{if $returnAllowed}
		<li><a href="{$link->getPageLink('order-follow.php', true)}" title="{l s='Merchandise returns'}"><img src="{$img_dir}icon/return.gif" alt="{l s='Merchandise returns'}" class="icon" /></a><a href="{$link->getPageLink('order-follow.php', true)}" title="{l s='Merchandise returns'}">{l s='My merchandise returns'}</a></li>
	{/if}
	<li><a href="{$link->getPageLink('order-slip.php', true)}" title="{l s='Credit slips'}"><img src="{$img_dir}icon/slip.gif" alt="{l s='Credit slips'}" class="icon" /></a><a href="{$link->getPageLink('order-slip.php', true)}" title="{l s='Credit slips'}">{l s='My credit slips'}</a></li>
	<li><a href="{$link->getPageLink('addresses.php', true)}" title="{l s='Addresses'}"><img src="{$img_dir}icon/addrbook.gif" alt="{l s='Addresses'}" class="icon" /></a><a href="{$link->getPageLink('addresses.php', true)}" title="{l s='Addresses'}">{l s='My addresses'}</a></li>
	<li><a href="{$link->getPageLink('identity.php', true)}" title="{l s='Information'}"><img src="{$img_dir}icon/userinfo.gif" alt="{l s='Information'}" class="icon" /></a><a href="{$link->getPageLink('identity.php', true)}" title="{l s='Information'}">{l s='My personal information'}</a></li>
	{if $voucherAllowed}
		<li><a href="{$link->getPageLink('discount.php', true)}" title="{l s='Vouchers'}"><img src="{$img_dir}icon/voucher.gif" alt="{l s='Vouchers'}" class="icon" /></a><a href="{$link->getPageLink('discount.php', true)}" title="{l s='Vouchers'}">{l s='My vouchers'}</a></li>
	{/if}
	{$HOOK_CUSTOMER_ACCOUNT}
</ul>
<p><a href="{$base_dir}" title="{l s='Home'}"><img src="{$img_dir}icon/home.gif" alt="{l s='Home'}" class="icon" /></a><a href="{$base_dir}" title="{l s='Home'}">{l s='Home'}</a></p>
