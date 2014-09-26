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

{if $status == 'ok'}
	<p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name mod='cheque'}</p>
    <div class="box order-confirmation">
    <h3 class="page-subheading">{l s='Your check must include:' mod='cheque'}</h3>
		- {l s='Payment amount.' mod='cheque'} <span class="price"><strong>{$total_to_pay}</strong></span>
		<br />- {l s='Payable to the order of' mod='cheque'} <strong>{if $chequeName}{$chequeName}{else}___________{/if}</strong>
		<br />- {l s='Mail to' mod='cheque'} <strong>{if $chequeAddress}{$chequeAddress}{else}___________{/if}</strong>
		{if !isset($reference) && isset($id_order) && $id_order}
			<br />- {l s='Do not forget to insert your order number #%d.' sprintf=$id_order mod='cheque'}
		{else}
			<br />- {l s='Do not forget to insert your order reference %s.' sprintf=$reference mod='cheque'}
		{/if}
		<br />- {l s='An email has been sent to you with this information.' mod='cheque'}
		<br />- <strong>{l s='Your order will be sent as soon as we receive your payment.' mod='cheque'}</strong>
		<br />- {l s='For any questions or for further information, please contact our' mod='cheque'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='cheque'}</a>.
	</div>
{else}
	<p class="alert alert-warning">
		{l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='cheque'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='cheque'}</a>.
	</p>
{/if}
