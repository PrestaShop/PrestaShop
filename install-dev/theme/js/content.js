/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(function() {
  const $modulesContainer = $('#modules-container');
  const $modulesList = $('input[name="modules[]"]');
  const $selectAllButton = $('input[name="select-all"]');
  const $searchInput = $('#search-for-module');

  $('input[name="module-action"]').on('change', function() {
    if ($(this).prop('checked') === false) {
      return;
    }

    if (parseInt($(this).val(), 10) === 1) {
      $modulesContainer.fadeIn();
    } else {
      $modulesContainer.fadeOut();
    }
  }).trigger('change');

  $selectAllButton.on('change', function() {
    $modulesList.prop('checked', $(this).prop('checked'));
  });

  $modulesList.on('click', function() {
    $selectAllButton.prop(
      'checked',
      $modulesList.filter(':checked').length === $modulesList.length
    );
  });

  $searchInput.on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $modulesContainer.find('dd').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    $modulesContainer.find('dt').each(function() {
      var $next = $(this).nextAll(':visible').slice(0, 1);
      // If the next visible item is also a <dt>, or there isn't a next visible item
      if ($next.is('dt') || $next.length == 0) {
        $(this).hide();
      } else {
        $(this).show();
      }
    });
  });
});
