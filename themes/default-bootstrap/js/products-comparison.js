/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

reloadProductComparison = function() {
	$('input:checkbox.comparator').each(function() {
        var checkedCheckbox = $(this);
		if (checkedCheckbox.is(':checked'))
			checkedCheckbox.parent().addClass('checked');
    });
	$('a.cmp_remove').click(function(){

		var idProduct = $(this).prop('rel').replace('ajax_id_product_', '');

		$.ajax({
  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
 			async: false,
 			cache: false,
  			success: function(){
				return true;
			}
		});	
	});
	$('input:checkbox.comparator').click(function(){
		var totalValueNow = parseInt($('.bt_compare').next('.compare_product_count').val());
		var idProduct = $(this).prop('value').replace('comparator_item_', '');
		var checkbox = $(this);
		if(checkbox.is(':checked'))
		{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=add&id_product=' + idProduct,
	 			async: true,
	 			cache: false,
	  			success: function(data){
	  				if (data === '0')
	  				{
	  					checkbox.prop('checked', false);
						alert(max_item);
					}
					else {
							checkbox.prop('checked', true),
							checkbox.parent().addClass('checked'),
							totalVal = totalValueNow +1,
							$('.bt_compare').next('.compare_product_count').val(totalVal),
							totalValue(totalVal)
					}
	  			},
	    		error: function(){
	    			checkbox.prop('checked', false);
	    		}
			});	
		}
		else
		{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
	 			async: true,
	 			cache: false,
	  			success: function(data){
	  				if (data === '0')
					
	  					checkbox.prop('checked', true);
						checkbox.parent().removeClass('checked');
						totalVal = totalValueNow -1;
						$('.bt_compare').next('.compare_product_count').val(totalVal),
						totalValue(totalVal)
	    		}, 
	    		error: function(){
	    			checkbox.prop('checked', true);
	    		}
			});	
		}
	});
}
function totalValue(value) {
	$('.bt_compare').find('.total-compare-val').html(value);
}