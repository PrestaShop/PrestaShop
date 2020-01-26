/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import initColorPickers from '@app/utils/colorpicker';
import TranslatableChoice from '@components/form/translatable-choice';
import TranslatableInput from '@components/translatable-input';

const {$} = window;

$(() => {
  initColorPickers();
  new TranslatableInput();
  new TranslatableChoice();

  let templatePreviewWindow = null;
  function viewTemplates($uri) {
    if (templatePreviewWindow != null && !templatePreviewWindow.closed) {
      templatePreviewWindow.close();
    }
    templatePreviewWindow = window.open($uri, 'tpl_viewing', 'toolbar=0,location=0,directories=0,statfr=no,menubar=0,scrollbars=yes,resizable=yes,width=520,height=400,top=50,left=300');
    templatePreviewWindow.focus();
  }

  $(document).ready(() => {
    if (!$('#order_state_send_email').is(':checked')) {
      $('.order_state_template_select').hide();
    }
    $(document).on('change', '#order_state_send_email', () => {
      $('.order_state_template_select').slideToggle();
    });

    $(document).on('click', '#order_state_template_preview', (event) => {
      const $element = $(event.currentTarget);
      const $select = $element.closest('.form-group').find('select.translatable_choice:visible');
      const $uri = $select.find('option:selected').attr('data-preview');

      viewTemplates($uri);
    });
  });
});
