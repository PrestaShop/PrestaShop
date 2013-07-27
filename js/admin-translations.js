/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var displayOnce = 0;
google.load("language", "1");
function translateAll() {
	if (!ggIsTranslatable(gg_translate['language_code']))
		alert('"'+gg_translate['language_code']+'" : '+gg_translate['not_available']);
	else
	{
		$.each($('input[type="text"]'), function() {
			var tdinput = $(this);
			if (tdinput.attr("value") == "" && tdinput.parent("td").prev().html()) {
				google.language.translate(tdinput.parent("td").prev().html(), "en", gg_translate['language_code'], function(result) {
					if (!result.error)
						tdinput.val(result.translation);
					else if (displayOnce == 0)
					{
						displayOnce = 1;
						alert(result.error.message);
					}
				});
			}
		});
		$.each($("textarea"), function() {
			var tdtextarea = $(this);
			if (tdtextarea.html() == "" && tdtextarea.parent("td").prev().html()) {
				google.language.translate(tdtextarea.parent("td").prev().html(), "en", gg_translate['language_code'], function(result) {
					if (!result.error)
						tdtextarea.html(result.translation);
					else if (displayOnce == 0)
					{
						displayOnce = 1;
						alert(result.error.message);
					}
				});
			}
		});
	}
}

$(document).ready(function(){$('#translate_all').bind('click', function(){
	translateAll();
})});
