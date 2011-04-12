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

{if !$opc}
	<script type="text/javascript">
	<!--
		var baseDir = '{$base_dir_ssl}';
	-->
	</script>

	{capture name=path}{l s='Your payment method'}{/capture}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}

{if !$opc}<h1>{l s='Choose your payment method'}</h1>{else}<h2>3. {l s='Choose your payment method'}</h2>{/if}

{if !$opc}
	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	{include file="$tpl_dir./errors.tpl"}
{else}
	<div id="opc_payment_methods" class="opc-main-block">
		<div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>
{/if}

<div id="HOOK_TOP_PAYMENT">{$HOOK_TOP_PAYMENT}</div>

{if $HOOK_PAYMENT}
	{if !$opc}<h4>{l s='Please select your preferred payment method to pay the amount of'}&nbsp;<span class="price">{convertPrice price=$total_price}</span> {if $taxes_enabled}{l s='(tax incl.)'}{/if}</h4><br />{/if}
	{if $opc}<div id="opc_payment_methods-content">{/if}
		<div id="HOOK_PAYMENT">{$HOOK_PAYMENT}</div>
	{if $opc}</div>{/if}
{else}
	<p class="warning">{l s='No payment modules have been installed.'}</p>
{/if}

{if !$opc}
	<p class="cart_navigation"><a href="{$link->getPageLink('order.php', true)}?step=2" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a></p>
{else}
	</div>
{/if}

