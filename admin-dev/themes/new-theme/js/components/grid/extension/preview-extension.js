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
 *  <span class="preview-toggle" data-preview-identifier="3">
 *    Preview
 *  </span>
 * </td>
 *
 * Note that "data-preview-identifier" attribute must be provided
 * with row identifier value (e.g. customer id if it's customers grid).
 *
 * You can use PreviewColumn when defining grid in PHP or create your custom column.
 *
 * In JS:
 *
 * // Here "identifier" contains "data-preview-identifier" value.
 *
 * function myPreviewRenderer(identifier) {
 *   // do ajax or anything else to load preview data and return html
 *
 *   return `<p>Preview data for ${identifier}</p>`;
 * }
 *
 * const grid = new Grid('my_grid');
 * grid.extend(new PreviewExtension(myPreviewRenderer));
 */
export default class PreviewExtension {
  constructor(previewRenderer) {
    this.renderer = previewRenderer;
  }

  extend(grid) {
    grid.getContainer().find('.preview-toggle').on('click', (event) => {
      const $previewToggle = $(event.currentTarget);
      const $columnRow = $previewToggle.closest('tr');

      if ($columnRow.hasClass('preview-open')) {
        $columnRow.siblings('tr.preview-row:first').remove();
        $columnRow.removeClass('preview-open');

        return;
      }

      const rowColumnCount = $columnRow.find('td').length;
      const identifier = $previewToggle.data('preview-identifier');

      const previewTemplate = `
        <tr class="preview-row">
          <td colspan="${rowColumnCount}">${this.renderer(identifier)}</td>
        </tr>
      `;

      $columnRow.addClass('preview-open');
      $columnRow.after(previewTemplate);
    });
  }
}
