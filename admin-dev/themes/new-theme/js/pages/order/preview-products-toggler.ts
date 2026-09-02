/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Toggles hidden products in order preview block.
 *
 * @param {jQuery} $gridContainer
 */
export default function previewProductsToggler($row: JQuery): void {
  toggleStockLocationColumn($row);
  $row.on('click', '.js-preview-more-products-btn', (event: JQueryEventObject) => {
    event.preventDefault();

    const $btn = $(event.currentTarget);
    const $hiddenProducts = $btn.closest('tbody').find('.js-product-preview-more');

    $hiddenProducts.removeClass('d-none');
    $btn.closest('tr').remove();
    toggleStockLocationColumn($row);
  });
}

function toggleStockLocationColumn($container: JQuery): void {
  let showColumn = false;
  $(
    '.js-cell-product-stock-location',
    // eslint-disable-next-line
    $container.find('tr:not(.d-none)')).filter('td').each((index, element) => {
    if ($(element).html().trim() !== '') {
      showColumn = true;
      return false;
    }
  },
  );

  $('.js-cell-product-stock-location', $container).toggle(showColumn);
}
