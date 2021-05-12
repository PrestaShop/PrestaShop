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

const {$} = window;

const $importDataTable = $('.js-import-data-table');

/**
 * Pagination directions - forward and backward.
 */
const FORWARD = 'forward';
const BACKWARD = 'backward';

export default class ImportDataTable {
  constructor() {
    this.numberOfColumnsPerPage = this.getNumberOfVisibleColumns();
    this.totalNumberOfColumns = this.getTotalNumberOfColumns();

    $('.js-import-next-page').on('click', () => this.importNextPageHandler());
    $('.js-import-previous-page').on('click', () => this.importPreviousPageHandler());
  }

  /**
   * Handle the next page action in import data table.
   */
  importNextPageHandler() {
    this.importPaginationHandler(FORWARD);
  }

  /**
   * Handle the previous page action in import data table.
   */
  importPreviousPageHandler() {
    this.importPaginationHandler(BACKWARD);
  }

  /**
   * Handle the forward and back buttons actions in the import table.
   *
   * @param {string} direction
   * @private
   */
  importPaginationHandler(direction) {
    const $currentPageElements = $importDataTable.find('th:visible,td:visible');
    const $oppositePaginationButton = direction === FORWARD ? $('.js-import-next-page') : $('.js-import-previous-page');
    let lastVisibleColumnFound = false;
    let numberOfVisibleColumns = 0;
    let $tableColumns = $importDataTable.find('th');

    if (direction === BACKWARD) {
      // If going backward - reverse the table columns array and use the same logic as forward
      $tableColumns = $($tableColumns.toArray().reverse());
    }

    /* eslint-disable-next-line */
    for (const index in $tableColumns) {
      if (Number.isNaN(index)) {
        // Reached the last column - hide the opposite pagination button
        this.hide($oppositePaginationButton);
        break;
      }

      // Searching for last visible column
      if ($($tableColumns[index]).is(':visible')) {
        lastVisibleColumnFound = true;
        /* eslint-disable-next-line no-continue */
        continue;
      }

      // If last visible column was found - show the column after it
      if (lastVisibleColumnFound) {
        // If going backward, the column index must be counted from the last element
        const showColumnIndex = direction === BACKWARD ? this.totalNumberOfColumns - 1 - index : index;
        this.showTableColumnByIndex(showColumnIndex);
        numberOfVisibleColumns += 1;

        // If number of visible columns per page is already reached - break the loop
        if (numberOfVisibleColumns >= this.numberOfColumnsPerPage) {
          this.hide($oppositePaginationButton);
          break;
        }
      }
    }

    // Hide all the columns from previous page
    this.hide($currentPageElements);

    // If the first column in the table is not visible - show the "previous" pagination arrow
    if (!$importDataTable.find('th:first').is(':visible')) {
      this.show($('.js-import-previous-page'));
    }

    // If the last column in the table is not visible - show the "next" pagination arrow
    if (!$importDataTable.find('th:last').is(':visible')) {
      this.show($('.js-import-next-page'));
    }
  }

  /**
   * Gets the number of currently visible columns in the import data table.
   *
   * @returns {number}
   * @private
   */
  getNumberOfVisibleColumns() {
    return $importDataTable.find('th:visible').length;
  }

  /**
   * Gets the total number of columns in the import data table.
   *
   * @returns {number}
   * @private
   */
  getTotalNumberOfColumns() {
    return $importDataTable.find('th').length;
  }

  /**
   * Hide the elements.
   *
   * @param $elements
   * @private
   */
  hide($elements) {
    $elements.addClass('d-none');
  }

  /**
   * Show the elements.
   *
   * @param $elements
   * @private
   */
  show($elements) {
    $elements.removeClass('d-none');
  }

  /**
   * Shows a column from import data table by given index
   *
   * @param columnIndex
   * @private
   */
  showTableColumnByIndex(columnIndex) {
    // Increasing the index because nth-child calculates from 1 and index starts from 0
    const colIndex = columnIndex + 1;

    this.show($importDataTable.find(`th:nth-child(${colIndex})`));
    this.show($importDataTable.find('tbody > tr').find(`td:nth-child(${colIndex})`));
  }
}
