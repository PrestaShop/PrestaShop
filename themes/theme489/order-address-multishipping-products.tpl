<p>{l s='Choose the delivery addresses:'}</p>
<script type="text/javascript">
	CloseTxt = '{l s='Submit' js=1}';
	QtyChanged = '{l s='Some product quantities have changed, please check them.' js=1}';
	ShipToAnOtherAddress = '{l s='Ship to multiple addresses' js=1}';
</script>
<div id="order-detail-content" class="table_block">
	<table id="cart_summary" class="std multishipping-cart">
		<thead>
			<tr>
				<th class="cart_product first_item">{l s='Product'}</th>
				<th class="cart_description item">{l s='Description'}</th>
				<th class="cart_ref item">{l s='Ref.'}</th>
				<th class="cart_quantity item">{l s='Qty'}</th>
				<th class="shipping_address last_item">{l s='Shipping address'}</th>
                				<th class="delete">{l s=''}</th>
			</tr>
		</thead>
		<tbody>
		{foreach $product_list as $product}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{assign var='odd' value=$product@iteration%2}
			{* Display the product line *}
			{include file="$tpl_dir./order-address-product-line.tpl" productLast=$product@last productFirst=$product@first}
		{/foreach}
		</tbody>
	</table>
</div>
