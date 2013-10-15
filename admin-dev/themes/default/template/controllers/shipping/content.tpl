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

<div id="ajax_confirmation" class="alert alert-success" style="display:none"></div>
{* ajaxBox allows*}	<div id="ajaxBox" style="display:none"></div>
{if isset($content)}
	{if isset($show_page_header_toolbar) && $show_page_header_toolbar &&(!isset($lite_display) || !$lite_display)}
		<div class="leadin">
			{foreach from=$page_header_toolbar_btn item=btn key=k}
				{if $k == 'modules-list'}
				<div class="modal fade" id="modules_list_container">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title">{l s='Modules'}</h3>
							</div>
							<div class="modal-body">
								<div id="modules_list_container_tab" style="display:none;"></div>
								<div id="modules_list_loader"><img src="../img/loader.gif" alt=""/></div>
							</div>
						</div>
					</div>
				</div>
				{/if}
			{/foreach}
		</div>
		{include file="page_header_toolbar.tpl" toolbar_btn=$page_header_toolbar_btn title=$page_header_toolbar_title}
	{/if}
	{$content}
{/if}


<form action="{$action_fees}" id="fees" name="fees" method="post" class="form-horizontal">
	<div class="panel">
		<h3>
			<i class="icon-truck"></i>
			{l s='Fees by carrier, geographical zone and ranges'}
		</h3>
		{if empty($carriers)}
			{l s='If you only have free carriers, there\'s no need to configure delivery prices.'}
		{else}
			<div class="row">
				<label class="control-label col-lg-3">{l s='Carrier:'} </label>
				<div class="col-lg-6">
					<select name="id_carrier2" onchange="$('#fees').attr('action', $('#fees').attr('action')+'&id_carrier='+$(this).attr('value')+'#fees'); $('#fees').submit();">
						{foreach $carriers AS $carrier}
							<option value="{$carrier['id_carrier']|intval}" {if $carrier['id_carrier'] == $id_carrier} selected="selected"{/if}>{$carrier['name']}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<table class="table" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th>{l s='Zone / Range'}</th>
						{if !$carrierSelected->is_free}
							{foreach $ranges AS $range}
								<th>{$range['delimiter1']|floatval}{$suffix} {l s='to'} {$range['delimiter2']|floatval}{$suffix}</th>
							{/foreach}
						{/if}
					</tr>
				</thead>
				<tbody>
					{if sizeof($ranges) && !$carrierSelected->is_free}
						{if sizeof($zones) > 1}
							<tr>
								<td>{l s='All'}</td>
								{foreach $ranges AS $range}
									<td>
										<div class="input-group col-lg-9">
											<span class="input-group-addon">{$currency->prefix}{$currency->suffix} {l s='(tax excl.)'}</span>
											<input type="text" id="fees_all_{$range[$rangeIdentifier]}" onchange="this.value = this.value.replace(/,/g, '.');" onkeyup="if ((event.keyCode||event.which) != 9){ spreadFees({$range[$rangeIdentifier]})}" />
										</div>
									</td>
								{/foreach}
							</tr>
						{/if}
						{foreach $zones AS $zone}
							<tr>
								<td>{$zone['name']}</td>
								{foreach $ranges AS $range}
									{if isset($deliveryArray[$zone['id_zone']][$id_carrier][$range[$rangeIdentifier]])}
										{$price = $deliveryArray[$zone['id_zone']][$id_carrier][$range[$rangeIdentifier]]}
									{else}
										{$price = '0.00'}
									{/if}
									<td>
										<div class="input-group col-lg-9">
											<span class="input-group-addon">{$currency->prefix}{$currency->suffix} {l s='(tax excl.)'}</span>
											<input 
												type="text" 
												class="fees_{$range[$rangeIdentifier]}" 
												onchange="this.value = this.value.replace(/,/g, '.');" name="fees_{$zone['id_zone']}_{$range[$rangeIdentifier]}" onkeyup="clearAllFees({$range[$rangeIdentifier]})" 
												value="{$price|string_format:"%.6f"}"
											/>
										</div>
									</td>
								{/foreach}
							</tr>
						{/foreach}
					{/if}
					<tr>
						<td colspan="{$ranges|sizeof + 1}" class="text-center">
							<input type="hidden" name="submitFees{$table}" value="1" />
						{if sizeof($ranges) && !$carrierSelected->is_free}
							<input type="submit" value="{l s='Save'}" class="btn btn-default" />
						{else if $carrierSelected->is_free}
							<span>{l s='This is a free carrier'}</span>
						{else}
							<span>{l s='No ranges is set for this carrier'}</span>
						{/if}
						</td>
					</tr>
				</tbody>
			</table>
		{/if}
		<input type="hidden" name="id_carrier" value="{$id_carrier}" />
	</div>
</form>
