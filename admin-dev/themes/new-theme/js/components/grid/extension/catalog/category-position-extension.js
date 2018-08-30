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
 * Class CategoryPositionExtension extends reordering positions
 */
export default class CategoryPositionExtension {

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    this._addIdsToGridTableRows(grid);

    grid.getContainer().find('.js-grid-table').tableDnD({
      dragHandle: '.js-drag-handle',
      onDragStart: () => {
        this.originalPositions = decodeURIComponent($.tableDnD.serialize());
      },
      onDrop: (table, trElement) => {
        const positions = decodeURIComponent($.tableDnD.serialize());
        const way = (this.originalPositions.indexOf(trElement.id) < positions.indexOf(trElement.id)) ? 1 : 0;

        const $categoryPositionContainer = $(trElement).find('.js-category-position:first');

        const categoryId = $categoryPositionContainer.data('id-category');
        const categoryParentId = $categoryPositionContainer.data('id-parent-category');
        const positionUpdateUrl = $categoryPositionContainer.data('position-update-url');

        let params = positions.replace(new RegExp(grid.getId() + '_grid_table', 'g'), 'category');
        params +=  '&id_category_parent=' + categoryParentId + '&id_category_to_move=' + categoryId;
        params += '&way=' + way + '&ajax=1&action=updatePositions';

        if (positions.indexOf('_0&') !== -1) {
          params += '&found_first=1';
        }

        this._updateCategoryPosition(positionUpdateUrl, params);
      },
    });
  }

  /**
   * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
   *
   * @param {Grid} grid
   *
   * @private
   */
  _addIdsToGridTableRows(grid) {
    grid.getContainer().find('.js-grid-table').find('.js-category-position').each((index, positionWrapper) => {
      const $positionWrapper = $(positionWrapper);

      const categoryId = $positionWrapper.data('id-category');
      const categoryParentId = $positionWrapper.data('id-parent-category');
      const position = $positionWrapper.find('.js-position').text().trim();

      const id = 'tr_' + categoryParentId + '_' + categoryId + '_' + position;

      $positionWrapper.closest('tr').attr('id', id);
    });
  }

  _updateCategoryPosition(url, params) {
    $.post({
      url: url,
      headers: {
        'cache-control': 'no-cache'
      },
      data: params
    });
  }
}
