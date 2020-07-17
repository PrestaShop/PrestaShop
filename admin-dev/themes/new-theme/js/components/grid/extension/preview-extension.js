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

const {$} = window;

/**
 * Extends grid with preview functionality.
 */
export default class PreviewExtension {
  constructor(previewCustomization) {
    this.locks = [];
    this.expandSelector = '.js-expand';
    this.collapseSelector = '.js-collapse';
    this.previewOpenClass = 'preview-open';
    this.previewToggleSelector = '.preview-toggle';
    this.previewCustomization = previewCustomization;
  }

  /**
   * Extends provided grid with preview functionality
   *
   * @param grid
   */
  extend(grid) {
    this.$gridContainer = $(grid.getContainer);

    this.$gridContainer.find('tbody tr').on('mouseover mouseleave', (event) => this.handleIconHovering(event));
    this.$gridContainer.find(this.previewToggleSelector).on('click', (event) => this.togglePreview(event));
  }

  /**
   * Shows/hides preview toggling icons
   *
   * @param event
   * @private
   */
  handleIconHovering(event) {
    const $previewToggle = $(event.currentTarget).find(this.previewToggleSelector);

    if (event.type === 'mouseover' && !$(event.currentTarget).hasClass(this.previewOpenClass)) {
      this.showExpandIcon($previewToggle);
    } else {
      this.hideExpandIcon($previewToggle);
    }
  }

  /**
   * Shows/hides preview
   *
   * @param event
   * @private
   */
  togglePreview(event) {
    const $previewToggle = $(event.currentTarget);
    const $columnRow = $previewToggle.closest('tr');

    if ($columnRow.hasClass(this.previewOpenClass)) {
      $columnRow.next('.preview-row').remove();
      $columnRow.removeClass(this.previewOpenClass);
      this.showExpandIcon($columnRow);
      this.hideCollapseIcon($columnRow);

      return;
    }

    this.closeOpenedPreviews();

    const dataUrl = $(event.currentTarget).data('preview-data-url');

    if (this.isLocked(dataUrl)) {
      return;
    }

    // Prevents loading preview multiple times.
    // Uses "dataUrl" as lock key.
    this.lock(dataUrl);

    $.ajax({
      url: dataUrl,
      method: 'GET',
      dataType: 'json',
      complete: () => {
        this.unlock(dataUrl);
      },
    }).then((response) => {
      this.renderPreviewContent($columnRow, response.preview);
    }).catch((e) => {
      window.showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * Renders preview content
   *
   * @param $columnRow
   * @param content
   *
   * @private
   */
  renderPreviewContent($columnRow, content) {
    const rowColumnCount = $columnRow.find('td').length;

    const $previewTemplate = $(`
        <tr class="preview-row">
          <td colspan="${rowColumnCount}">${content}</td>
        </tr>
      `);

    $columnRow.addClass(this.previewOpenClass);
    this.showCollapseIcon($columnRow);
    this.hideExpandIcon($columnRow);

    if (typeof this.previewCustomization === 'function') {
      this.previewCustomization($previewTemplate);
    }

    $columnRow.after($previewTemplate);
  }

  /**
   * Shows preview expanding icon
   *
   * @param parent
   * @private
   */
  showExpandIcon(parent) {
    parent.find(this.expandSelector).removeClass('d-none');
  }

  /**
   * Hides preview expanding icon
   *
   * @param parent
   * @private
   */
  hideExpandIcon(parent) {
    parent.find(this.expandSelector).addClass('d-none');
  }

  /**
   * Shows preview collapsing icon
   *
   * @param parent
   * @private
   */
  showCollapseIcon(parent) {
    parent.find(this.collapseSelector).removeClass('d-none');
  }

  /**
   * Hides preview collapsing icon
   *
   * @param parent
   * @private
   */
  hideCollapseIcon(parent) {
    parent.find(this.collapseSelector).addClass('d-none');
  }

  isLocked(key) {
    return this.locks.indexOf(key) !== -1;
  }

  lock(key) {
    if (this.isLocked(key)) {
      return;
    }

    this.locks.push(key);
  }

  unlock(key) {
    const index = this.locks.indexOf(key);

    if (index === -1) {
      return;
    }

    this.locks.splice(index, 1);
  }

  /**
   * Close all previews that are open.
   *
   * @private
   */
  closeOpenedPreviews() {
    const $rows = this.$gridContainer.find('.grid-table tbody').find('tr:not(.preview-row)');

    $.each($rows, (i, row) => {
      const $row = $(row);

      if (!$row.hasClass(this.previewOpenClass)) {
        return;
      }

      const $previewRow = $row.next();

      if (!$previewRow.hasClass('preview-row')) {
        return;
      }

      $previewRow.remove();
      $row.removeClass(this.previewOpenClass);
      this.hideCollapseIcon($row);
    });
  }
}
