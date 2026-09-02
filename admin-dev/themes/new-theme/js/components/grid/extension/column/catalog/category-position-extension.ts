/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import 'tablednd/dist/jquery.tablednd.min';
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Class CategoryPositionExtension extends Grid with reorderable category positions
 */
export default class CategoryPositionExtension {
  grid: Grid;

  originalPositions: string;

  constructor(grid: Grid) {
    this.grid = grid;
    this.originalPositions = '';
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
        dragHandle: GridMap.dragHandler,
        onDragClass: 'dragging-row',
        onDragStart: () => {
          this.originalPositions = decodeURIComponent($.tableDnD.serialize());
        },
        onDrop: (table: HTMLElement, row: HTMLElement) => this.handleCategoryPositionChange(row),
      });
  }

  /**
   * When position is changed handle update
   *
   * @param {HTMLElement} row
   *
   * @private
   */
  handleCategoryPositionChange(row: HTMLElement): void {
    const positions = decodeURIComponent($.tableDnD.serialize());
    const way = this.originalPositions.indexOf(row.id) < positions.indexOf(row.id)
      ? 1
      : 0;

    const $categoryPositionContainer = $(row).find(
      GridMap.position(this.grid.getId()),
    );

    const categoryId = $categoryPositionContainer.data('id');
    const categoryParentId = $categoryPositionContainer.data('id-parent');
    const positionUpdateUrl = $categoryPositionContainer.data(
      'position-update-url',
    );

    let params = positions.replace(
      new RegExp(GridMap.specificGridTable(this.grid.getId()), 'g'),
      'positions',
    );

    const queryParams = {
      id_category_parent: categoryParentId,
      id_category_to_move: categoryId,
      way,
      found_first: 0,
    };

    if (positions.indexOf('_0&') !== -1) {
      queryParams.found_first = 1;
    }

    params += `&${$.param(queryParams)}`;

    this.updateCategoryPosition(positionUpdateUrl, params);
  }

  /**
   * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
   *
   * @private
   */
  addIdsToGridTableRows(): void {
    this.grid
      .getContainer()
      .find(GridMap.gridTable)
      .find(GridMap.gridPosition(this.grid.getId()))
      .each((index, positionWrapper) => {
        const $positionWrapper = $(positionWrapper);

        const categoryId = $positionWrapper.data('id');
        const categoryParentId = $positionWrapper.data('id-parent');
        const position = $positionWrapper.data('position');

        const id = `tr_${categoryParentId}_${categoryId}_${position}`;

        $positionWrapper.closest('tr').attr('id', id);
      });
  }

  /**
   * Update categories listing with new positions
   *
   * @private
   */
  updateCategoryIdsAndPositions(): void {
    this.grid
      .getContainer()
      .find(GridMap.gridTable)
      .find(GridMap.gridPosition(this.grid.getId()))
      .each((index, positionWrapper) => {
        const $positionWrapper = $(positionWrapper);
        const $row = $positionWrapper.closest('tr');

        const offset = $positionWrapper.data('pagination-offset');
        const newPosition = offset > 0 ? index + offset : index;

        const oldId = $row.attr('id');

        if (oldId) {
          $row.attr('id', oldId.replace(/_[0-9]$/g, `_${newPosition}`));
        }

        $positionWrapper.find(GridMap.selectPosition).text(newPosition + 1);
        $positionWrapper.data('position', newPosition);
      });
  }

  /**
   * Process categories positions update
   *
   * @param {String} url
   * @param {String} params
   *
   * @private
   */
  updateCategoryPosition(url: string, params: string): void {
    $.post({
      url,
      headers: {
        'cache-control': 'no-cache',
      },
      data: params,
      dataType: 'json',
    }).then((response) => {
      if (response.success) {
        window.showSuccessMessage(response.message);
      } else {
        window.showErrorMessage(response.message);
      }

      this.updateCategoryIdsAndPositions();
    });
  }
}
