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

import ConfirmModal from '@components/modal';

const {$} = window;

$(() => {
  const $submitButton = $('#submit-btn-feature-flag');
  const $form = $('#feature-flag-form');

  let noFieldModifiedInTheForm = true;

  $form.find('input').change(() => {
    noFieldModifiedInTheForm = false;
  });

  $submitButton.on('click', (event) => {
    event.preventDefault();

    const formData = $form.serializeArray();
    let oneFlagIsEnabled = false;

    for (let i = 0; i < formData.length; i += 1) {
      if ((formData[i].name !== 'form[_token]') && (formData[i].value !== '0')) {
        oneFlagIsEnabled = true;
      }
    }

    const modal = new ConfirmModal(
      {
        id: 'modal-confirm-submit-feature-flag',
        confirmTitle: $submitButton.data('modal-title'),
        confirmMessage: $submitButton.data('modal-message'),
        confirmButtonLabel: $submitButton.data('modal-apply'),
        closeButtonLabel: $submitButton.data('modal-cancel'),
      },
      () => {
        $form.submit();
      },
    );

    if (oneFlagIsEnabled && !noFieldModifiedInTheForm) {
      modal.show();
    } else {
      $form.submit();
    }
  });
});
