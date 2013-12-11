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
};
$(document).ready(function(){ 
	tmDropDown ('', '#header .current', 'ul.toogle_content', 'active');							// all of this should be defined or left empty brackets
	//tmDropDown ('cart', 'li#shopping_cart > a', '#cart_block', 'active');			// all of this should be defined or left empty brackets
});


var responsiveflag = false;

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
	   		accordion('enable'),
		    accordionFooter('enable'),
			responsiveflag = true	
		}
		else if ($(document).width() >= 768){
			accordion('disable'),
			accordionFooter('disable'),
	        responsiveflag = false
		}
}

$(document).ready(responsiveResize);
$(window).resize(responsiveResize);

//replace top banner to top of page, before #header
$(document).ready(function(){
	topBanner = $('body').find('#banner_block_top');
	topBanner.remove();
	topBanner.insertBefore('#header');
});