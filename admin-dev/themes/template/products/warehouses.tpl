<div class="hint" style="display:block; position:'auto';">
	<p>{l s='This interface allows you to specify in which warehouses the product is stocked.'}</p>
	<p>{l s='It is also possible to specify for each product/product combinations its location in each warehouse.'}</p>
</div>
<br />
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

<div id="warehouse_accordion" style="margin-top:10px; display:block;">
	{foreach from=$warehouses item=warehouse}
	    <h3 style="margin-bottom:0;"><a href="#">{$warehouse['name']}</a></h3>
	    <div style="display:block;">
			<table cellpadding="5" cellspacing="5" style="width:80%; margin-left:0;">
				<tr>
					<th width="100">{l s='In the warehouse?'}</th>
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
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td><input type="checkbox" id="check_all_suppliers" value="check_warehouse_{$warehouse['id_warehouse']}" /></td>
					<td colspan="2"><i>{l s='Mark all products available in the current warehouse.'}</i></td>
				</tr>
			</table>
		</div>
	{/foreach}
</div>
<p>&nbsp;</p>

<script type="text/javascript">
	$(function() {
		$('#check_all_suppliers').click(function() {
			var check = $(this);

			//get all checkboxes of current warehouse
			var checkboxes = $('input[name*="'+check.val()+'"]');

			for (i=0; i<checkboxes.length; i++)
			{
				var item = $(checkboxes[i]);

				if (item.is(':checked'))
				{
					item.attr("checked", "");
				}
				else
				{
					item.attr("checked", "checked");
				}
			}
		});

		setTimeout(function() {
			$('#warehouse_accordion').accordion();
		}, 500);
	});
</script>