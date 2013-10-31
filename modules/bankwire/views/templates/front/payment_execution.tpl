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

{capture name=path}{l s='Bank-wire payment.' mod='bankwire'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='bankwire'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='bankwire'}</p>
{else}

<h3>{l s='Bank-wire payment.' mod='bankwire'}</h3>
<form action="{$link->getModuleLink('bankwire', 'validation', [], true)|escape:'html'}" method="post">
<p>
	<img src="{$this_path_bw}bankwire.jpg" alt="{l s='Bank wire' mod='bankwire'}" width="86" height="49" style="float:left; margin: 0px 10px 5px 0px;" /><br />
	{l s='You have chosen to pay by bank wire.' mod='bankwire'}
	<br/><br />
</p>
<!-- if customer currency is that one I want -->
{if ($cart->id_currency) == 1}
<p>
	{l s='The total amount of your order is' mod='bankwire'}
	<span id="amount" class="price" style="font-weight: bold">{displayPrice price=$total}</span>{if $priceDisplay == 1} {l s='(tax excl.)' mod='bankwire'}{else} {l s='(tax incl.)' mod='bankwire'}{/if}
</p>
<p>
	{l s='Bank wire account information will be displayed on the next page.' mod='bankwire'}
</p>
<p>
	<b>{l s='Please confirm your order by clicking \'Place my order\'' mod='bankwire'}.</b>
</p>
<p class="cart_navigation" id="cart_navigation">
	<input type="submit" value="{l s='Place my order' mod='bankwire'}" class="exclusive_large" />
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='bankwire'}</a>
</p>
</form>
<!-- if customer currency is different from what I want -->
{else}
<p>
	{l s='We only accept payments in' mod='bankwire'} <b>{l s='Euros' mod='bankwire'}</b>. {l s='Your current currency is' mod='bankwire'} <b>{if ($cart->id_currency) == 2}{l s='Dollar' mod='bankwire'}{/if}</b><b>{if ($cart->id_currency) == 3}{l s='Pound' mod='bankwire'}{/if}</b>.<br /><br />
	{l s='The total amount of your order is' mod='bankwire'}
	<span id="amount" class="price"><b>{displayPrice price=$total}</b></span>{if $priceDisplay == 1} {l s='(tax excl.)' mod='bankwire'}{else} {l s='(tax incl.)' mod='bankwire'}{/if}
	<br /><br />
	<b>{l s='Please note' mod='bankwire'}</b>:<br />
	{l s='The reference prices of the entire shop are in' mod='bankwire'} {l s='Euros' mod='bankwire'}. {l s='The conversion in' mod='bankwire'} {if ($cart->id_currency) == 2}{l s='Dollar' mod='bankwire'}{/if}{if ($cart->id_currency) == 3}{l s='Pound' mod='bankwire'}{/if} {l s='is automatically made using daily updated exchange rates, but keep in mind that exchange rates of your bank may be slightly different' mod='bankwire'}.<br /><br />
	{l s='By clicking' mod='bankwire'} <b>OK</b> {l s='your order will be converted in' mod='bankwire'} {l s='Euros' mod='bankwire'} {l s='and you will be able to conclude the check out paying with bank wire' mod='bankwire'}.<br /><br />
	{l s='Otherwise click' mod='bankwire'} <b>{l s='Other payment methods' mod='bankwire'}</b> {l s='and choose another kind of payment' mod='bankwire'}.
</p>
<p class="cart_navigation">
	<input type="button" id="currency_payement" name="currency_payement" value="OK" onclick="setCurrency(1);" class="exclusive_large" />
	<a href="{$link->getPageLink('order', true, NULL, "step=3")}" class="button_large">{l s='Other payment methods' mod='bankwire'}</a>
</p>
{/if}
{/if}
