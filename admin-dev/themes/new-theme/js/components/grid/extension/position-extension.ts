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

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';
import 'tablednd/dist/jquery.tablednd.min';

const {$} = window;

interface RowDatas {
  rowMarker: string;
  offset: number;
}

interface DNDPositions {
  rowId: string;
  oldPosition: number;
  newPosition: number;
}

/**
 * Class PositionExtension extends Grid with reorderable positions
 */
export default class PositionExtension {
  grid: Grid;

  constructor(grid: Grid) {
    this.grid = grid;
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    this.grid = grid;
    this.addIdsToGridTableRows();
    grid
      .getContainer()
      .find(GridMap.gridTable)
      .tableDnD({
        onDragClass: GridMap.onDragClass,
        dragHandle: GridMap.dragHandler,
        onDrop: (table: HTMLElement, row: HTMLElement) => this.handlePositionChange(row),
      });
    grid
      .getContainer()
      .find('.js-drag-handle')
      .hover(
        function hover() {
          $(this)
            .closest('tr')
            .addClass('hover');
        },
        function stopHover() {
          $(this)
            .closest('tr')
            .removeClass('hover');
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
  private handlePositionChange(row: HTMLElement): void {
    const $rowPositionContainer = $(row).find(
      GridMap.gridPositionFirst(this.grid.getId()),
    );
    const updateUrl = $rowPositionContainer.data('update-url');
    const method = $rowPositionContainer.data('update-method');
    const positions = this.getRowsPositions();
    const params = {positions};

    this.updatePosition(updateUrl, params, method);
  }

  /**
   * Returns the current table positions
   * @returns {Array}
   * @private
   */
  private getRowsPositions(): Array<DNDPositions> {
    const tableData = JSON.parse($.tableDnD.jsonize());
    const rowsData = tableData[`${this.grid.getId()}_grid_table`];
    const completeRowsData = [];

    let trData;

    // retrieve dragAndDropOffset offset to have all needed data
    // for positions mapping evolution over time
    for (let i = 0; i < rowsData.length; i += 1) {
      trData = this.grid.getContainer().find(`#${rowsData[i]}`);

      completeRowsData.push({
        rowMarker: rowsData[i],
        offset: trData.data('dragAndDropOffset'),
      });
    }

    return this.computeMappingBetweenOldAndNewPositions(completeRowsData);
  }

  /**
   * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
   *
   * @private
   */
  private addIdsToGridTableRows(): void {
    let counter = 0;

    this.grid
      .getContainer()
      .find(GridMap.gridTablePosition(this.grid.getId()))
      .each((index, positionWrapper) => {
        const $positionWrapper = $(positionWrapper);
        const rowId = $positionWrapper.data('id');
        const position = $positionWrapper.data('position');
        const id = `row_${rowId}_${position}`;
        $positionWrapper.closest('tr').attr('id', id);
        $positionWrapper.closest('td').addClass(GridMap.dragHandler);
        $positionWrapper.closest('tr').data('dragAndDropOffset', counter);

        counter += 1;
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
  private updatePosition(
    url: string,
    params: Record<string, Array<DNDPositions>>,
    method: string,
  ): void {
    const isGetOrPostMethod = ['GET', 'POST'].includes(method);

    const $form = $('<form>', {
      action: url,
      method: isGetOrPostMethod ? method : 'POST',
    }).appendTo('body');

    const positionsNb = params.positions.length;
    let position;

    for (let i = 0; i < positionsNb; i += 1) {
      position = params.positions[i];
      $form.append(
        $('<input>', {
          type: 'hidden',
          name: `positions[${i}][rowId]`,
          value: position.rowId,
        }),
        $('<input>', {
          type: 'hidden',
          name: `positions[${i}][oldPosition]`,
          value: position.oldPosition,
        }),
        $('<input>', {
          type: 'hidden',
          name: `positions[${i}][newPosition]`,
          value: position.newPosition,
        }),
      );
    }

    // This _method param is used by Symfony to simulate DELETE and PUT methods
    if (!isGetOrPostMethod) {
      $form.append(
        $('<input>', {
          type: 'hidden',
          name: '_method',
          value: method,
        }),
      );
    }

    $form.submit();
  }

  /**
   * Rows have been reordered. This function
   * finds, for each row ID: the old position, the new position
   *
   * @returns {Array}
   * @private
   */
  private computeMappingBetweenOldAndNewPositions(
    rowsData: Array<RowDatas>,
  ): Array<DNDPositions> {
    const regex = /^row_(\d+)_(\d+)$/;
    const mapping = Array(rowsData.length).map(Object);

    for (let i = 0; i < rowsData.length; i += 1) {
      const regexResult = <RegExpPositions>regex.exec(rowsData[i].rowMarker);

      if (regexResult?.rowId && regexResult?.oldPosition) {
        mapping[i].rowId = regexResult.rowId;
        mapping[i].oldPosition = parseInt(regexResult.oldPosition, 10);
      }
      // This row will have as a new position the old position of the current one
      mapping[rowsData[i].offset].newPosition = mapping[i].oldPosition;
    }

    return mapping;
  }
}
