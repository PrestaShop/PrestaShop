$(document).ready(function(){
	var message = $('.toolbarHead');
	var view = $(window);

	// bind only if message exists. placeholder will be its parent
	view.bind("scroll resize", function(e)
	{
		message.each(function(el){
			if (message.length)
			{
				placeholder = $(this).parent();
				if(e.type == 'resize')
					$(this).css('width', $(this).parent().width());

				placeholderTop = placeholder.offset().top;
				var viewTop = view.scrollTop() + 15;
				// here we force the toolbar to be "not fixed" when
				// the height of the window is really small (toolbar hiding the page is not cool)
				window_is_more_than_twice_the_toolbar  = view.height() > message.parent().height() * 2;
				if (!$(this).hasClass("fix-toolbar") && (window_is_more_than_twice_the_toolbar && (viewTop > placeholderTop)))
				{
					$(this).css('width', $(this).width());
					// fixing parent height will prevent that annoying "pagequake" thing
					// the order is important : this has to be set before adding class fix-toolbar 
					$(this).parent().css('height', $(this).parent().height());
					$(this).addClass("fix-toolbar");
				}
				else if ($(this).hasClass("fix-toolbar") && (!window_is_more_than_twice_the_toolbar || (viewTop <= placeholderTop)) )
				{
					$(this).removeClass("fix-toolbar");
					$(this).removeAttr('style');
					$(this).parent().removeAttr('style');
				}
			}
		});
	}); // end bind

	// if count errors
	$('#hideError').live('click', function(e)
	{
		e.preventDefault();
		$('.error').hide('slow', function (){
			$('.error').remove();
		});
		return false;
	});

	// if count warnings
	$('#linkSeeMore').live('click', function(e){
		e.preventDefault();
		$('.warn #seeMore').show();
		$(this).hide();
		$('.warn #linkHide').show();
		return false;
	});
	$('#linkHide').live('click', function(e){
		e.preventDefault();
		$('.warn #seeMore').hide();
		$(this).hide();
		$('.warn #linkSeeMore').show();
		return false;
	});
	$('#hideWarn').live('click', function(e){
		e.preventDefault();
		$('.warn').hide('slow', function (){
			$('.warn').remove();
		});
		return false;
	});
});