{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="table-responsive">
	<table class="table" id="shipping_table">
		<thead>
			<tr>
				<th>
					<span class="title_box ">{l s='Date'}</span>
				</th>
				<th>
					<span class="title_box ">&nbsp;</span>
				</th>
				<th>
					<span class="title_box ">{l s='Carrier'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Weight'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Shipping cost'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Tracking number'}</span>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$order->getShipping() item=line}
			<tr>
				<td>{dateFormat date=$line.date_add full=true}</td>
				<td>&nbsp;</td>
				<td>{$line.carrier_name}</td>
				<td class="weight">{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}</td>
				<td id="price_carrier_{$line.id_carrier}" class="center">
					{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
						{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}
					{else}
						{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}
					{/if}
				</td>
				<td>
					<span class="shipping_number_show">{if $line.url && $line.tracking_number}<a class="_blank" href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}</span>
				</td>
				<td>
					{if $line.can_edit}
						<a href="#" class="edit_shipping_link btn btn-default" data_id_order_carrier="{$line.id_order_carrier}" data_id_carrier="{$line.id_carrier}" data_tracking_number="{$line.tracking_number|htmlentities}" >
							<i class="icon-pencil"></i>
							{l s='Edit'}
						</a>
					{/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	<!-- shipping update modal -->
	<div class="modal fade" id="modal-shipping">
		<div class="modal-dialog">
			<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
				<input type="hidden" name="id_order_carrier" id="id_order_carrier" />
				<input type="hidden" name="submitShippingNumber" id="submitShippingNumber" value="1" />
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close'}"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">{l s='Edit shipping details'}</h4>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<div class="form-group">
								<div class="col-lg-5">{l s='Tracking number'}</div>
								<div class="col-lg-7"><input type="text" name="shipping_tracking_number" id="shipping_tracking_number" /></div>
							</div>
							<div class="form-group">
								<div class="col-lg-5">{l s='Carrier'}</div>
								<div class="col-lg-7">
									<select name="shipping_carrier" id="shipping_carrier">
										{foreach from=$carrier_list item=carrier}
										<option value="{$carrier.id}">{$carrier.name}</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancel'}</button>
						<button type="submit" class="btn btn-primary">{l s='Update'}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- END shipping update modal -->
</div>
