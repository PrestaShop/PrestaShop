/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
          type: 'hidden',
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
