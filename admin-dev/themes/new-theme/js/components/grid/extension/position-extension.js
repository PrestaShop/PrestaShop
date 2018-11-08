/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import tableDnD from "tablednd/dist/jquery.tablednd.min";

const $ = window.$;

/**
 * Class PositionExtension extends Grid with reorderable positions
 */
export default class PositionExtension {
  constructor() {
    return {
      extend: (grid) => this.extend(grid),
    }
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
      function() {
        $(this).closest('tr').addClass('hover');
      },
      function() {
        $(this).closest('tr').removeClass('hover');
      }
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
    const $rowPositionContainer = $(row).find('.js-' + this.grid.getId() + '-position:first');
    const updateUrl = $rowPositionContainer.data('update-url');
    const method = $rowPositionContainer.data('update-method');
    const paginationOffset = parseInt($rowPositionContainer.data('pagination-offset'), 10);
    const positions = this._getRowsPositions(paginationOffset);
    const params = {positions};

    this._updatePosition(updateUrl, params, method);
  }

  /**
   * Returns the current table positions
   * @returns {Array}
   * @private
   */
  _getRowsPositions(paginationOffset) {
    const tableData = JSON.parse($.tableDnD.jsonize());
    const rowsData = tableData[this.grid.getId()+'_grid_table'];
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
      .find('.js-grid-table .js-' + this.grid.getId() + '-position')
      .each((index, positionWrapper) => {
        const $positionWrapper = $(positionWrapper);
        const rowId = $positionWrapper.data('id');
        const position = $positionWrapper.data('position');
        const id = `row_${rowId}_${position}`;
        $positionWrapper.closest('tr').attr('id', id);
        $positionWrapper.closest('td').addClass('js-drag-handle');
      });
  }

  /**
   * Process rows positions update
   *
   * @param {String} url
   * @param {Object} params
   * @param {String} method
   *
   * @private
   */
  _updatePosition(url, params, method) {
    const isGetOrPostMethod = ['GET', 'POST'].includes(method);

    const $form = $('<form>', {
      'action': url,
      'method': isGetOrPostMethod ? method : 'POST',
    }).appendTo('body');

    const positionsNb = params.positions.length;
    let position;
    for (let i = 0; i < positionsNb; ++i) {
      position = params.positions[i];
      $form.append(
        $('<input>', {
          'type': 'hidden',
          'name': 'positions['+i+'][rowId]',
          'value': position.rowId
        }),
        $('<input>', {
          'type': 'hidden',
          'name': 'positions['+i+'][oldPosition]',
          'value': position.oldPosition
        }),
        $('<input>', {
          'type': 'hidden',
          'name': 'positions['+i+'][newPosition]',
          'value': position.newPosition
        })
      );
    }

    // This _method param is used by Symfony to simulate DELETE and PUT methods
    if (!isGetOrPostMethod) {
      $form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': method,
      }));
    }

    $form.submit();
  }
}
