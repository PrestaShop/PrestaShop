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

{if $status == 'ok'}
<p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='bankwire'}
		<br /><br />
		{l s='Please send us a bank wire with' mod='bankwire'}:<br /><br />
		&bull; {l s='an amount of' mod='bankwire'}<span class="price">:<br />
		<strong>{$total_to_pay}</strong></span><br /><br />
		&bull; {l s='to the account owner' mod='bankwire'}:<br />
		<strong>{if $bankwireOwner}{$bankwireOwner}{else}___________{/if}</strong><br /><br />
		&bull; {l s='with these details' mod='bankwire'}:<br />
		<strong>{if $bankwireDetails}{$bankwireDetails}{else}___________{/if}</strong><br /><br />
		&bull; {l s='to this bank' mod='bankwire'}:<br />
		<strong>{if $bankwireAddress}{$bankwireAddress}{else}___________{/if}</strong><br /><br />
		{if !isset($reference)}
		&bull; {l s='Do not forget to insert your order number #%d in the subject of your bank wire' sprintf=$id_order mod='bankwire'}
		{else}
		&bull; {l s='Do not forget to insert your order reference %s in the subject of your bank wire.' sprintf=$reference mod='bankwire'}
		{/if}<br /><br />
		{l s='An email has been sent to you with this information.' mod='bankwire'}<br /><br />
		{l s='Your order will be sent as soon as we receive your settlement' mod='bankwire'}.<br /><br />
		<b>{l s='Please note' mod='bankwire'}</b>:<br />
		{l s='Charges for bank transfer as well as any other cost for the transaction are fully charged to the buyer. During compilation of the bank transfer, please set the costs on \"OUR\"' mod='bankwire'}. <b>{l s='Only after receiving the full amount, we\'ll be able to submit your order.' mod='bankwire'}</b><br /><br />
		{l s='For any questions or for further information, please contact our' mod='bankwire'} <a href="{$link->getPageLink('contact', true)}" style="color: #08c;">{l s='customer support' mod='bankwire'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='bankwire'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}" style="color: #08c;">{l s='customer support' mod='bankwire'}</a>.
	</p>
{/if}
