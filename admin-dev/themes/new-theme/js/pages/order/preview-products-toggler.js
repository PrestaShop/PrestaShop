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

const $ = window.$;

/**
 * Toggles hidden products in order preview block.
 *
 * @param {jQuery} $gridContainer
 */
export default function previewProductsToggler($row) {
  toggleStockLocationColumn($row);
  $row.on('click', '.js-preview-more-products-btn', (event) => {
    event.preventDefault();

    const $btn = $(event.currentTarget);
    const $hiddenProducts = $btn.closest('tbody').find('.js-product-preview-more');

    $hiddenProducts.removeClass('d-none');
    $btn.closest('tr').remove();
    toggleStockLocationColumn($row);
  });
}

function toggleStockLocationColumn($container) {
  let showColumn = false;
  $('.js-cell-product-stock-location', $container.find('tr:not(.d-none)')).filter('td').each((index, element) => {
    if ($(element).html().trim() !== '') {
      showColumn = true;
      return false;
    }
    return true;
  });
  $('.js-cell-product-stock-location', $container).toggle(showColumn);
}
