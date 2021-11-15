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

import ComponentsMap from '@components/components-map';
import {ConfirmModal} from '@components/modal/confirm-modal';

const {$} = window;

const initMultistoreForm = () => {
  const MultistoreFormMap = ComponentsMap.multistoreForm;
  const MultistoreHeaderMap = ComponentsMap.multistoreHeader;
  const multistoreForm = $(MultistoreFormMap.formRow).closest('form');
  const $modalItem = $(MultistoreHeaderMap.modal);
  const translations = $(MultistoreHeaderMap.header).data('translations');

  const generateFormValuesHash = () => {
    const formValues = multistoreForm.serializeArray().reduce((obj: any, item: any) => {
      const form = multistoreForm[0] as HTMLFormElement;
      const formElement = form.elements[item.name] as HTMLFormElement;

      if ($(formElement).prop('disabled')) { // Ignore if element is disabled
        return obj;
      }
      const fieldValues = obj;

      if (formElement instanceof HTMLTextAreaElement) {
        fieldValues[item.name] = item.value
          .replace(/<[^>]*>?/gm, '') // remove html tags
          .replace(/\r?\n|\r/g, '') //remove line breaks
          .replace(/(\.\s)/g, '.'); //remove space after dots added by TinyMCE
      } else {
        fieldValues[item.name] = item.value;
      }

      return fieldValues;
    });

    return JSON.stringify(formValues);
  };

  /**
   * @param {string} path
   */
  const showConfirmModal = (
    path: string,
  ) => {
    const confirmTitle = translations['modal.confirm_leave.title'];
    const confirmMessage = translations['modal.confirm_leave.body'];
    const confirmButtonLabel = translations['modal.confirm_leave.confirm'];
    const closeButtonLabel = translations['modal.confirm_leave.cancel'];
    const confirmButtonClass = 'btn-primary';

    const modal = new ConfirmModal(
      {
        confirmTitle,
        confirmMessage,
        confirmButtonLabel,
        closeButtonLabel,
        confirmButtonClass,
      },
      () => {
        window.location.href = path;
      },
    );

    modal.show();
  };

  if (multistoreForm) {
    const originalFormValuesHash = generateFormValuesHash();

    // Bind click on header's links
    $modalItem.find('a').each((index, itemLink) => {
      $(itemLink).on('click', () => {
        const formValuesHash = generateFormValuesHash();

        if (originalFormValuesHash !== formValuesHash) {
          const targetUrl = $(itemLink).attr('href');
          showConfirmModal(`${targetUrl}`);

          return false;
        }

        return true;
      });
    });
  }
};

$(() => {
  window.prestashop.component.EventEmitter.on('tinymceInitialized', () => {
    initMultistoreForm();
  });
});
