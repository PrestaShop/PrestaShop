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

function toggleShopModuleCheckbox(id_shop, toggle){
	var formGroup = $("[for='to_disable_shop"+id_shop+"']").parent();
	if (toggle === true) {
		formGroup.removeClass('hide');
		formGroup.find('input').each(function(){$(this).prop('checked', 'checked');});
	}
	else {
		formGroup.addClass('hide');
		formGroup.find('input').each(function(){$(this).prop('checked', '');});
	}
}

$(function(){
  $('div.thumbnail-wrapper').on(
    'mouseenter',
    function() {
      var w = $(this).parent('div').outerWidth(true);
      var h = $(this).parent('div').outerHeight(true);
      $(this).children('.action-wrapper').css('width', w + 'px');
      $(this).children('.action-wrapper').css('height', h + 'px');
      $(this).children('.action-wrapper').show();
    })
    .on(
      'mouseleave',
      function() {
        $('.thumbnail-wrapper .action-wrapper').hide();
      }
    );

	$("[name^='checkBoxShopGroupAsso_theme']").on('change', function(){
		$(this).parents('.tree-folder').find("[name^='checkBoxShopAsso_theme']").each(function(){
			var id = $(this).attr('value');
			var checked = $(this).prop('checked');
			toggleShopModuleCheckbox(id, checked);
		});
	});
	$("[name^='checkBoxShopAsso_theme']").on('click', function(){
		var id = $(this).attr('value');
		var checked = $(this).prop('checked');
		toggleShopModuleCheckbox(id, checked);
	});
});
