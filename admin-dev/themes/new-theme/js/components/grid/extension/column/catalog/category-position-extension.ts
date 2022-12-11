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

import 'tablednd/dist/jquery.tablednd.min';
import {Grid} from '@js/types/grid';
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
