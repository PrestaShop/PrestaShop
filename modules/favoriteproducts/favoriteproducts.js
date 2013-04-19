$('document').ready(function(){
	$('#favoriteproducts_block_extra_add').click(function(){
		$.ajax({
			url: favorite_products_url_add + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#favoriteproducts_block_extra_add').slideUp(function() {
			    		$('#favoriteproducts_block_extra_added').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#favoriteproducts_block_extra_remove').click(function(){
		$.ajax({
			url: favorite_products_url_remove + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#favoriteproducts_block_extra_remove').slideUp(function() {
			    		$('#favoriteproducts_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#favoriteproducts_block_extra_added').click(function(){
		$.ajax({
			url: favorite_products_url_remove + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#favoriteproducts_block_extra_added').slideUp(function() {
			    		$('#favoriteproducts_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#favoriteproducts_block_extra_removed').click(function(){
		$.ajax({
			url: favorite_products_url_add + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#favoriteproducts_block_extra_removed').slideUp(function() {
			    		$('#favoriteproducts_block_extra_added').slideDown("slow");
			    	});

				}
		 	}
		});
	});
})