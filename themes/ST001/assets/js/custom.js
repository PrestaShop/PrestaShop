/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */



        // Parallax Background Image Js Start
		function ParallaxImage(){
			var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
			if(!isMobile) {
				if($(".custom-parallaxblock").length){  $(".custom-parallaxblock").ParallaxBackground({  invert: false });};    
			}else{
				$(".custom-parallaxblock").ParallaxBackground({  invert: true });	
			}	
		}
		
		
		
		
		// Common Slider Image
		function initialize_owl(el) {
			el.owlCarousel({
					items: 4,
					mouseDrag: true,
					touchDrag: true,
					autoplay: false,
					responsive:{
						0:{  
								items:2  
						},
						480:{  
								items:2
						},
						640:{  
								items:3
						},
						991:{
								items:3	
						},
						1200:{
								items:4
						}
						
					},
					nav:true,
					loop:true,
					navText:[
						"<div class='prev-arrow'></div>",
						"<div class='next-arrow'></div>"
					]
			});
		}

		// Common Slider Image
		function initialize_cartowl(el) {
			el.owlCarousel({
					items: 3,
					mouseDrag: true,
					touchDrag: true,
					autoplay: false,
					responsive:{
						0:{  
								items:2  
						},
						480:{  
								items:2
						},
						640:{  
								items:3
						},
						991:{
								items:3	
						},
						1200:{
								items:3
						}
						
					},
					nav:true,
					loop:true,
					navText:[
						"<div class='prev-arrow'></div>",
						"<div class='next-arrow'></div>"
					]
			});
		}
		
		function initialize_brandowl(el) {
			el.owlCarousel({
					items: 5,
					mouseDrag: true,
					touchDrag: true,
					autoplay: true,
					responsive:{
						0:{  
								items:2  
						},
						480:{  
								items:3
						},
						640:{  
								items:4
						},
						991:{
								items:4	
						},
						1200:{
								items:5
						}
						
					},
					nav:true,
					loop:true,
					navText:[
						"<div class='prev-arrow'></div>",
						"<div class='next-arrow'></div>"
					]
			});
		}
		
		// Single Image owl Slider
		function initialize_oneowl(elsingle) {
			elsingle.owlCarousel({
					items: 1,
					mouseDrag: true,
					touchDrag: true,
					autoplay: true,
					singleItem:true,
					nav:true,
					loop:true,
					navText:[
						"<div class='prev-arrow'></div>",
						"<div class='next-arrow'></div>"
					]
			});
	
	   }
	   

function bindGrid()
{
	var view = $.totalStorage('display');

	if (view && view != 'list')
		display(view);
	else
		$('.display').find('#grid').addClass('selected');

	$(document).on('click', '#grid', function(e){
		e.preventDefault();
		$('#products div.products').animate({ opacity: 0 }, 400);
		setTimeout(function() { display('grid'); }, 400)
		$('#products div.products').animate({ opacity: 1 }, 400);
	});

	$(document).on('click', '#list', function(e){
		e.preventDefault();
		$('#products div.products').animate({ opacity: 0 }, 400)
		setTimeout(function() {display('list');  }, 400)
		$('#products div.products').animate({ opacity: 1 }, 400);
	});
}

function display(view)
{
	if (view == 'list')
	{
		$('#products div.products').removeClass('grid').addClass('list');
		$('#products div.products > article').removeClass('col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-4').addClass('col-xs-12');
		$('#products div.products > article').each(function(index, element) {
			var html = '';
			html = '<div class="thumbnail-container">';
				html += '<div class="row">';
					html += '<div class="thumbnail-inner col-xs-12 col-sm-5 col-md-5 col-lg-4">' + $(element).find('.thumbnail-inner').html() + '</div>';
					html += '<div class="product-description col-xs-12 col-sm-7 col-md-7 col-lg-8">';
						html += '<h1 class="h3 product-title" itemprop="name">'+ $(element).find('.product-title').html() + '</h1>';
						var price = $(element).find('.product-price-and-shipping').html();
						if (price != null) {
							html += '<div class="product-price-and-shipping">'+ price + '</div>';
						}
						html += '<p class="product-desc" itemprop="description">'+ $(element).find('.product-desc').text() + '</p>';
						var colorList = $(element).find('.highlighted-informations').html();
						if (colorList != null) {
							html += '<div class="highlighted-informations hidden-sm-down">'+ colorList +'</div>';
						}
						html += '<div class="product-add-to-cart addtocart-button">'+ $(element).find('.product-add-to-cart').html() +'</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			$(element).html(html);
		});
		$('.display').find('li#list').addClass('selected');
		$('.display').find('li#grid').removeAttr('class');
		$.totalStorage('display', 'list');
	}
	else
	{
		$('#products div.products').removeClass('list').addClass('grid');
		$('#products div.products > article').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-4');
		$('#products div.products > article').each(function(index, element) {
			var html = '';
			html += '<div class="thumbnail-container">';
				html += '<div class="thumbnail-inner">' + $(element).find('.thumbnail-inner').html() + '</div>';
				
				html += '<div class="product-description">';
					html += '<h1 class="h3 product-title" itemprop="name">'+ $(element).find('.product-title').html() + '</h1>';
					var price = $(element).find('.product-price-and-shipping').html();
					if (price != null) {
						html += '<div class="product-price-and-shipping">'+ price + '</div>';
					}
				//	html += '<p class="product-desc" itemprop="description">'+ $(element).find('.product-desc').html() + '</p>';
					var colorList = $(element).find('.highlighted-informations').html();
					if (colorList != null) {
						html += '<div class="highlighted-informations hidden-sm-down">'+ colorList +'</div>';
					}
					html += '<div class="product-add-to-cart">'+ $(element).find('.product-add-to-cart').html() +'</div>';
				html += '</div>';
			
			html += '</div>';
			$(element).html(html);
		});
		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$.totalStorage('display', 'grid');
	}
}



function leftRightColumn(){
	
	if ($(document).width() <= 767)
	{
		$('#wrapper .container > .row #left-column').appendTo('#wrapper .container > .row');
	}
	else if($(document).width() >= 768)
	{
		$('#wrapper .container > .row #left-column').prependTo('#wrapper .container > .row');
	}
}

	function searchwidget(){
	
	if ($(document).width() <= 767)
	{
		$('.right-nav #search_widget').appendTo('.header-top');
	}
	else if($(document).width() >= 768)
	{
		$('.header-top #search_widget').prependTo('.right-nav');
	}
}

function initializeAddtocart(){
	var carturl = $('#carturl').val();
	var carttoken = $('#carttoken').val();
	//alert(carttoken);
	$('.cart-form-url').attr('action', carturl);
	$('.cart-form-token').attr('value', carttoken);
}

$(document).ready(function (){	
	// Common Slider Image Function Call
	initialize_owl($('#bestsellers-carousel'));
	initialize_owl($('#newproducts-carousel'));
	initialize_owl($('.page-index #featuredproducts-carousel'));
	initialize_owl($('#specialproducts-carousel'));
	initialize_owl($('#accessories-carousel'));
	

	initialize_cartowl($('.page-cart  #featuredproducts-carousel'));
	// Brand slider
	initialize_brandowl($('#brand-carousel'));
	
	// Single Image owl Slider Dunction Call
	initialize_oneowl($('#testimonial-slider'));
	initialize_oneowl($('#homepage-carousel'));
	
	// Parallax Image Slider Call
	ParallaxImage();
	
	// Parallax Image Slider Call
	initializeAddtocart();
	
	
	// Left And right Column After Wrapper
	leftRightColumn(); 
	
	searchwidget();
	
	// Grid lIst change
	bindGrid();
	
	 $('.user-icon').on("click", function(){
		$(this).parent().find('.userinfo-toggle').slideToggle();												
	 });
	 
	 $( ".search-widget input[type=text]" ).focus(function() {
		$(this).parent().parent().addClass("inputfocus");
	});
	$( ".search-widget input[type=text]" ).blur(function() {
		$(this).parent().parent().removeClass("inputfocus");
	});
});
$( window ).resize(function() {
	// Single Image owl Slider Dunction Call
	initialize_oneowl($('#testimonial-slider'));	
	initialize_oneowl($('#homepage-carousel'));
	
	// Left And right Column After Wrapper
	leftRightColumn(); 
	
	searchwidget();
	
});							


