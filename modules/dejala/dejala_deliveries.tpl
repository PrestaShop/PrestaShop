			<fieldset>
				{if ($djl_mode == 'PROD')}{l s='Your credit' mod='dejala'}{else if ($djl_mode == 'TEST')}{l s='Your virtual credit (in order to test)' mod='dejala'}{/if} : {$account_balance} {l s='euros' mod='dejala'}<br/>
				{if ($djl_mode == 'PROD')}<a href="http://pro.dejala.{$country}" target="_blank" style="color:blue;font-weight:bold;text-decoration:underline;">{l s='Credit your account' mod='dejala'}</a><br/>{/if}
			
				<h4 class="clear">{l s='List of last deliveries' mod='dejala'}:</h4>

				<table class="table" width="100%" >

					<tr class='bold'>
						<th>{l s='#' mod='dejala'}</th>
						<th>{l s='Creation' mod='dejala'}</th>
						<th>{l s='Order ID' mod='dejala'}</th>
						<th>{l s='Tracking_number' mod='dejala'}</th>
						<th>{l s='Label' mod='dejala'}</th>
						<th>{l s='Price' mod='dejala'}</th>
						<th>{l s='Status' mod='dejala'}</th>
						<th>{l s='Shipping' mod='dejala'}</th>
						<th>{l s='Description' mod='dejala'}</th>
					</tr>

					{foreach from=$deliveries item=delivery name=deliveryLoop}
						<tr {if ($smarty.foreach.deliveryLoop.index % 2 == 0)}class='alt_row'{/if}>
						{if $delivery.status.picto_id}
							<td><img src="{if $delivery.status.picto_id < 10}{$module_dir}{else}http://module.pro.dejala.{$country}/picto/{/if}picto_{$delivery.status.picto_id}.gif" /></td>
						{else}
							<td><img src="{$module_dir}picto_0.gif"/></td>
						{/if}	
							<td>{$delivery.creation_date}</td>
							<td>{$delivery.packet_reference}</td>
							<td>{if $delivery.tracking_number}<a href="http://tracking.dejala.{$country}/tracker/{$delivery.tracking_number}" target="_blank">{$delivery.tracking_number}</a>{/if}</td>
							<td>{$delivery.short_label}</td>
							<td>{$delivery.price}</td>
							<td>{$delivery.status.labels.fr}</td>
							<td>{$delivery.shipping_date}</td>
							<td>{l s='In ' mod='dejala'}{$delivery.timelimit}{l s='h' mod='dejala'} - {l s='from' mod='dejala'} {$delivery.shipping_start} {l s='to' mod='dejala'} {$delivery.shipping_stop}</td>
						</tr>
					{/foreach}
				</table>
				<br/>
				</fieldset>
				<br/>
				<fieldset>
				<legend class="clear">{l s='Csv Export' mod='dejala'}:</legend>

				<form action="{$formAction}" method="post">
					<input type="hidden" name="method" value="accounting"/>
					<div id="calendar">
					<script type="text/javascript" src="{$module_dir}../../js/jquery/jquery-ui-1.8.10.custom.min.js"></script>
					<script type="text/javascript" src="{$module_dir}../../js/jquery/datepicker/ui/i18n/ui.datepicker-fr.js"></script>
					{literal}
					<script type="text/javascript">
						$(function() {
							$("#datepickerFrom").datepicker({
								prevText:"",
								nextText:"",
								dateFormat:"dd/mm/yy"});
						});
						$(function() {
							$("#datepickerTo").datepicker({
								prevText:"",
								nextText:"",
								dateFormat:"dd/mm/yy"});
						});
					</script>
	{/literal}

{l s='From' mod='dejala'}: <input type="text" name="datepickerFrom" id="datepickerFrom" value="{$defaultDateFrom}">
{l s='To' mod='dejala'}: <input type="text" name="datepickerTo" id="datepickerTo" value="{$defaultDateTo}">

					<input type="submit" name="submitDatePicker" value="{l s='Ok' mod='dejala'}" class="button" />
			</div>
				</form>
			</fieldset>
			