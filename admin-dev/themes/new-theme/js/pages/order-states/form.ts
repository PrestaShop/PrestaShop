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

import initColorPickers from '@app/utils/colorpicker';
import TranslatableChoice from '@components/form/translatable-choice';
import TranslatableInput from '@components/translatable-input';
import FormMap from '@pages/order-states/form-map';

const {$} = window;

$(() => {
  initColorPickers();
  new TranslatableInput();
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
