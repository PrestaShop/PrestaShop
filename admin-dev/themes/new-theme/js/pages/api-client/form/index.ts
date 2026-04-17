/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ApiClientMap from '@pages/api-client/api-client-map';
import ConfirmModal from '@components/modal/confirm-modal';
import textToLinkRewriteCopier from '@components/text-to-link-rewrite-copier';

const {$} = window;

$(() => {
  // Auto-generate client_id from client_name (only for new clients)
  textToLinkRewriteCopier({
    sourceElementSelector: ApiClientMap.clientIdSource,
    destinationElementSelector: ApiClientMap.clientIdDestination,
  });

  // Toggle all scopes enable/disable
  document.querySelectorAll<HTMLButtonElement>(ApiClientMap.toggleAllScopes).forEach((button) => {
    button.addEventListener('click', () => {
      const {action} = button.dataset;
      const targetValue = action === 'enable' ? '1' : '0';
      const switches = document.querySelectorAll<HTMLElement>(ApiClientMap.scopesSwitches);

      switches.forEach((switchElement) => {
        const radioToSelect = switchElement.querySelector<HTMLInputElement>(`input[type="radio"][value="${targetValue}"]`);

        if (radioToSelect && !radioToSelect.checked) {
          radioToSelect.click();
        }
      });
    });
  });

  // Display a confirmation modal when regeneration link is clicked before submitting the regeneration
  document.querySelector<HTMLLinkElement>(ApiClientMap.generateSecretLink)?.addEventListener('click', (event) => {
    event.preventDefault();
    const generateLink = event.target as HTMLLinkElement;
    const generateConfirmModal = new ConfirmModal(
      {
        id: ApiClientMap.generateSecretModalId,
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

  function copyClientSecret(copyLink: HTMLLinkElement): void {
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
  }

  // Copy secret to clipboard
  document.querySelector<HTMLLinkElement>(ApiClientMap.copySecret)?.addEventListener('click', (event: Event) => {
    event.preventDefault();
    const copyLink = event.target as HTMLLinkElement;

    copyClientSecret(copyLink);
  });
  document.querySelector<HTMLElement>(ApiClientMap.copySecretIcon)?.addEventListener('click', (event: Event) => {
    event.preventDefault();

    const copyLinkIcon = event.target as HTMLElement;
    const copyLink = copyLinkIcon.parentElement as HTMLLinkElement;

    copyClientSecret(copyLink);
  });
});
