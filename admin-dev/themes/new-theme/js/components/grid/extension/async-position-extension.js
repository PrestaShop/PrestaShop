/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import tableDnD from 'tablednd/dist/jquery.tablednd.min';

const $ = window.$;

/**
 * A component which adds asynchronous position drag & drop functionality -
 * no page refresh happens after each drag & drop action.
 *
 * Usage:
 *
 * const myGrid = new Grid('my_grid_id');
 * myGrid.addExtension(new AsyncPositionExtension());
 */
export default class AsyncPositionExtension {
  constructor() {
    return {
      extend: grid => this.extend(grid),
    };
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    this.grid = grid;
    this._addIdsToGridTableRows();
    grid.getContainer().find('.js-grid-table').tableDnD({
      onDragClass: 'position-row-while-drag',
      dragHandle: '.js-drag-handle',
      onDrop: (table, row) => this._handlePositionChange(row),
    });
    grid.getContainer().find('.js-drag-handle').hover(
      function () {
        $(this).closest('tr').addClass('hover');
      },
      function () {
        $(this).closest('tr').removeClass('hover');
      },
    );
  }

  /**
   * When position is changed handle update
   *
   * @param {HTMLElement} row
   *
   * @private
   */
  _handlePositionChange(row) {
    const $rowPositionContainer = $(row).find(`.js-${  this.grid.getId()  }-position:first`);
    const updateUrl = $rowPositionContainer.data('update-url');
    const paginationOffset = parseInt($rowPositionContainer.data('pagination-offset'), 10);
    const positions = this._getRowsPositions(paginationOffset);
    const params = {positions};

    this._updatePositionsForOriginalColumn();
    this._updatePosition(updateUrl, params);
  }

  /**
   * Returns the current table positions
   * @returns {Array}
   * @private
   */
  _getRowsPositions(paginationOffset) {
    const tableData = JSON.parse($.tableDnD.jsonize());
    const rowsData = tableData[`${this.grid.getId()}_grid_table`];
    const regex = /^row_(\d+)_(\d+)$/;

    const rowsNb = rowsData.length;
    const positions = [];
    let rowData, i;

    for (i = 0; i < rowsNb; ++i) {
      rowData = regex.exec(rowsData[i]);
      positions.push({
        rowId: rowData[1],
        newPosition: paginationOffset + i,
        oldPosition: parseInt(rowData[2], 10),
      });
    }

    return positions;
  }

  /**
   * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
   *
   * @private
   */
  _addIdsToGridTableRows() {
    this.grid.getContainer()
      .find(`.js-grid-table .js-${this.grid.getId()}-position`)
      .each((index, positionWrapper) => {
        const $positionWrapper = $(positionWrapper);
        const rowId = $positionWrapper.data('id');
        const position = $positionWrapper.data('position');
        const id = `row_${rowId}_${position}`;

        const $td = $positionWrapper.closest('td');
        const $tr = $positionWrapper.closest('tr');

        $tr.attr('id', id);
        $td.addClass('js-drag-handle');
      });
  }

  /**
   * Updates position for the column which actually is visible for the user.
   * @private
   */
  _updatePositionsForOriginalColumn() {
    this.grid.getContainer()
      .find(`.js-grid-table .js-${this.grid.getId()}-position`)
      .each((index, positionWrapper) => {
        const $positionWrapper = $(positionWrapper);
        const $tr = $positionWrapper.closest('tr');
        const $td = $positionWrapper.closest('td');

        const positionColumnId = this._getOriginalColumnId($td);
        const displayPosition = parseInt(index, 10) + 1;
        $tr.find(`td[class="${positionColumnId}"] div`).text(displayPosition.toString());
      })
  }

  /**
   * Gets original column id.
   *
   * @param $positionWrapper
   *
   * @return {string}
   * @private
   */
  _getOriginalColumnId($positionWrapper) {
    const classes = $positionWrapper.attr('class').split(/\s+/);
    const classEndsWith = '_handle-type';

    const foundClass = classes.find((item) => item.endsWith(classEndsWith));

    if (typeof foundClass === 'undefined') {
      return '';
    }

    return foundClass.replace(classEndsWith, '-type');
  }

  /**
   * Process rows positions update
   *
   * @param {String} url
   * @param {Object} params
   *
   * @private
   */
  _updatePosition(url, params) {
    $.post({
      url,
      data: params,
      dataType: 'json',
    }).then((response) => {
      if (response.success) {
        showSuccessMessage(response.message);
      } else {
        showErrorMessage(response.message);
      }
    }).catch((error) => {
      const response = error.responseJSON;

      showErrorMessage(response.message);
    }).always(() => {
      this._addIdsToGridTableRows(true);
    });
  }
}
