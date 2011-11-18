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
<div class="margin-form">
	<input type="submit" class="button" name="submitSuppliers" value="{l s='Update suppliers of this product'}"/>
</div>
<br />
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
<table cellpadding="5" style="width:100%">
	<tbody>
		<tr>
			<td valign="top" style="text-align:center;vertical-align:top;">
				<table class="table" cellpadding="0" cellspacing="0" style="min-width:80%;margin-left:10%;">
					<thead>
						<tr>
							<th>{l s='Product Name'}</th>
							{foreach from=$associated_suppliers item=supplier}
								<th>{$supplier->name}</th>
							{/foreach}
						</tr>
					</thead>
					<tbody>
					{foreach from=$attributes item=attribute}
						<tr>
							<td>{$product_designation[$attribute['id_product_attribute']]}</td>
							{foreach from=$associated_suppliers item=supplier}
								{assign var=reference value=''}
								{foreach from=$associated_suppliers_collection item=asc}
									{if $asc->id_product == $attribute['id_product'] && $asc->id_product_attribute == $attribute['id_product_attribute'] && $asc->id_supplier == $supplier->id_supplier}
										{assign var=reference value=$asc->product_supplier_reference}
									{/if}
								{/foreach}
								<td>
									<input type="text" size="10" value="{$reference}" name="supplier_reference_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}" />
								</td>
							{/foreach}
						</tr>
					{/foreach}
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<div class="margin-form">
	<input type="submit" class="button" name="submitSupplierReferences" value="{l s='Update supplier reference(s)'}"/>
</div>
