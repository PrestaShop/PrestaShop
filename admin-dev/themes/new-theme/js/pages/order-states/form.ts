/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import initColorPickers from '@app/utils/colorpicker';
import TranslatableChoice from '@components/form/translatable-choice';
import FormMap from '@pages/order-states/form-map';

const {$} = window;

$(() => {
  initColorPickers();
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
  new TranslatableChoice();

  let templatePreviewWindow: null | Record<string, any> = null;
  function viewTemplates($uri: string) {
    if (templatePreviewWindow != null && !templatePreviewWindow.closed) {
      templatePreviewWindow.close();
    }
    templatePreviewWindow = window.open(
      $uri,
      'tpl_viewing',
      'toolbar=0,'
        + 'location=0,'
        + 'directories=0,'
        + 'statfr=no,'
        + 'menubar=0,'
        + 'scrollbars=yes,'
        + 'resizable=yes,'
        + 'width=520,'
        + 'height=400,'
        + 'top=50,'
        + 'left=300',
    );
    if (templatePreviewWindow) {
      templatePreviewWindow.focus();
    }
  }

  $(() => {
    if (!$(FormMap.sendEmailSelector).is(':checked')) {
      $(FormMap.mailTemplateSelector).hide();
    }
    $(document).on('change', FormMap.sendEmailSelector, () => {
      $(FormMap.mailTemplateSelector).slideToggle();
    });

    $(document).on('click', FormMap.mailTemplatePreview, (event) => {
      const $element = $(event.currentTarget);
      const $select = $element
        .closest('.form-group')
        .find('select.translatable_choice:visible');
      const $uri = $select.find('option:selected').attr('data-preview');

      viewTemplates(<string>$uri);
    });
  });
});
