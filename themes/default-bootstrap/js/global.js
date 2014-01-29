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

var responsiveflag = false;

$(document).ready(function(){
	highdpi_init();
	blockHover();
	responsiveResize();
	$(window).resize(responsiveResize);
	tmDropDown ('', '#header .current', 'ul.toogle_content', 'active');							// all of this should be defined or left empty brackets
	//tmDropDown ('cart', 'li#shopping_cart > a', '#cart_block', 'active');			// all of this should be defined or left empty brackets

	if (navigator.userAgent.match(/Android/i)) {
		var viewport = document.querySelector("meta[name=viewport]");
		viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
	}
	if (navigator.userAgent.match(/Android/i)) {
		window.scrollTo(0,1);
	}

	if (typeof page_name != 'undefined' && !in_array(page_name, ['index', 'product']))
	{
		var view = $.totalStorage('display');

		if (view && view != 'grid')
			display(view);
		else
			$('.display').find('li#grid').addClass('selected');

		$('.add_to_compare').click(function(e){
			e.preventDefault();
			if (typeof addToCompare != 'undefined')
				addToCompare(parseInt($(this).data('id-product')));
		});
		
		$('#grid').click(function(e){
			e.preventDefault();
			display('grid');
		});

		$('#list').click(function(e){
			e.preventDefault();
			display('list');
		});
	}
});

function highdpi_init()
{
	if($('.replace-2x').css('font-size') == "1px")
	{		
		var els = $("img.replace-2x").get();
		for(var i = 0; i < els.length; i++)
		{
			src = els[i].src;
			extension = src.substr( (src.lastIndexOf('.') +1) );
			src = src.replace("."+extension, "2x."+extension);
			
			var img = new Image();
			img.src = src;
			img.height != 0 ? els[i].src = src : els[i].src = els[i].src;
		}
	}
}

function blockHover(status) 
{
	$('.product_list.grid li.ajax_block_product').each(function() {
		$(this).find('.product-container').hover(
		function(){
			if ($('body').find('.container').width() == 1170){
				var pcHeight = $(this).parent().outerHeight();
				var pcPHeight = $(this).parent().find('.button-container').outerHeight() + $(this).parent().find('.comments_note').outerHeight() + $(this).parent().find('.functional-buttons').outerHeight();
				$(this).parent().addClass('hovered'),
				$(this).parent().css('height', pcHeight + pcPHeight).css('margin-bottom',pcPHeight*-1)
			}
		},
		function(){
			if ($('body').find('.container').width() == 1170)
			$(this).parent().removeClass('hovered').removeProp('style');
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

/*********************************************************** TMMenuDropDown **********************************/
function tmDropDown (elementType, elementClick, elementSlide, activeClass){
	elementType = elementType;           // special if hidden element isn't next (like for cart block here)
	elementClick = elementClick;         // element to click
	elementSlide =  elementSlide;        // element to show/hide
	activeClass = activeClass;			 // active class for "element to click"

	//show/hide elements
	$(elementClick).on('click touchstart', function(){
		if (elementType != 'cart')
			var subUl = $(this).next(elementSlide);
		else
			var subUl = $(this).parents('#header').find(elementSlide);
		if(subUl.is(':hidden')) {
			subUl.slideDown(),
			$(this).addClass(activeClass)	
		}
		else {
			subUl.slideUp(),
			$(this).removeClass(activeClass)
		}
		$(elementClick).not(this).next(elementSlide).slideUp(),
		$(elementClick).not(this).removeClass(activeClass);
		return false
	}),

	//enable clicks on showed elements
	$(elementSlide).on('click touchstart', function(e){
		e.stopPropagation();
	});

	// hide showed elements on document click
	$(document).on('click touchstart', function(){
		if (elementType != 'cart')
			var elementHide = $(elementClick).next(elementSlide);
		else
			var elementHide = $(elementClick).parents('#header').find(elementSlide);
			$(elementHide).slideUp(),
			$(elementClick).removeClass('active')
	})
}

//   TOGGLE FOOTER

function accordionFooter(status){
		if(status == 'enable'){
			$('#footer .footer-block h4').on('click', function(){
				$(this).toggleClass('active').parent().find('.toggle-footer').stop().slideToggle('medium');
			})
			$('#footer').addClass('accordion').find('.toggle-footer').slideUp('fast');
		}else{
			$('.footer-block h4').removeClass('active').off().parent().find('.toggle-footer').removeAttr('style').slideDown('fast');
			$('#footer').removeClass('accordion');
		}
	}

//   TOGGLE COLUMNS
function accordion(status){
		leftColumnBlocks = $('#left_column');
		if(status == 'enable'){
			$('#right_column .block:not(#layered_block_left) .title_block, #left_column .block:not(#layered_block_left) .title_block, #left_column #newsletter_block_left h4').on('click', function(){
				$(this).toggleClass('active').parent().find('.block_content').stop().slideToggle('medium');
			})
			$('#right_column, #left_column').addClass('accordion').find('.block:not(#layered_block_left) .block_content').slideUp('fast');
		}else{
			$('#right_column .block:not(#layered_block_left) .title_block, #left_column .block:not(#layered_block_left) .title_block, #left_column #newsletter_block_left h4').removeClass('active').off().parent().find('.block_content').removeAttr('style').slideDown('fast');
			$('#left_column, #right_column').removeClass('accordion');
		}
	}

function responsiveResize(){
	   if ($(document).width() <= 767 && responsiveflag == false){
	   		accordion('enable');
		    accordionFooter('enable');
			responsiveflag = true;	
		}
		else if ($(document).width() >= 768){
			accordion('disable');
			accordionFooter('disable');
	        responsiveflag = false;
		}
}