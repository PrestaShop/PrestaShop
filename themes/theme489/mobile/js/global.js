// Allows to set the same height on ui-block element
// for #category-list items.
$( '.prestashop-page' ).live( 'pageshow',function(event)
{
	if ($('.ui-grid-a.same-height').length)
	{
		$('.ui-grid-a.same-height .ui-block-a').each(function()
		{
			if ($(this).height() != $(this).next('.ui-block-b').height())
			{
				var height1 = $(this).height();
				var height2 = $(this).next('.ui-block-b').height();
				if (height1 < height2) {
					$(this).height(height2).find('.ui-btn-inner.ui-li').height(height2);
					if ($(this).find('.ui-bar').length) {
						var less_h = [
							parseInt($(this).find('.ui-bar').css('padding-top')),
							parseInt($(this).find('.ui-bar').css('padding-bottom')),
							parseInt($(this).find('.ui-bar').css('border-top-width')),
							parseInt($(this).find('.ui-bar').css('border-bottom-width'))
						];
						$(this).find('.ui-bar').height(height2-less_h[0]-less_h[1]-less_h[2]-less_h[3]);
					}
				} else {
					$(this).next('.ui-block-b').height(height1).find('.ui-btn-inner.ui-li').height(height1);
				}
			}
		});
	}
});

$( '.prestashop-page' ).live( 'pageinit',function(event)
{
	if ($('.wrapper_pagination_mobile').length)
	{
		$('.wrapper_pagination_mobile').find('.disabled').live('click', function(e)
		{
			e.preventDefault();
			return false;
		});
	}
});