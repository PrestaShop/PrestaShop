{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div class="form-horizontal well">
	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Name' d='Admin.Global'}
		</label>
		<div class="col-lg-9">
			<input class="form-control" type="text" name="discount_name" value="" />
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Type' d='Admin.Catalog.Feature'}
		</label>
		<div class="col-lg-9">
			<select class="form-control" name="discount_type" id="discount_type">
				<option value="1">{l s='Percent' d='Admin.Global'}</option>
				<option value="2">{l s='Amount' d='Admin.Global'}</option>
				<option value="3">{l s='Free shipping' d='Admin.Shipping.Feature'}</option>
			</select>
		</div>
	</div>

	<div id="discount_value_field" class="form-group">
		<label class="control-label col-lg-3">
			{l s='Value' d='Admin.Global'}
		</label>
		<div class="col-lg-9">
			<div class="input-group">
				<div class="input-group-addon">
					<span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
					<span id="discount_percent_symbol">%</span>
				</div>
				<input class="form-control" type="text" name="discount_value"/>
			</div>
			<p class="text-muted" id="discount_value_help" style="display: none;">
				{l s='This value must include taxes.' d='Admin.Orderscustomers.Notification'}
			</p>
		</div>
	</div>

	{if $order->hasInvoice()}
	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Invoice' d='Admin.Global'}
		</label>
		<div class="col-lg-9">
			<select name="discount_invoice">
				{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}" selected="selected">
					{$invoice->getInvoiceNumberFormatted($current_id_lang)} - {displayPrice price=$invoice->total_paid_tax_incl currency=$order->id_currency}
				</option>
				{/foreach}
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<p class="checkbox">
				<label class="control-label" for="discount_all_invoices">
					<input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" />
					{l s='Apply on all invoices' d='Admin.Orderscustomers.Feature'}
				</label>
			</p>
			<p class="help-block">
				{l s='If you chooses to create this discount for all invoices, only one discount will be created per order invoice.' d='Admin.Orderscustomers.Help'}
			</p>
		</div>
	</div>
	{/if}

	<div class="row">
		<div class="col-lg-9 col-lg-offset-3">
			<button class="btn btn-default" type="button" id="cancel_add_voucher">
				<i class="icon-remove text-danger"></i>
				{l s='Cancel' d='Admin.Actions'}
			</button>
			<button class="btn btn-default" type="submit" name="submitNewVoucher">
				<i class="icon-ok text-success"></i>
				{l s='Add' d='Admin.Actions'}
			</button>
		</div>
	</div>
</div>
