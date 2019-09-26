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
    this.expandIcon = '.js-expand';
    this.collapseIcon = '.js-collapse';
    this.previewOpen = 'preview-open';
    this.previewToggle = '.preview-toggle';
  }

  extend(grid) {
    grid.getContainer().find('tbody tr').on('mouseover mouseleave', (event) => {
      this._handleIconHovering(event);
    });
    grid.getContainer().find(this.previewToggle).on('click', (event) => {
      this._togglePreview(event);
    });
  }

  _handleIconHovering(event) {
    const $previewToggle = $(event.currentTarget).find('.preview-toggle');

    if (event.type === 'mouseover' && !$(event.currentTarget).hasClass('preview-open')) {
      this._showExpandIcon($previewToggle);
    } else {
      this._hideExpandIcon($previewToggle);
    }
  }

  _togglePreview(event) {
    const $previewToggle = $(event.currentTarget);
    const $columnRow = $previewToggle.closest('tr');

    if ($columnRow.hasClass(this.previewOpen)) {
      $columnRow.next('.preview-row').remove();
      $columnRow.removeClass('preview-open');
      this._showExpandIcon($columnRow);
      this._hideCollapseIcon($columnRow);

      return;
    }
    const dataUrl = $(event.currentTarget).data('preview-data-url');

    Promise.resolve(this.renderer(dataUrl).then((result) => {
      this._renderPreviewContent($columnRow, result.preview);
    }));
  }

  _renderPreviewContent($columnRow, content) {
    const rowColumnCount = $columnRow.find('td').length;

    const previewTemplate = `
        <tr class="preview-row">
          <td colspan="${rowColumnCount}">${content}</td>
        </tr>
      `;

    $columnRow.addClass(this.previewOpen);
    this._showCollapseIcon($columnRow);
    this._hideExpandIcon($columnRow);
    $columnRow.after(previewTemplate);
  }

  _showExpandIcon(parent) {
    parent.find(this.expandIcon).removeClass('d-none');
  }
  _hideExpandIcon(parent) {
    parent.find(this.expandIcon).addClass('d-none');
  }
  _showCollapseIcon(parent) {
    parent.find(this.collapseIcon).removeClass('d-none');
  }
  _hideCollapseIcon(parent) {
    parent.find(this.collapseIcon).addClass('d-none');
  }
}
