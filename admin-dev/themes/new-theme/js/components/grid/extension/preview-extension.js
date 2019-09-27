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

const $ = window.$;

/**
 * Extends grid with preview functionality with additional row data.
 *
 * Usage:
 *
 * Grid column must contain .preview-toggle element that PreviewExtension can hook on, example:
 *
 * <td>
 *  <span class="preview-toggle">
 *    Preview
 *  </span>
 * </td>
 *
 * You can use PreviewColumn when defining grid in PHP or create your custom column.
 *
 * In JS:
 *
 * function myPreviewRenderer(url) {
 *   // do ajax load preview data and return json with preview content html
 *
 *   return {preview: `<p>Preview data</p>`};
 * }
 *
 * const grid = new Grid('my_grid');
 * grid.extend(new PreviewExtension(myPreviewRenderer));
 */
export default class PreviewExtension {
  constructor(previewRenderer) {
    this.renderer = previewRenderer;
    this.expandSelector = '.js-expand';
    this.collapseSelector = '.js-collapse';
    this.previewOpenClass = 'preview-open';
    this.previewToggleSelector = '.preview-toggle';
  }

  /**
   * Extends provided grid with preview functionality
   *
   * @param grid
   */
  extend(grid) {
    grid.getContainer().find('tbody tr').on('mouseover mouseleave', (event) => {
      this._handleIconHovering(event);
    });
    grid.getContainer().find(this.previewToggleSelector).on('click', (event) => {
      this._togglePreview(event);
    });
  }

  /**
   * Shows/hides preview toggling icons
   *
   * @param event
   * @private
   */
  _handleIconHovering(event) {
    const $previewToggle = $(event.currentTarget).find(this.previewToggleSelector);

    if (event.type === 'mouseover' && !$(event.currentTarget).hasClass(this.previewOpenClass)) {
      this._showExpandIcon($previewToggle);
    } else {
      this._hideExpandIcon($previewToggle);
    }
  }

  /**
   * Shows/hides preview
   *
   * @param event
   * @private
   */
  _togglePreview(event) {
    const $previewToggle = $(event.currentTarget);
    const $columnRow = $previewToggle.closest('tr');

    if ($columnRow.hasClass(this.previewOpenClass)) {
      $columnRow.next('.preview-row').remove();
      $columnRow.removeClass(this.previewOpenClass);
      this._showExpandIcon($columnRow);
      this._hideCollapseIcon($columnRow);

      return;
    }
    const dataUrl = $(event.currentTarget).data('preview-data-url');

    Promise.resolve(this.renderer(dataUrl).then((result) => {
      this._renderPreviewContent($columnRow, result.preview);
    }));
  }

  /**
   * Renders preview content
   *
   * @param $columnRow
   * @param content
   *
   * @private
   */
  _renderPreviewContent($columnRow, content) {
    const rowColumnCount = $columnRow.find('td').length;

    const previewTemplate = `
        <tr class="preview-row">
          <td colspan="${rowColumnCount}">${content}</td>
        </tr>
      `;

    $columnRow.addClass(this.previewOpenClass);
    this._showCollapseIcon($columnRow);
    this._hideExpandIcon($columnRow);
    $columnRow.after(previewTemplate);
  }

  /**
   * Shows preview expanding icon
   *
   * @param parent
   * @private
   */
  _showExpandIcon(parent) {
    parent.find(this.expandSelector).removeClass('d-none');
  }

  /**
   * Hides preview expanding icon
   *
   * @param parent
   * @private
   */
  _hideExpandIcon(parent) {
    parent.find(this.expandSelector).addClass('d-none');
  }

  /**
   * Shows preview collapsing icon
   *
   * @param parent
   * @private
   */
  _showCollapseIcon(parent) {
    parent.find(this.collapseSelector).removeClass('d-none');
  }

  /**
   * Hides preview collapsing icon
   *
   * @param parent
   * @private
   */
  _hideCollapseIcon(parent) {
    parent.find(this.collapseSelector).addClass('d-none');
  }
}
