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

import ApiAccessMap from '@pages/api-access/api-access-map';
import ConfirmModal from '@components/modal/confirm-modal';

const {$} = window;

$(() => {
  // Display a confirmation modal when regeneration link is clicked before submitting the regeneration
  document.querySelector<HTMLLinkElement>(ApiAccessMap.generateSecretLink)?.addEventListener('click', (event) => {
    event.preventDefault();
    const generateLink = event.target as HTMLLinkElement;
    const generateConfirmModal = new ConfirmModal(
      {
        id: ApiAccessMap.generateSecretModalId,
        confirmTitle: generateLink.dataset.confirmTitle,
        confirmMessage: generateLink.dataset.confirmMessage,
        confirmButtonLabel: generateLink.dataset.confirmButtonLabel,
        closeButtonLabel: generateLink.dataset.closeButtonLabel,
        confirmButtonClass: 'btn-warning',
        closable: true,
      },
      () => {
        submitGeneration(generateLink);
      },
    );

    generateConfirmModal.show();
  });

  function submitGeneration(generateLink: HTMLLinkElement): void {
    const form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', generateLink.href);
    form.setAttribute('style', 'display: none;');
    document.body.appendChild(form);
    form.submit();
  }

  // Copy secret to clipboard
  document.querySelector<HTMLLinkElement>(ApiAccessMap.copySecret)?.addEventListener('click', (event) => {
    event.preventDefault();
    const copyLink = event.target as HTMLLinkElement;

    // Fallback to navigator.clipboard.writeText because it only works with https
    const input = document.createElement('input');
    input.value = copyLink.dataset.secret ?? '';
    document.body.prepend(input);
    input.select();
    input.setSelectionRange(0, 99999);
    try {
      document.execCommand('copy');
    } finally {
      input.remove();
    }
  });
});
