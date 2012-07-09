{*
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
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

	<label>{l s='Name'}</label>
	<div class="margin-form">
		<input type="text" name="discount_name" value="" />
	</div>

	<label>{l s='Type'}</label>
	<div class="margin-form">
		<select name="discount_type" id="discount_type">
			<option value="1">{l s='Percent'}</option>
			<option value="2">{l s='Amount'}</option>
			<option value="3">{l s='Free shipping'}</option>
		</select>
	</div>

	<div id="discount_value_field">
		<label>{l s='Value'}</label>
		<div class="margin-form">
			{if ($currency->format % 2)}
				<span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
			{/if}
			<input type="text" name="discount_value" size="3" />
			{if !($currency->format % 2)}
				<span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
			{/if}
			<span id="discount_percent_symbol">%</span>
			<p class="preference_description" id="discount_value_help" style="width: 95%;display: none;">
				{l s='This value must be taxes included.'}
			</p>
		</div>
	</div>

	{if $order->hasInvoice()}
	<label>{l s='Invoice'}</label>
	<div class="margin-form">
		<select name="discount_invoice">
			{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang)} - {displayPrice price=$invoice->total_paid_tax_incl currency=$order->id_currency}</option>
			{/foreach}
		</select><br />
		<input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" /> <label class="t" for="discount_all_invoices">{l s='Apply on all invoices'}</label>
		<p class="preference_description" style="width: 95%">
			{l s='If you select to create this discount for all invoices, one discount will be created per order invoice.'}
		</p>
	</div>
	{/if}

	<p class="center">
		<input class="button" type="submit" name="submitNewVoucher" value="{l s='Add'}" />&nbsp;
		<a href="#" id="cancel_add_voucher">{l s='Cancel'}</a>
	</p>

