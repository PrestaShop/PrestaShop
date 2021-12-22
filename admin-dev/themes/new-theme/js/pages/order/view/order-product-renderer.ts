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

import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderProductEdit from '@pages/order/view/order-product-edit';
import Router from '@components/router';

const {$} = window;

export default class OrderProductRenderer {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  addOrUpdateProductToList($productRow: JQuery, newRow: HTMLElement): void {
    if ($productRow.length > 0) {
      $productRow.html($(newRow).html());
    } else {
      $(OrderViewPageMap.productAddRow).before(
        $(newRow)
          .hide()
          .fadeIn(),
      );
    }
  }

  updateNumProducts(numProducts: number): void {
    $(OrderViewPageMap.productsCount).html(<string>(<unknown>numProducts));
  }

  editProductFromList(
    orderDetailId: number,
    quantity: number,
    priceTaxIncl: number,
    priceTaxExcl: number,
    taxRate: number,
    location: string,
    availableQuantity: number,
    availableOutOfStock: string,
    orderInvoiceId: string,
    isOrderTaxIncluded: number,
  ): void {
    const $orderEdit = new OrderProductEdit(orderDetailId);
    $orderEdit.displayProduct({
      price_tax_excl: priceTaxExcl,
      price_tax_incl: priceTaxIncl,
      tax_rate: taxRate,
      quantity,
      location,
      availableQuantity,
      availableOutOfStock,
      orderInvoiceId,
      isOrderTaxIncluded,
    });
    $(OrderViewPageMap.productAddActionBtn).addClass('d-none');
    $(OrderViewPageMap.productAddRow).addClass('d-none');
  }

  moveProductsPanelToModificationPosition(scrollTarget = 'body'): void {
    $(OrderViewPageMap.productActionBtn).addClass('d-none');
    $(
      `${OrderViewPageMap.productAddActionBtn}, ${OrderViewPageMap.productAddRow}`,
    ).removeClass('d-none');
    this.moveProductPanelToTop(scrollTarget);
  }

  moveProductsPanelToRefundPosition(): void {
    this.resetAllEditRows();
    $(
      /* eslint-disable-next-line max-len */
      `${OrderViewPageMap.productAddActionBtn}, ${OrderViewPageMap.productAddRow}, ${OrderViewPageMap.productActionBtn}`,
    ).addClass('d-none');
    this.moveProductPanelToTop();
  }

  moveProductPanelToTop(scrollTarget = 'body'): void {
    const $modificationPosition = $(
      OrderViewPageMap.productModificationPosition,
    );

    if ($modificationPosition.find(OrderViewPageMap.productsPanel).length > 0) {
      return;
    }
    $(OrderViewPageMap.productsPanel)
      .detach()
      .appendTo($modificationPosition);
    $modificationPosition.removeClass('d-none');

    // Show column location & refunded
    this.toggleColumn(OrderViewPageMap.productsCellLocation);
    this.toggleColumn(OrderViewPageMap.productsCellRefunded);

    // Show all rows, hide pagination controls
    const $rows = $(OrderViewPageMap.productsTable).find(
      'tr[id^="orderProduct_"]',
    );
    $rows.removeClass('d-none');
    $(OrderViewPageMap.productsPagination).addClass('d-none');

    const target = $(scrollTarget).offset();
    const headerBarHeight = $('.header-toolbar').height();

    if (target && headerBarHeight) {
      const scrollValue = target.top - headerBarHeight - 100;
      $('html,body').animate({scrollTop: scrollValue}, 'slow');
    }
  }

  moveProductPanelToOriginalPosition(): void {
    $(OrderViewPageMap.productAddNewInvoiceInfo).addClass('d-none');
    $(OrderViewPageMap.productModificationPosition)
      .addClass('d-none');

    $(OrderViewPageMap.productsPanel)
      .detach()
      .appendTo(OrderViewPageMap.productOriginalPosition);

    $(OrderViewPageMap.productsPagination).removeClass('d-none');
    $(OrderViewPageMap.productActionBtn).removeClass('d-none');
    $(
      `${OrderViewPageMap.productAddActionBtn}, ${OrderViewPageMap.productAddRow}`,
    ).addClass('d-none');

    // Restore pagination
    this.paginate(1);
  }

  resetAddRow(): void {
    $(OrderViewPageMap.productAddIdInput).val('');
    $(OrderViewPageMap.productSearchInput).val('');
    $(OrderViewPageMap.productAddCombinationsBlock).addClass('d-none');
    $(OrderViewPageMap.productAddCombinationsSelect).val('');
    $(OrderViewPageMap.productAddCombinationsSelect).prop('disabled', false);
    $(OrderViewPageMap.productAddPriceTaxExclInput).val('');
    $(OrderViewPageMap.productAddPriceTaxInclInput).val('');
    $(OrderViewPageMap.productAddQuantityInput).val('');
    $(OrderViewPageMap.productAddAvailableText).html('');
    $(OrderViewPageMap.productAddLocationText).html('');
    $(OrderViewPageMap.productAddNewInvoiceInfo).addClass('d-none');
    $(OrderViewPageMap.productAddActionBtn).prop('disabled', true);
  }

  resetAllEditRows(): void {
    $(OrderViewPageMap.productEditButtons).each((key, editButton) => {
      this.resetEditRow($(editButton).data('orderDetailId'));
    });
  }

  resetEditRow(orderProductId: string): void {
    const $productRow = $(OrderViewPageMap.productsTableRow(orderProductId));
    const $productEditRow = $(
      OrderViewPageMap.productsTableRowEdited(orderProductId),
    );
    $productEditRow.remove();
    $productRow.removeClass('d-none');
  }

  paginate(originalNumPage: number): void {
    const $rows = $(OrderViewPageMap.productsTable).find(
      'tr[id^="orderProduct_"]',
    );
    const $customizationRows = $(
      OrderViewPageMap.productsTableCustomizationRows,
    );
    const $tablePagination = $(OrderViewPageMap.productsTablePagination);
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

    for (let i = startRow - 1; i < Math.min(endRow, $rows.length); i += 1) {
      $($rows[i]).removeClass('d-none');
    }

    $customizationRows.each(function () {
      if (
        !$(this)
          .prev()
          .hasClass('d-none')
      ) {
        $(this).removeClass('d-none');
      }
    });

    // Remove all edition rows (careful not to remove the template)
    $(OrderViewPageMap.productEditRow)
      .not(OrderViewPageMap.productEditRowTemplate)
      .remove();

    // Toggle Column Location & Refunded
    this.toggleColumn(OrderViewPageMap.productsCellLocationDisplayed);
    this.toggleColumn(OrderViewPageMap.productsCellRefundedDisplayed);
  }

  paginateUpdateControls(numPage: number): void {
    // Why 3 ? Next & Prev & Template
    const totalPage = $(OrderViewPageMap.productsTablePagination).find('li.page-item').length
      - 3;
    $(OrderViewPageMap.productsTablePagination)
      .find('.active')
      .removeClass('active');
    $(OrderViewPageMap.productsTablePagination)
      .find(`li:has(> [data-page="${numPage}"])`)
      .addClass('active');
    $(OrderViewPageMap.productsTablePaginationPrev).removeClass('disabled');
    if (numPage === 1) {
      $(OrderViewPageMap.productsTablePaginationPrev).addClass('disabled');
    }
    $(OrderViewPageMap.productsTablePaginationNext).removeClass('disabled');
    if (numPage === totalPage) {
      $(OrderViewPageMap.productsTablePaginationNext).addClass('disabled');
    }
    this.togglePaginationControls();
  }

  updateNumPerPage(numPerPage: number): void {
    $(OrderViewPageMap.productsTablePagination).data('numPerPage', numPerPage);
    this.updatePaginationControls();
  }

  togglePaginationControls(): void {
    // Why 3 ? Next & Prev & Template
    const totalPage = $(OrderViewPageMap.productsTablePagination).find('li.page-item').length
      - 3;
    $(OrderViewPageMap.productsNavPagination).toggleClass(
      'd-none',
      totalPage <= 1,
    );
  }

  toggleProductAddNewInvoiceInfo(): void {
    $(OrderViewPageMap.productAddNewInvoiceInfo).toggleClass(
      'd-none',
      parseInt(
        <string>$(OrderViewPageMap.productAddInvoiceSelect).val(),
        10,
      ) !== 0,
    );
  }

  toggleColumn(target: string, forceDisplay = null): void {
    let isColumnDisplayed: boolean | null = false;

    if (forceDisplay === null) {
      $(target)
        .filter('td')
        // eslint-disable-next-line
        .each(function() {
          if ($(this).html() !== '') {
            isColumnDisplayed = true;
            return false;
          }
        });
    } else {
      isColumnDisplayed = forceDisplay;
    }
    $(target).toggleClass('d-none', !isColumnDisplayed);
  }

  updatePaginationControls(): void {
    const $tablePagination = $(OrderViewPageMap.productsTablePagination);
    const numPerPage = $tablePagination.data('numPerPage');
    const $rows = $(OrderViewPageMap.productsTable).find(
      'tr[id^="orderProduct_"]',
    );
    const numPages = Math.ceil($rows.length / numPerPage);

    // Update table data fields
    $tablePagination.data('numPages', numPages);

    // Clean all page links, reinsert the removed template
    const $linkPaginationTemplate = $(
      OrderViewPageMap.productsTablePaginationTemplate,
    );
    $(OrderViewPageMap.productsTablePagination)
      .find('li:has(> [data-page])')
      .remove();
    $(OrderViewPageMap.productsTablePaginationNext).before(
      $linkPaginationTemplate,
    );

    // Add appropriate pages
    for (let i = 1; i <= numPages; i += 1) {
      const $linkPagination = $linkPaginationTemplate.clone();
      $linkPagination.find('span').attr('data-page', i);
      $linkPagination.find('span').html(<string>(<unknown>i));
      $linkPaginationTemplate.before($linkPagination.removeClass('d-none'));
    }

    this.togglePaginationControls();
  }
}
