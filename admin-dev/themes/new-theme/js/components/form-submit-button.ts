/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ComponentsMap from './components-map';

const {$} = window;

/**
 * Component which allows submitting very simple forms without having to use <form> element.
 *
 * Useful when performing actions on resource where URL contains all needed data.
 * For example, to toggle category status via "POST /categories/2/toggle-status)"
 * or delete cover image via "POST /categories/2/delete-cover-image".
 *
 * Usage example in template:
 *
 * <button class="js-form-submit-btn"
 *         data-form-submit-url="/my-custom-url"          // (required) URL to which form will be submitted
 *         data-method="GET|POST|DELETE|PATCH"            // (optional) specify the verb to use for the request.
 *                                                        // POST is taken by default if not value is set
 *         data-form-csrf-token="my-generated-csrf-token" // (optional) to increase security
 *         data-form-confirm-message="Are you sure?"      // (optional) to confirm action before submit
 *         type="button"                                  // make sure its simple button
 *                                                        // so we can avoid submitting actual form
 *                                                        // when our button is defined inside form
 * >
 *     Click me to submit form
 * </button>
 *
 * In page specific JS you have to enable this feature:
 *
 * new FormSubmitButton();
 */
export default class FormSubmitButton {
  constructor() {
    $(document).on(
      'click',
      ComponentsMap.formSubmitButton,
      (event: JQueryEventObject) => {
        event.preventDefault();

        const $btn = $(event.target);

        if (
          $btn.data('form-confirm-message')
          && window.confirm($btn.data('form-confirm-message')) === false
        ) {
          return;
        }

        let method = 'POST';
        let addInput = null;

        if ($btn.data('method')) {
          const btnMethod = $btn.data('method');
          const isGetOrPostMethod = ['GET', 'POST'].includes(btnMethod);
          method = isGetOrPostMethod ? btnMethod : 'POST';

          if (!isGetOrPostMethod) {
            addInput = $('<input>', {
              type: '_hidden',
              name: '_method',
              value: btnMethod,
            });
          }
        }

        const $form = $('<form>', {
          action: $btn.data('form-submit-url'),
          method,
        });

        if (addInput) {
          $form.append(addInput);
        }

        if ($btn.data('form-csrf-token')) {
          $form.append(
            $('<input>', {
              type: '_hidden',
              name: '_csrf_token',
              value: $btn.data('form-csrf-token'),
            }),
          );
        }

        $form.appendTo('body').submit();
      },
    );
  }
}
