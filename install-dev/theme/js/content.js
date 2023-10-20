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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

$(document).ready(function() {
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
