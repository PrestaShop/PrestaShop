/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';
import {isUndefined} from '@components/typeguard';

const {$} = window;

type OnClickCallbackFunction = (button: HTMLElement) => void;

/**
 * Class LinkRowActionExtension handles link row actions
 */
export default class LinkRowActionExtension {
  private readonly onClick?: OnClickCallbackFunction | undefined;

  constructor(onClick:OnClickCallbackFunction | undefined = undefined) {
    this.onClick = onClick;
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    this.initRowLinks(grid);
    this.initConfirmableActions(grid);
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  initConfirmableActions(grid: Grid): void {
    grid.getContainer().on('click', GridMap.rows.linkRowAction, (event) => {
      const confirmMessage = $(event.currentTarget).data('confirm-message');

      if (confirmMessage.length && !window.confirm(confirmMessage)) {
        event.preventDefault();
      }
    });
  }

  /**
   * Add a click event on rows that matches the first link action (if present)
   *
   * @param {Grid} grid
   */
  initRowLinks(grid: Grid): void {
    const onClickCallback = this.onClick;

    $('tr', grid.getContainer()).each(function initEachRow() {
      const $parentRow = $(this);

      $(GridMap.rows.linkRowActionClickableFirst, $parentRow).each(
        function propagateFirstLinkAction() {
          const $rowAction = $(this);
          const $parentCell = $rowAction.closest('td');

          const clickableCells = $(GridMap.rows.clickableTd, $parentRow).not(
            $parentCell,
          );
          let isDragging = false;
          clickableCells.addClass('cursor-pointer').on('mousedown', () => {
            $(window).on('mousemove', () => {
              isDragging = true;
              $(window).off('mousemove');
            });
          });

          clickableCells.on('mouseup', () => {
            const wasDragging = isDragging;
            isDragging = false;
            $(window).off('mousemove');

            if (!wasDragging) {
              const confirmMessage = $rowAction.data('confirm-message');

              if (
                !confirmMessage.length
                || (window.confirm(confirmMessage) && $rowAction.attr('href'))
              ) {
                if (!isUndefined(onClickCallback) && !isUndefined($rowAction.get(0))) {
                  onClickCallback($rowAction.get(0) as HTMLElement);
                } else {
                  document.location.href = <string>$rowAction.attr('href');
                }
              }
            }
          });
        },
      );
    });
  }
}
