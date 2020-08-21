/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import MerchandiseReturnEditPageMap from '@pages/merchandise-return/MerchandiseReturnEditPageMap';

const {$} = window;

export default class MerchandiseReturnProductRenderer {
  paginate(originalNumPage) {
    const $rows = $(MerchandiseReturnEditPageMap.productsTable).find(
      'tr[id^="merchandiseReturnProduct_"]',
    );
    const $customizationRows = $(
      MerchandiseReturnEditPageMap.productsTableCustomizationRows,
    );
    const $tablePagination = $(MerchandiseReturnEditPageMap.productsTablePagination);
    const numRowsPerPage = parseInt($tablePagination.data('numPerPage'), 10);
    const maxPage = Math.ceil($rows.length / numRowsPerPage);
    const numPage = Math.max(1, Math.min(originalNumPage, maxPage));
    this.paginateUpdateControls(numPage);
    // Hide all rows...
    $rows.addClass('d-none');
    $customizationRows.addClass('d-none');
    // ... and display good ones

    const startRow = (numPage - 1) * numRowsPerPage + 1;
    const endRow = numPage * numRowsPerPage;
    $(MerchandiseReturnEditPageMap.productsTable)
      .find(
        `tr[id^="merchandiseReturnProduct_"]:nth-child(n+${startRow}):nth-child(-n+${endRow})`,
      )
      .removeClass('d-none');

    $customizationRows.each(function () {
      if (
        !$(this)
          .prev()
          .hasClass('d-none')
      ) {
        $(this).removeClass('d-none');
      }
    });
  }

  paginateUpdateControls(numPage) {
    // Why 3 ? Next & Prev & Template
    const totalPage = $(MerchandiseReturnEditPageMap.productsTablePagination).find('li.page-item').length
      - 3;
    $(MerchandiseReturnEditPageMap.productsTablePagination)
      .find('.active')
      .removeClass('active');
    $(MerchandiseReturnEditPageMap.productsTablePagination)
      .find(`li:has(> [data-page="${numPage}"])`)
      .addClass('active');
    $(MerchandiseReturnEditPageMap.productsTablePaginationPrev).removeClass('disabled');
    if (numPage === 1) {
      $(MerchandiseReturnEditPageMap.productsTablePaginationPrev).addClass('disabled');
    }
    $(MerchandiseReturnEditPageMap.productsTablePaginationNext).removeClass('disabled');
    if (numPage === totalPage) {
      $(MerchandiseReturnEditPageMap.productsTablePaginationNext).addClass('disabled');
    }
    this.togglePaginationControls();
  }

  togglePaginationControls() {
    // Why 3 ? Next & Prev & Template
    const totalPage = $(MerchandiseReturnEditPageMap.productsTablePagination).find('li.page-item').length
      - 3;
    $(MerchandiseReturnEditPageMap.productsNavPagination).toggleClass(
      'd-none',
      totalPage <= 1,
    );
  }
}
