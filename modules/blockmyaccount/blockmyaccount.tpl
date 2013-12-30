{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block myaccount module -->
<div class="block myaccount">
	<h4 class="title_block"><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='My account' mod='blockmyaccount'}">{l s='My account' mod='blockmyaccount'}</a></h4>
	<div class="block_content">
		<ul class="bullet">
			<li><a href="{$link->getPageLink('history', true)|escape:'html'}" title="">{l s='My orders' mod='blockmyaccount'}</a></li>
			{if $returnAllowed}<li><a href="{$link->getPageLink('order-follow', true)|escape:'html'}" title="{l s='My merchandise returns' mod='blockmyaccount'}">{l s='My merchandise returns' mod='blockmyaccount'}</a></li>{/if}
			<li><a href="{$link->getPageLink('order-slip', true)|escape:'html'}" title="{l s='My credit slips' mod='blockmyaccount'}">{l s='My credit slips' mod='blockmyaccount'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)|escape:'html'}" title="{l s='My addresses' mod='blockmyaccount'}">{l s='My addresses' mod='blockmyaccount'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)|escape:'html'}" title="{l s='My personal info' mod='blockmyaccount'}">{l s='My personal info' mod='blockmyaccount'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount', true)|escape:'html'}" title="{l s='My vouchers' mod='blockmyaccount'}">{l s='My vouchers' mod='blockmyaccount'}</a></li>{/if}
			{$HOOK_BLOCK_MY_ACCOUNT}
		</ul>
		<p class="logout"><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Sign out' mod='blockmyaccount'}">{l s='Sign out' mod='blockmyaccount'}</a></p>
	</div>
</div>
<!-- /Block myaccount module -->
