/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
$(document).ready(function() {
	tinySetup({
		editor_selector :"autoload_rte",
		setup : function(ed) {
			ed.on('keydown', function(ed, e) {
				tinyMCE.triggerSave();
				textarea = $('#'+tinymce.activeEditor.id);
				var max = textarea.parent('div').find('span.counter').data('max');
				if (max != 'none')
				{
					count = tinyMCE.activeEditor.getBody().textContent.length;
					rest = max - count;
					if (rest < 0)
						textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum '+ max +' caractères : '+rest+'</span>');
					else
						textarea.parent('div').find('span.counter').html(' ');
				}
			});
		}
	});
});
