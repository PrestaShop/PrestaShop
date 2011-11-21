<table cellpadding="5">
	<tbody>
		<tr>
			<td colspan="2">
				<b>{l s='Product location in warehouses'}</b>
			</td>
		</tr>
	</tbody>
</table>
<div class="separation"></div>
<p>{l s='Please choose the warehouses associated to this product, and the default one.'}</p>

{assign var=confirm value="Are you sure you want to delete entered product information?"}

<a href="{$link->getAdminLink('AdminWarehouses')}&addwarehouse" onclick="return confirm(' {$confirm} ')">
	<img src="../img/admin/add.gif" alt="{l s='Create new warehouse'}" title="{l s='Create new warehouse'}" /> <b>{l s='Create new warehouse'}</b>
</a>



<div id="accordion" style="margin-top:10px; display:block;">
	{foreach from=$warehouses item=warehouse}
	    <h3 style="margin-bottom:0;"><a href="#">{$warehouse['name']}</a></h3>
	    <div style="display:block;">
			<table cellpadding="5" cellspacing="5" style="width:80%; margin-left:0;">
				<tr>
					<th width="40">{l s='In the warehouse?'}</th>
					<th>{l s='product name'}</th>
					<th width="150">{l s='location in the warehouse (optionnal)'}</th>
				</tr>
			{foreach from=$attributes item=attribute}
				{assign var=location value=''}
				{assign var=selected value=''}
				{foreach from=$associated_warehouses item=aw}
					{if $aw->id_product == $attribute['id_product'] && $aw->id_product_attribute == $attribute['id_product_attribute'] && $aw->id_warehouse == $warehouse['id_warehouse']}
						{assign var=location value=$aw->location}
						{assign var=selected value=true}
					{/if}
				{/foreach}
				<tr>
					<td><input type="checkbox"
						name="check_warehouse_{$warehouse['id_warehouse']}_{$attribute['id_product']}_{$attribute['id_product_attribute']}"
						{if $selected == true}checked="checked"{/if}
						value="1" />
					</td>
					<td>{$product_designation[$attribute['id_product_attribute']]}</td>
					<td><input type="text"
						name="location_warehouse_{$warehouse['id_warehouse']}_{$attribute['id_product']}_{$attribute['id_product_attribute']}"
						value="{$location}"
						size="20" />
					</td>
				</tr>
			{/foreach}
			</table>
		</div>
	{/foreach}
</div>
<p>&nbsp;</p>
<div class="margin-form">
	<input type="submit" class="button" name="submitProductWarehouses" value="{l s='Update warehouses locations'}"/>
</div>

<script type="text/javascript">
	$(function() {
		window.setTimeout(function() {
			$('#accordion').accordion();
		}, 500);
	});
</script>