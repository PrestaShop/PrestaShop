/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';
import {isUndefined} from '@components/typeguard';
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
      .on(
        'mouseenter',
        function () {
          $(this)
            .closest('tr')
            .addClass('hover');
        },
      ).on(
        'mouseleave',
        function () {
          $(this)
            .closest('tr')
            .removeClass('hover');
        },
      );

    this.setReorderButtonLabel();
    this.getReorderButton().on('click', (event) => this.oncClickReorderButton(event));
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
        $positionWrapper.closest('td').addClass(GridMap.dragHandlerClass);
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
    const regex = /^row_(?<rowId>\d+)_(?<oldPosition>\d+)$/;
    const mapping: Array<DNDPositions> = [];

    // First loop is to create the mapping objects with old positions
    for (let i = 0; i < rowsData.length; i += 1) {
      const regexResult = regex.exec(rowsData[i].rowMarker);

      if (regexResult
        && !isUndefined(regexResult.groups)
        && !isUndefined(regexResult.groups.rowId)
        && !isUndefined(regexResult.groups.oldPosition)) {
        const oldPosition: number = parseInt(regexResult?.groups?.oldPosition, 10);
        mapping[i] = {
          rowId: regexResult.groups.rowId,
          oldPosition,
          newPosition: oldPosition,
        };
      }

      // Second loop, now that all positions are defined for all rows we can switch the position when needed
      for (let j = 0; j < rowsData.length; j += 1) {
        if (!isUndefined(rowsData[j])
          && !isUndefined(rowsData[j].offset)
          && !isUndefined(mapping[rowsData[j].offset])
          && !isUndefined(mapping[j])) {
          // This row will have as a new position the old position of the current one
          mapping[rowsData[j].offset].newPosition = mapping[j].oldPosition;
        }
      }
    }

    return mapping;
  }

  /**
   * Check if position reorder is active
   *
   * @private
   */
  private isPositionsReorderActive(): boolean {
    return this.grid.getContainer()
      .find('.ps-sortable-column[data-sort-col-name="position"]')
      .first()
      .data('sort-is-current');
  }

  /**
   * Get reorder button
   *
   * @private
   */
  private getReorderButton(): JQuery<HTMLElement> {
    return this.grid
      .getContainer()
      .find('.js-btn-reorder-positions')
      .first();
  }

  /**
   * Set reorder button label in function of sortable column state.
   *
   * @private
   */
  private setReorderButtonLabel(): void {
    const rearrangeButton = this.getReorderButton();

    if (this.isPositionsReorderActive()) {
      rearrangeButton.hide();
    } else {
      rearrangeButton.data('label-reorder');
    }
  }

  /**
   * Onclick reorder button
   *
   * @param event
   * @private
   */
  private oncClickReorderButton(event: JQuery.Event): void {
    event.preventDefault();
    // If positions are actually being reordered...
    if (this.isPositionsReorderActive()) {
      // we need to reset filters and order by of the grid
      this.grid.getContainer()
        .find('.ps-sortable-column')
        .first()
        .click();
    } else {
      // Else, we need to set the position column as the current sort ordering
      this.grid.getContainer()
        .find('.ps-sortable-column[data-sort-col-name="position"]')
        .first()
        .click();
    }
  }
}
