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
			$(elementHide).slideUp();
	})
};
$(document).ready(function(){ 
	tmDropDown ('', '#header .current', 'ul.toogle_content', 'active');							// all of this should be defined or left empty brackets
	//tmDropDown ('cart', 'li#shopping_cart > a', '#cart_block', 'active');			// all of this should be defined or left empty brackets
});
