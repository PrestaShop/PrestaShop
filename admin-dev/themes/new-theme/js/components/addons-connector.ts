/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Responsible for connecting to addons marketplace.
 * Makes an addons connect request to the server, displays error messages if it fails.
 */
export default class AddonsConnector {
  addonsConnectFormSelector: string;

  $loadingSpinner: JQuery;

  constructor(
    addonsConnectFormSelector: string,
    loadingSpinnerSelector: string,
  ) {
    this.addonsConnectFormSelector = addonsConnectFormSelector;
    this.$loadingSpinner = $(loadingSpinnerSelector);

    this.initEvents();
  }

  /**
   * Initialize events related to connection to addons.
   *
   * @private
   */
  private initEvents(): void {
    $('body').on('submit', this.addonsConnectFormSelector, (event) => {
      const $form = $(event.currentTarget);
      event.preventDefault();
      event.stopPropagation();

      this.connect(<string>$form.attr('action'), $form.serialize());
    });
  }

  /**
   * Do a POST request to connect to addons.
   *
   * @param {String} addonsConnectUrl
   * @param {Object} formData
   *
   * @private
   */
  private connect(addonsConnectUrl: string, formData: string): void {
    $.ajax({
      method: 'POST',
      url: addonsConnectUrl,
      dataType: 'json',
      data: formData,
      beforeSend: () => {
        this.$loadingSpinner.show();
        $('button.btn[type="submit"]', this.addonsConnectFormSelector).hide();
      },
    }).then(
      (response) => {
        if (response.success === 1) {
          window.location.reload();
        } else {
          $.growl.error({
            message: response.message,
          });

          this.$loadingSpinner.hide();
          $(
            'button.btn[type="submit"]',
            this.addonsConnectFormSelector,
          ).fadeIn();
        }
      },
      () => {
        $.growl.error({
          message: $(this.addonsConnectFormSelector).data('error-message'),
        });

        this.$loadingSpinner.hide();
        $('button.btn[type="submit"]', this.addonsConnectFormSelector).show();
      },
    );
  }
}
