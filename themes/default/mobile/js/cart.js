$( '.prestashop-page' ).live( 'pageshow',function(event)
{
	var quantity =  new Array();

	$("[name='cart_product_id[]']").each(function(i){
		quantity[$(this).val()]	= parseInt($('[name="product_cart_quantity_'+$(this).val()+'"]').val());
	});

	$(".display_block_card_product").children().each(function(i){
		$(this).hide();
	});

	$(".grouped_buttons_card").children().each(function(i){
		$(this).click(function(){
		$(".display_block_card_product").children().each(function(i){
			$(this).hide();
		});
		$("#"+$(this).attr('id')+"sheet").show();
		});
	});

	$('[name*="product_cart_quantity_"]').change(function()
	{
		ids = $(this).attr("name").split('_');
		id = ids[3];
		val = parseInt($(this).val());

		if (quantity[id] < val)
		{
			CartUpd.ajaxUpdQty(id, val - quantity[id], 1);
			quantity[id] = val;
		}
		else if (quantity[id] > val)
		{
			CartUpd.ajaxUpdQty(id, quantity[id] - val, 0);
			quantity[id] = val;
		}
	});

	$('[id*="delete_cart_"]').click(function()
	{
		ids = $(this).attr("id").split('_');
		CartUpd.deleteProductFromSummary(ids[2]);
	});

});

var CartUpd = (function()
{
	return {
		ajaxUpdQty : function(id, qty, op)
		{
			productAttributeId = $("#cart_product_attribute_id_"+id).val();
			id_address_delivery = $("#cart_product_address_delivery_id_"+id).val();
			customizationId = 0;

			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: baseDir + '?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				dataType: 'json',
				data: 'controller=cart&ajax=true&add=true&getproductprice&summary&id_product='+id+'&ipa='+productAttributeId+'&id_address_delivery='+id_address_delivery+ ( op == 0 ? '&op=down' : '' ) + ( (customizationId != 0) ? '&id_customization='+customizationId : '') + '&qty='+qty+'&token=' + static_token ,
				success: function(jsonData)
				{
					if (!jsonData.hasError)
						CartUpd.updData(jsonData);
				}
			});
		},
		deleteProductFromSummary : function(id)
		{
			productAttributeId = $("#cart_product_attribute_id_"+id).val();
			id_address_delivery = $("#cart_product_address_delivery_id_"+id).val();
			customizationId = 0;
			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: baseDir + '?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				dataType: 'json',
				data: 'controller=cart&ajax=true&delete=true&summary=true&id_product='+id+'&ipa='+productAttributeId+'&id_address_delivery='+id_address_delivery+ ( (customizationId != 0) ? '&id_customization='+customizationId : '') + '&token=' + static_token ,
				success: function(jsonData)
				{
					if (!jsonData.hasError)
					{
						if (jsonData.refresh)
							location.reload();
						$("#cart_total_products").html(jsonData.summary.total_products_wt);
						$("#cart_total_price").html(jsonData.summary.total_price);
						$("#element_product_"+id).fadeOut();
					}
				}
			});
		}
		,
		updData : function(data)
		{
			var products = data.summary.products;

			$(products).each(function(i){
				price = this.price_wt * this.quantity;
				$("#grouped_buttons_card_"+this.id_product+"_totsheet").html((price).toFixed(2));
			});

			$("#cart_total_products").html(data.summary.total_products_wt);
			$("#cart_total_price").html(data.summary.total_price);
		}
	}
})();
