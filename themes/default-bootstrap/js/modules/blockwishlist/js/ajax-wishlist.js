/*
* 2007-2014 PrestaShop
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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2014 PrestaShop SA
* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

/**
* Update WishList Cart by adding, deleting, updating objects
*
* @return void
*/
//global variables
var wishlistProductsIds = [];
$(document).ready(function(){
	wishlistRefreshStatus();

	$(document).on('change', 'select[name=wishlists]', function(){
		WishlistChangeDefault('wishlist_block_list', $(this).val());
	});
});

function WishlistCart(id, action, id_product, id_product_attribute, quantity)
{
	$.ajax({
		type: 'GET',
		url: baseDir + 'modules/blockwishlist/cart.php?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		async: true,
		cache: false,
		data: 'action=' + action + '&id_product=' + id_product + '&quantity=' + quantity + '&token=' + static_token + '&id_product_attribute=' + id_product_attribute,
		success: function(data)
		{
			if (action == 'add')
			{
				if (isLoggedWishlist == true) {
					wishlistProductsIdsAdd(id_product);
					wishlistRefreshStatus();

		            if (!!$.prototype.fancybox)
		                $.fancybox.open([
		                    {
		                        type: 'inline',
		                        autoScale: true,
		                        minHeight: 30,
		                        content: '<p class="fancybox-error">' + added_to_wishlist + '</p>'
		                    }
		                ], {
		                    padding: 0
		                });
		            else
		                alert(added_to_wishlist);
				}
				else
				{
		            if (!!$.prototype.fancybox)
		                $.fancybox.open([
		                    {
		                        type: 'inline',
		                        autoScale: true,
		                        minHeight: 30,
		                        content: '<p class="fancybox-error">' + loggin_required + '</p>'
		                    }
		                ], {
		                    padding: 0
		                });
		            else
		                alert(loggin_required);
				}
			}
			if (action == 'delete') {
				wishlistProductsIdsRemove(id_product);
				wishlistRefreshStatus();
			}
			if($('#' + id).length != 0)
			{
				$('#' + id).slideUp('normal');
				document.getElementById(id).innerHTML = data;
				$('#' + id).slideDown('normal');
			}
		}
	});
}

/**
* Change customer default wishlist
*
* @return void
*/
function WishlistChangeDefault(id, id_wishlist)
{
	$.ajax({
		type: 'GET',
		url: baseDir + 'modules/blockwishlist/cart.php?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		async: true,
		data: 'id_wishlist=' + id_wishlist + '&token=' + static_token,
		cache: false,
		success: function(data)
		{
			$('#' + id).slideUp('normal');
			document.getElementById(id).innerHTML = data;
			$('#' + id).slideDown('normal');
		}
	});
}

/**
* Buy Product
*
* @return void
*/
function WishlistBuyProduct(token, id_product, id_product_attribute, id_quantity, button, ajax)
{
	if(ajax)
		ajaxCart.add(id_product, id_product_attribute, false, button, 1, [token, id_quantity]);
	else
	{
		$('#' + id_quantity).val(0);
		WishlistAddProductCart(token, id_product, id_product_attribute, id_quantity)
		document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].method='POST';
		document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].action=baseUri + '?controller=cart';
		document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].elements['token'].value = static_token;
		document.forms['addtocart' + '_' + id_product + '_' + id_product_attribute].submit();
	}
	return (true);
}

function WishlistAddProductCart(token, id_product, id_product_attribute, id_quantity)
{
	if ($('#' + id_quantity).val() <= 0)
		return (false);

	$.ajax({
			type: 'GET',
			url: baseDir + 'modules/blockwishlist/buywishlistproduct.php?rand=' + new Date().getTime(),
			headers: { "cache-control": "no-cache" },
			data: 'token=' + token + '&static_token=' + static_token + '&id_product=' + id_product + '&id_product_attribute=' + id_product_attribute,
			async: true,
			cache: false,
			success: function(data)
			{
				if (data)
				{
		            if (!!$.prototype.fancybox)
		                $.fancybox.open([
		                    {
		                        type: 'inline',
		                        autoScale: true,
		                        minHeight: 30,
		                        content: '<p class="fancybox-error">' + data + '</p>'
		                    }
		                ], {
		                    padding: 0
		                });
		            else
		                alert(data);
				}
				else
					$('#' + id_quantity).val($('#' + id_quantity).val() - 1);
			}
	});

	return (true);
}

/**
* Show wishlist managment page
*
* @return void
*/
function WishlistManage(id, id_wishlist)
{
	$.ajax({
		type: 'GET',
		async: true,
		url: baseDir + 'modules/blockwishlist/managewishlist.php?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		data: 'id_wishlist=' + id_wishlist + '&refresh=' + false,
		cache: false,
		success: function(data)
		{
			$('#' + id).hide();
			document.getElementById(id).innerHTML = data;
			$('#' + id).fadeIn('slow');
		}
	});
}

/**
* Show wishlist product managment page
*
* @return void
*/
function WishlistProductManage(id, action, id_wishlist, id_product, id_product_attribute, quantity, priority)
{
	$.ajax({
		type: 'GET',
		async: true,
		url: baseDir + 'modules/blockwishlist/managewishlist.php?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		data: 'action=' + action + '&id_wishlist=' + id_wishlist + '&id_product=' + id_product + '&id_product_attribute=' + id_product_attribute + '&quantity=' + quantity + '&priority=' + priority + '&refresh=' + true,
		cache: false,
		success: function(data)
		{
			if (action == 'delete')
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('fast');
			else if (action == 'update')
			{
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('fast');
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeIn('fast');
			}
			nb_products = 0;
			$("[id^='quantity']").each(function(index, element){
				nb_products += parseInt(element.value);
			});
			console.log(nb_products);
			$("#wishlist_"+id_wishlist).children('td').eq(1).html(nb_products);
		}
	});
}

/**
* Delete wishlist
*
* @return boolean succeed
*/
function WishlistDelete(id, id_wishlist, msg)
{
	var res = confirm(msg);
	if (res == false)
		return (false);

	$.ajax({
		type: 'GET',
		async: true,
		url: baseDir + 'modules/blockwishlist/mywishlist.php?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		cache: false,
		data: 'deleted&id_wishlist=' + id_wishlist,
		success: function(data)
		{
			var mywishlist_siblings_count = $('#' + id).siblings().length;
			$('#' + id).fadeOut('slow').remove();
			$("#block-order-detail").html('');
			if (mywishlist_siblings_count == 0)
				$("#block-history").remove();
		}
	});
}

/**
* Hide/Show bought product
*
* @return void
*/
function WishlistVisibility(bought_class, id_button)
{
	if ($('#hide' + id_button).css('display') == 'none')
	{
		$('.' + bought_class).slideDown('fast');
		$('#show' + id_button).hide();
		$('#hide' + id_button).css('display', 'block');
	}
	else
	{
		$('.' + bought_class).slideUp('fast');
		$('#hide' + id_button).hide();
		$('#show' + id_button).css('display', 'block');
	}
}

/**
* Send wishlist by email
*
* @return void
*/
function WishlistSend(id, id_wishlist, id_email)
{
	$.post(
		baseDir + 'modules/blockwishlist/sendwishlist.php',
		{
			token: static_token,
			id_wishlist: id_wishlist,
			email1: $('#' + id_email + '1').val(),
			email2: $('#' + id_email + '2').val(),
			email3: $('#' + id_email + '3').val(),
			email4: $('#' + id_email + '4').val(),
			email5: $('#' + id_email + '5').val(),
			email6: $('#' + id_email + '6').val(),
			email7: $('#' + id_email + '7').val(),
			email8: $('#' + id_email + '8').val(),
			email9: $('#' + id_email + '9').val(),
			email10: $('#' + id_email + '10').val()
		},
		function(data)
		{
			if (data)
			{
	            if (!!$.prototype.fancybox)
	                $.fancybox.open([
	                    {
	                        type: 'inline',
	                        autoScale: true,
	                        minHeight: 30,
	                        content: '<p class="fancybox-error">' + data + '</p>'
	                    }
	                ], {
	                    padding: 0
	                });
	            else
	                alert(data);
			}
			else
				WishlistVisibility(id, 'hideSendWishlist');
		}
	);
}

function wishlistProductsIdsAdd(id)
{
	if ($.inArray(parseInt(id),wishlistProductsIds) == -1)
		wishlistProductsIds.push(parseInt(id))
}

function wishlistProductsIdsRemove(id)
{
	wishlistProductsIds.splice($.inArray(parseInt(id),wishlistProductsIds), 1)
}

function wishlistRefreshStatus()
{
	$('.addToWishlist').each(function(){
		if ($.inArray(parseInt($(this).prop('rel')),wishlistProductsIds)!= -1)
			$(this).addClass('checked');
		else
			$(this).removeClass('checked');
	});        
}