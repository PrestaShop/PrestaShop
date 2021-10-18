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

  const generateFormValuesHash = () => multistoreForm.serialize();

  const originalFormValuesHash = generateFormValuesHash();

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
        $.post({
          url: <string>multistoreForm.attr('action'),
          data: multistoreForm.serialize(),
        }).then(() => {
          window.location.href = path;
        });
      }, () => {
        window.location.href = path;
      },
    );

    modal.show();
  };

  if (multistoreForm) {
    // Bind click on header's links
    $modalItem.find('a').each((index, itemLink) => {
      $(itemLink).on('click', (event) => {
        if (originalFormValuesHash !== generateFormValuesHash()) {
          const targetUrl = $(itemLink).attr('href');
          showConfirmModal(`${targetUrl}`);

          event.stopPropagation();
          event.preventDefault();
        }
      });
    });
  }
};

$(() => {
  initMultistoreForm();
});
