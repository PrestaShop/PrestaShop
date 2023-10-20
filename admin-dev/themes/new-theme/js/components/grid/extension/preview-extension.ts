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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import {Grid} from '@js/types/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Extends grid with preview functionality.
 */
export default class PreviewExtension {
  locks: Array<unknown>;

  expandSelector: string;

  previewOpenClass: string;

  collapseSelector: string;

  previewToggleSelector: string;

  previewCustomization: (previewTemplate: JQuery) => void;

  $gridContainer: JQuery;

  constructor(previewCustomization: (previewTemplate: JQuery) => void, grid: Grid) {
    this.locks = [];
    this.expandSelector = GridMap.expand;
    this.collapseSelector = GridMap.collapse;
    this.previewOpenClass = 'preview-open';
    this.previewToggleSelector = GridMap.previewToggle;
    this.previewCustomization = previewCustomization;
    this.$gridContainer = $(grid.getContainer);
  }

  /**
   * Extends provided grid with preview functionality
   *
   * @param grid
   */
  extend(grid: Grid): void {
    this.$gridContainer = $(grid.getContainer);

    this.$gridContainer.find('tbody tr').on('mouseover mouseleave', (event: JQueryEventObject) => this.handleIconHovering(event));
    this.$gridContainer.find(this.previewToggleSelector).on('click', (event: JQueryEventObject) => this.togglePreview(event));
  }

  /**
   * Shows/hides preview toggling icons
   *
   * @param event
   * @private
   */
  private handleIconHovering(event: JQueryEventObject) {
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
  togglePreview(event: JQueryEventObject): void {
    const $previewToggle = $(event.currentTarget);
    const $columnRow = $previewToggle.closest('tr');

    if ($columnRow.hasClass(this.previewOpenClass)) {
      $columnRow.next(GridMap.previewRow).remove();
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
    })
      .then((response) => {
        this.renderPreviewContent($columnRow, response.preview);
      })
      .catch((e: AjaxError) => {
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
  private renderPreviewContent($columnRow: JQuery<Element>, content: string) {
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
  private showExpandIcon(parent: JQuery<Element>): void {
    parent.find(this.expandSelector).removeClass('d-none');
  }

  /**
   * Hides preview expanding icon
   *
   * @param parent
   * @private
   */
  private hideExpandIcon(parent: JQuery<Element>): void {
    parent.find(this.expandSelector).addClass('d-none');
  }

  /**
   * Shows preview collapsing icon
   *
   * @param parent
   * @private
   */
  private showCollapseIcon(parent: JQuery<Element>): void {
    parent.find(this.collapseSelector).removeClass('d-none');
  }

  /**
   * Hides preview collapsing icon
   *
   * @param parent
   * @private
   */
  private hideCollapseIcon(parent: JQuery<Element>): void {
    parent.find(this.collapseSelector).addClass('d-none');
  }

  isLocked(key: number): boolean {
    return this.locks.indexOf(key) !== -1;
  }

  lock(key: number): void {
    if (this.isLocked(key)) {
      return;
    }

    this.locks.push(key);
  }

  unlock(key: number): void {
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
  private closeOpenedPreviews(): void {
    const $rows = this.$gridContainer.find(GridMap.gridTbody).find(GridMap.trNotPreviewRow);

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
