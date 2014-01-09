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
$('document').ready(function(){
	if (typeof blockHover != 'undefined')
		blockHover();
	if (typeof reloadProductComparison != 'undefined')
		reloadProductComparison();
	if (typeof page_name != 'undefined' && !in_array(page_name, ['index', 'product']))
	{
		var view = $.totalStorage('display');
		if (view)
			display(view);
		else
			display('grid');

		$('.add_to_compare').click(function(e){
			if (typeof addToCompare != 'undefined')
				addToCompare(parseInt($(this).data('id-product')));
			e.preventDefault();
		});
	}
});
function blockHover(status) 
{
	$('.product_list.grid li.ajax_block_product').each(function() {
		$(this).find('.product-container').hover(
		function(){
			if ($('body').find('.container').width() == 1170)
			$(this).parent().addClass('hovered'); 
		},
		function(){
			if ($('body').find('.container').width() == 1170)
			$(this).parent().removeClass('hovered');
		}
	)});	
}
function display(view)
{
	if (view == 'list')
	{
		$('ul.product_list').removeClass('grid').addClass('list row');
		$('.product_list > li').removeClass('col-xs-12 col-sm-6 col-md-4').addClass('col-xs-12');
		$('.product_list > li').each(function(index, element) {
			html = '';
			html = '<div class="product-container"><div class="row">';
				html += '<div class="left-block col-xs-4 col-xs-5 col-md-4">' + $(element).find('.left-block').html() + '</div>';
				html += '<div class="center-block col-xs-4 col-xs-7 col-md-4">';
					html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
					html += '<h5 itemprop="name">'+ $(element).find('h5').html() + '</h5>';
					var rating = $(element).find('.comments_note').html(); // check : rating
					if (rating != null) { 
						html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">'+ rating + '</div>';
					}
					html += '<p class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
					var colorList = $(element).find('.color-list-container').html();
					if (colorList != null) {
						html += '<div class="color-list-container">'+ colorList +'</div>';
					}
					var availability = $(element).find('.availability').html();	// check : catalog mode is enabled
					if (availability != null) {
						html += '<span class="availability">'+ availability +'</span>';
					}
				html += '</div>';	
				html += '<div class="right-block col-xs-4 col-xs-12 col-md-4"><div class="right-block-content row">';
					var price = $(element).find('.content_price').html();       // check : catalog mode is enabled
					if (price != null) { 
						html += '<div class="content_price col-xs-5 col-md-12">'+ price + '</div>';
					}
					html += '<div class="button-container col-xs-7 col-md-12">'+ $(element).find('.button-container').html() +'</div>';
					html += '<div class="functional-buttons clearfix col-sm-12">' + $(element).find('.functional-buttons').html() + '</div>';
				html += '</div>';
			html += '</div></div>';
		$(element).html(html);
		});		
		$('.display').find('li#list').addClass('selected');
		$('.display').find('li#grid').removeAttr('class');
		$.totalStorage('display', 'list');
		if (typeof ajaxCart != 'undefined')      // cart button reload
			ajaxCart.overrideButtonsInThePage();
		if (typeof quick_view != 'undefined') 	// qick view button reload
			quick_view();
	}
	else 
	{
		$('ul.product_list').removeClass('list').addClass('grid row');
		$('.product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-4');
		$('.product_list > li').each(function(index, element) {
		html = '';
		html += '<div class="product-container">';
			html += '<div class="left-block">' + $(element).find('.left-block').html() + '</div>';
			html += '<div class="right-block">';
				html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
				html += '<h5 itemprop="name">'+ $(element).find('h5').html() + '</h5>';
				var rating = $(element).find('.comments_note').html(); // check : rating
					if (rating != null) { 
						html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">'+ rating + '</div>';
					}
				html += '<p itemprop="description" class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
				var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
					if (price != null) { 
						html += '<div class="content_price">'+ price + '</div>';
					}
				html += '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="button-container">'+ $(element).find('.button-container').html() +'</div>';
				var colorList = $(element).find('.color-list-container').html();
				if (colorList != null) {
					html += '<div class="color-list-container">'+ colorList +'</div>';
				}
				var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
				if (availability != null) {
					html += '<span class="availability">'+ availability +'</span>';
				}
			html += '</div>';
			html += '<div class="functional-buttons clearfix">' + $(element).find('.functional-buttons').html() + '</div>';
		html += '</div>';		
		$(element).html(html);
		});
		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$.totalStorage('display', 'grid');			
		if (typeof ajaxCart != 'undefined') 	// cart button reload
			ajaxCart.overrideButtonsInThePage();
		if (typeof quick_view != 'undefined') 	// qick view button reload
			quick_view();
		blockHover();
	}	
}