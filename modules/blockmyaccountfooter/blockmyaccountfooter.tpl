{*
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
*}

<!-- Block myaccount module -->
<div class="block myaccount">
	<p class="title_block"><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Manage your customer account' mod='blockmyaccount'}" rel="nofollow">{l s='My account' mod='blockmyaccount'}</a></p>
	<div class="block_content">
		<ul class="bullet">
			<li><a href="{$link->getPageLink('identity', true)|escape:'html'}" title="{l s='Manage your personal account information' mod='blockmyaccount'}" rel="nofollow">{l s='My personal info' mod='blockmyaccount'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)|escape:'html'}" title="{l s='Manage your shipping and billing addresses' mod='blockmyaccount'}" rel="nofollow">{l s='My addresses' mod='blockmyaccount'}</a></li>
			<li><a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='View list of the orders you\'ve created' mod='blockmyaccount'}" rel="nofollow">{l s='Order history and details' mod='blockmyaccount'}</a></li>
			{if $returnAllowed}
			<li><a href="{$link->getPageLink('order-follow', true)|escape:'html'}" title="{l s='View list of your merchandise returns' mod='blockmyaccount'}" rel="nofollow">{l s='My merchandise returns' mod='blockmyaccount'}</a></li>
			{/if}
			<li><a href="{$link->getPageLink('order-slip', true)|escape:'html'}" title="{l s='View list of your credit slips' mod='blockmyaccount'}" rel="nofollow">{l s='My credit slips' mod='blockmyaccount'}</a></li>
			{if $voucherAllowed}
			<li><a href="{$link->getPageLink('discount', true)|escape:'html'}" title="{l s='View list of your discount vouchers' mod='blockmyaccount'}" rel="nofollow">{l s='My discount vouchers' mod='blockmyaccount'}</a></li>
			{/if}
			{$HOOK_BLOCK_MY_ACCOUNT}
			{if $logged}
			<li class="logout"><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Log out from this customer account' mod='blockmyaccount'}" rel="nofollow">{l s='Log out' mod='blockmyaccount'}</a></li>
			{/if}
		</ul>
	</div>
</div>
<!-- /Block myaccount module -->
