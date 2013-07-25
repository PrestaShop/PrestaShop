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

<div class="row">
	<label class="control-label col-lg-3">
		{l s='Name'}
		<input type="text" name="discount_name" value="" />
	</label>

	<label class="control-label col-lg-3">
		{l s='Type'}
		<select name="discount_type" id="discount_type">
			<option value="1">{l s='Percent'}</option>
			<option value="2">{l s='Amount'}</option>
			<option value="3">{l s='Free shipping'}</option>
		</select>
	</label>

	<div id="discount_value_field">
		<label class="control-label col-lg-3">
			{l s='Value'}
			
			{if ($currency->format % 2)}
				<span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
			{else}
				<span id="discount_percent_symbol">%</span>
			{/if}
			{if !($currency->format % 2)}
				<span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
			{/if}
			<input type="text" name="discount_value" size="3" />
			<p class="text-muted" id="discount_value_help" style="display: none;">
				{l s='This value must include taxes.'}
			</p>
		</label>
	</div>

	{if $order->hasInvoice()}
	<label class="control-label col-lg-3">
		{l s='Invoice'}
		<select name="discount_invoice">
			{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang)} - {displayPrice price=$invoice->total_paid_tax_incl currency=$order->id_currency}</option>
			{/foreach}
		</select>
		<p class="checkbox">
			<label class="control-label" for="discount_all_invoices">
				<input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" /> 
				{l s='Apply on all invoices'}
			</label>
		</p>
		<p class="text-muted">
			{l s='If you chooses to create this discount for all invoices, only one discount will be created per order invoice.'}
		</p>
	</label>
	{/if}

	<p class="col-lg-12 text-right">
		<input class="btn btn-default" type="submit" name="submitNewVoucher" value="{l s='Add'}" />
		<a href="#" id="cancel_add_voucher" class="btn btn-default">
			<i class="icon-remove"></i>
			{l s='Cancel'}
		</a>
	</p>
</div>

