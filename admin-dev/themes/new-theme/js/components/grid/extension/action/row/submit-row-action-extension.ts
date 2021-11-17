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
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

import {ConfirmModal} from '@components/modal';

const {$} = window;

/**
 * Class SubmitRowActionExtension handles submitting of row action
 */
export default class SubmitRowActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid.getContainer().on('click', '.js-submit-row-action', (event) => {
      event.preventDefault();

      const $button = $(event.currentTarget);
      const confirmMessage = $button.data('confirmMessage');
      const confirmTitle = $button.data('title');

      const method = $button.data('method');

      if (confirmTitle) {
        this.showConfirmModal(
          $button,
          grid,
          confirmMessage,
          confirmTitle,
          method,
        );
      } else {
        // eslint-disable-next-line
        if (confirmMessage.length && !window.confirm(confirmMessage)) {
          return;
        }

        this.postForm($button, method);
      }
    });
  }

  postForm($button: JQuery, method: string): void {
    const isGetOrPostMethod = ['GET', 'POST'].includes(method);

    const $form = $('<form>', {
      action: $button.data('url'),
      method: isGetOrPostMethod ? method : 'POST',
    }).appendTo('body');

    if (!isGetOrPostMethod) {
      $form.append(
        $('<input>', {
          type: '_hidden',
          name: '_method',
          value: method,
        }),
      );
    }

    $form.submit();
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   * @param {string} confirmMessage
   * @param {string} confirmTitle
   * @param {string} method
   */
  showConfirmModal(
    $submitBtn: JQuery,
    grid: Grid,
    confirmMessage: string,
    confirmTitle: string,
    method: string,
  ): void {
    const confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
    const closeButtonLabel = $submitBtn.data('closeButtonLabel');
    const confirmButtonClass = $submitBtn.data('confirmButtonClass');

    const modal = new ConfirmModal(
      {
        id: GridMap.confirmModal(grid.getId()),
        confirmTitle,
        confirmMessage,
        confirmButtonLabel,
        closeButtonLabel,
        confirmButtonClass,
      },
      () => this.postForm($submitBtn, method),
    );

    modal.show();
  }
}
