/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
$(document).ready(function() {
	tinySetup({
		editor_selector :"autoload_rte",
		setup : function(ed) {
      ed.on('loadContent', function(ed, e) {
        handleCounterTiny(tinymce.activeEditor.id);
      });
			ed.on('change', function(ed, e) {
				tinyMCE.triggerSave();
        handleCounterTiny(tinymce.activeEditor.id);
			});
			ed.on('blur', function(ed) {
				tinyMCE.triggerSave();
			});
		}
	});

	function handleCounterTiny(id) {
    let textarea = $('#'+id);
    let counter = textarea.attr('counter');
    let counter_type = textarea.attr('counter_type');
    let max = tinyMCE.activeEditor.getBody().textContent.length;

    textarea.parent().find('span.currentLength').text(max);
    if ('recommended' !== counter_type && max > counter) {
      textarea.parent().find('span.maxLength').addClass('text-danger');
    } else {
      textarea.parent().find('span.maxLength').removeClass('text-danger');
    }
  }
});
