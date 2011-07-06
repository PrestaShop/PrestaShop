google.load('language', '1');
var display_once = 0;
var output_error = '<div class="gg-errors-output error" style="margin:10px;"></div>';
var gg_current_bt = {};
var current_translate = '';
$(function()
{
	// @see gg_language_code declaration in AdminTranslations::displayAutoTranslate() method.
	if (!ggIsTranslatable(gg_translate['language_code']))
	{
		setErrorMessage('"'+gg_translate['language_code']+'" : '+gg_translate['not_available']);
	}
	else
	{
		$('#content').find('input[type="text"], textarea').each(function()
		{
			$(this).after('<a class="button-translate" title="'+gg_translate['tooltip_title']+'|wait..." ><img src="../img/admin/page_world.png" alt="'+gg_translate['tooltip_title']+'"></a>');
		});
		$('.button-translate').mouseover(function()
		{
			gg_current_bt = $(this);
		})
		.click(function(e)
		{
			var field = $(this).prev();
			if (current_translate != '')
			{
				if (field.is('input[type="text"]')) field.val(current_translate);
				if (field.is('textarea')) field.html(current_translate);
			}
		})
		.cluetip({
			splitTitle: '|', // use the invoking element's title attribute to populate the clueTip...
							 // ...and split the contents into separate divs where there is a "|"
			showTitle: true, // hide the clueTip's heading
			dropShadow: false,
			onShow : function(ct, c_inner)
			{
				current_translate = '';
				$('#cluetip-inner').html('<img src="../img/loader.gif" alt="loader" />');
				var button = gg_current_bt;
				if (button.parent("td").prev().html())
				{
					google.language.translate(button.parent("td").prev().html(), 'en', gg_translate['language_code'], function(result)
					{
						if (!result.error)
						{
							current_translate = result.translation.replace('&#39;', '\'');
							while (current_translate != (current_translate = current_translate.replace('&#39;', '\'')));
							$('#cluetip-inner').html(current_translate);
						}
						else if (display_once == 0)
						{
							display_once = 1;
							$('#cluetip-inner').html('<span class="error">'+result.error.message+'</span>');
							current_translate = '';
						}
					});
				}
			}
		});
	}
});
function ggIsTranslatable(iso_lang)
{
	if(iso_lang.length == 2)
		return google.language.isTranslatable(iso_lang);
	else if (iso_lang.length > 2)
	{
		iso_lang = iso_lang.substring(0, 2);
		gg_translate['language_code'] = iso_lang;
		return ggIsTranslatable(iso_lang);
	}
}
function setErrorMessage(string)
{
	$('#content .path_bar').after(output_error);
	$('#content .gg-errors-output:eq(0)').html(string);
}