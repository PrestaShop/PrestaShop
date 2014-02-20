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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$(document).ready(function(){

	$(document).delegate('.add_to_compare','click', function(e){
		e.preventDefault();
		if (typeof addToCompare != 'undefined')
			addToCompare(parseInt($(this).data('id-product')));
	});

	reloadProductComparison();
	compareButtonsStatusRefresh();
	totalCompareButtons();
});

function addToCompare(productId)
{
	var totalValueNow = parseInt($('.bt_compare').next('.compare_product_count').val());
	var action, totalVal;
	if($.inArray(parseInt(productId),comparedProductsIds) === -1)
		action = 'add';
	else
		action = 'remove';

	$.ajax({
		url: 'index.php?controller=products-comparison&ajax=1&action='+action+'&id_product=' + productId,
		async: true,
		cache: false,
		success: function(data) {
			if (action === 'add' && comparedProductsIds.length < comparator_max_item) {
				comparedProductsIds.push(parseInt(productId)),
				compareButtonsStatusRefresh(),
				totalVal = totalValueNow +1,
				$('.bt_compare').next('.compare_product_count').val(totalVal),
				totalValue(totalVal);
			}
			else if (action === 'remove') {
				comparedProductsIds.splice($.inArray(parseInt(productId),comparedProductsIds), 1),
				compareButtonsStatusRefresh(),
				totalVal = totalValueNow -1,
				$('.bt_compare').next('.compare_product_count').val(totalVal),
				totalValue(totalVal);
			}
			else {
				alert(max_item);
			}
			totalCompareButtons();
		},
		error: function(){}
	});
}

function reloadProductComparison()
{
	$('a.cmp_remove').click(function() {
		var idProduct = parseInt($(this).data('id-product'));
		$.ajax({
			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
			async: false,
			cache: false,
			success: function(){
				return true;
			}
		});
	});
};

function compareButtonsStatusRefresh()
{
	$('.add_to_compare').each(function() {
		if ($.inArray(parseInt($(this).data('id-product')),comparedProductsIds)!== -1)
			$(this).addClass('checked');
		else
			$(this).removeClass('checked');
	});
}

function totalCompareButtons()
{
	var totalProductsToCompare = parseInt($('.bt_compare .total-compare-val').html());
	if (typeof totalProductsToCompare !== "number" || totalProductsToCompare === 0)
		$('.bt_compare').attr("disabled",true);
	else
		$('.bt_compare').attr("disabled",false);
}

function totalValue(value)
{
	$('.bt_compare').find('.total-compare-val').html(value);
}