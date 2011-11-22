<div class="hint" style="display:block; position:'auto';">
	<p>{l s='This interface allows you to specify the suppliers of the current product and eventually its combinations.'}</p>
	<p>{l s='It is also possible to specify for each product/product combinations the supplier reference according to previously associated suppliers.'}</p>
</div>
<br />
<table cellpadding="5">
	<tbody>
		<tr>
			<td colspan="2">
				<b>{l s='Suppliers of the current product'}</b>
			</td>
		</tr>
	</tbody>
</table>
<div class="separation"></div>
<p>{l s='Please choose the suppliers associated to this product, and the default one.'}</p>
{assign var=confirm value="Are you sure you want to delete entered product information?"}
<a href="{$link->getAdminLink('AdminSuppliers')}&addsupplier" onclick="return confirm(' {$confirm} ')">
	<img src="../img/admin/add.gif" alt="{l s='Create new supplier'}" title="{l s='Create new supplier'}" /> <b>{l s='Create new supplier'}</b>
</a>
<table cellpadding="5" style="width:100%">
	<tbody>
		<tr>
			<td valign="top" style="text-align:center;vertical-align:top;">
				<table class="table" cellpadding="0" cellspacing="0" style="width:60%;margin-left:20%;">
					<thead>
						<tr>
							<th>{l s='Selected'}</th>
							<th>{l s='Supplier Name'}</th>
							<th>{l s='Default'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$suppliers item=supplier}
						<tr>
							<td><input type="checkbox" class="supplierCheckBox" name="check_supplier_{$supplier['id_supplier']}" {if $supplier['is_selected'] == true}checked="checked"{/if} value="{$supplier['id_supplier']}" /></td>
							<td>{$supplier['name']}</td>
							<td><input type="radio" id="default_supplier_{$supplier['id_supplier']}" name="default_supplier" value="{$supplier['id_supplier']}" {if $supplier['is_selected'] == false}disabled="disabled"{/if} {if $supplier['is_default'] == true}checked="checked"{/if} /></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<p>&nbsp;</p>
<table cellpadding="5">
	<tbody>
		<tr>
			<td colspan="2">
				<b>{l s='Product reference(s)'}</b>
			</td>
		</tr>
	</tbody>
</table>
<div class="separation"></div>
<p>{l s='You can specify product reference(s) for each supplier associated.'}</p>

<div id="suppliers_accordion" style="margin-top:10px; display:block;">
	{foreach from=$associated_suppliers item=supplier}
	    <h3 style="margin-bottom:0;"><a href="#">{$supplier->name}</a></h3>
	    <div style="display:block;">
			<table cellpadding="5" cellspacing="5" style="width:80%; margin-left:0;">
				<thead>
					<tr>
						<th>{l s='product name'}</th>
						<th width="150">{l s='supplier reference'}</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$attributes item=attribute}
					{assign var=reference value=''}
					{foreach from=$associated_suppliers_collection item=asc}
						{if $asc->id_product == $attribute['id_product'] && $asc->id_product_attribute == $attribute['id_product_attribute'] && $asc->id_supplier == $supplier->id_supplier}
							{assign var=reference value=$asc->product_supplier_reference}
						{/if}
					{/foreach}
					<tr>
						<td>{$product_designation[$attribute['id_product_attribute']]}</td>
						<td>
							<input type="text" size="10" value="{$reference}" name="supplier_reference_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}" />
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	{/foreach}
</div>

<script type="text/javascript">
	$(function() {
		var default_is_ok = false;

		var manageDefaultSupplier = function() {

			var availables_radio_buttons = [];
			var radio_buttons = $('input[name="default_supplier"]');

			for (i=0; i<radio_buttons.length; i++)
			{
				var item = $(radio_buttons[i]);

				if (item.is(':disabled'))
				{
					if (item.is(':checked'))
					{
						item.attr("checked", "");
						default_is_ok = false;
					}
				}
				else
				{
					availables_radio_buttons.push(item);
				}
			}

			if (default_is_ok == false)
			{
				for (i=0; i<availables_radio_buttons.length; i++)
				{
					var item = $(availables_radio_buttons[i]);

					if (item.is(':disabled') == false)
					{
						item.attr("checked", "checked");
						default_is_ok = true;
					}

					break;
				}
			}
		};

		$('.supplierCheckBox').click(function() {

			var check = $(this);

			var checkbox = $('#default_supplier_'+check.val());

			if (this.checked)
			{
				//enable default radio button associated
				checkbox.attr("disabled","");
			}
			else
			{
				//enable default radio button associated
				checkbox.attr("disabled","disabled");
			}

			//manage default supplier check
			manageDefaultSupplier();

		});

		setTimeout(function() {
			$('#suppliers_accordion').accordion();
		}, 500);
	});
</script>