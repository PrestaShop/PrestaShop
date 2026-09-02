/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

class HookStatusHandler {
  $hookStatus: JQuery;

  $modulePositionsForm: JQuery;

  constructor() {
    const self = this;
    this.$hookStatus = $('.hook-switch-action');
    this.$modulePositionsForm = $('#module-positions-form');

    this.$hookStatus.on('change', function (e) {
      e.stopImmediatePropagation();
      self.toogleHookStatus($(this));
    });
  }

  /**
   * Toogle hooks status
   */
  toogleHookStatus($hookElement: JQuery): void {
    $.ajax({
      type: 'POST',
      headers: {'cache-control': 'no-cache'},
      url: this.$modulePositionsForm.data('togglestatus-url'),
      data: {hookId: $hookElement.data('hook-id')},
      success(data) {
        if (data.status) {
          window.showSuccessMessage(data.message);
          const $hookModulesList = $hookElement
            .closest('.hook-panel')
            .find('.module-list, .module-list-disabled');
          $hookModulesList.fadeTo(500, data.hook_status ? 1 : 0.5);
        } else {
          window.showErrorMessage(data.message);
        }
      },
    });
  }
}

export default HookStatusHandler;
