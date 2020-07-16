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

const $ = window.$;

/**
 * Extends grid with preview functionality.
 */
export default class PreviewExtension {
  constructor(previewCustomization) {
    this.lock = [];
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

    this.$gridContainer.find('tbody tr').on('mouseover mouseleave', event => this._handleIconHovering(event));
    this.$gridContainer.find(this.previewToggleSelector).on('click', event => this._togglePreview(event));
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

    this._closeOpenedPreviews();

    const dataUrl = $(event.currentTarget).data('preview-data-url');

    if (this._isLocked(dataUrl)) {
      return;
    }

    // Prevents loading preview multiple times.
    // Uses "dataUrl" as lock key.
    this._lock(dataUrl);

    $.ajax({
      url: dataUrl,
      method: 'GET',
      dataType: 'json',
      complete: () => {
        this._unlock(dataUrl);
      },
    }).then((response) => {
      this._renderPreviewContent($columnRow, response.preview);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
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
  _renderPreviewContent($columnRow, content) {
    const rowColumnCount = $columnRow.find('td').length;

    const $previewTemplate = $(`
        <tr class="preview-row">
          <td colspan="${rowColumnCount}">${content}</td>
        </tr>
      `);

    $columnRow.addClass(this.previewOpenClass);
    this._showCollapseIcon($columnRow);
    this._hideExpandIcon($columnRow);

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

  _isLocked(key) {
    return this.lock.indexOf(key) !== -1;
  }

  _lock(key) {
    if (this._isLocked(key)) {
      return;
    }

    this.lock.push(key);
  }

  _unlock(key) {
    const index = this.lock.indexOf(key);

    if (index === -1) {
      return;
    }

    this.lock.splice(index, 1);
  }

  /**
   * Close all previews that are open.
   *
   * @private
   */
  _closeOpenedPreviews() {
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
      this._hideCollapseIcon($row);
    });
  }
}
