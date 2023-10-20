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
import {isUndefined} from '@PSTypes/typeguard';

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
          clickableCells.addClass('cursor-pointer').mousedown(() => {
            $(window).mousemove(() => {
              isDragging = true;
              $(window).unbind('mousemove');
            });
          });

          clickableCells.mouseup(() => {
            const wasDragging = isDragging;
            isDragging = false;
            $(window).unbind('mousemove');

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
