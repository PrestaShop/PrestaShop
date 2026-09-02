/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
