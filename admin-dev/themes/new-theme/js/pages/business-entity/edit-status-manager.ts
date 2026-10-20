/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import BusinessEntityListMap from './business-entity-list-map';

export default class EditStatusManager {
  private requestedFormUrl: string | null = null;

  private submitting = false;

  constructor() {
    document.addEventListener('click', this.onEditStatusClick);
    document.querySelector(BusinessEntityListMap.editStatusModal)?.addEventListener('submit', this.onSubmit);
  }

  private onEditStatusClick = (event: Event): void => {
    const link = (event.target as HTMLElement).closest<HTMLAnchorElement>(BusinessEntityListMap.editStatusRowLink);

    if (!link) {
      return;
    }

    const {statusFormUrl} = link.dataset;

    if (!statusFormUrl) {
      throw new Error('The edit status action carries no form url.');
    }

    this.loadStatusForm(statusFormUrl);
  };

  private async loadStatusForm(url: string): Promise<void> {
    const modal = document.querySelector<HTMLElement>(BusinessEntityListMap.editStatusModal);
    const formContainer = document.querySelector<HTMLElement>(BusinessEntityListMap.editStatusFormContainer);

    if (!modal || !formContainer) {
      throw new Error('Edit status modal not found.');
    }

    formContainer.innerHTML = '';
    this.clearSubmissionErrors();
    this.toggleLoadingError(false);
    // The footer button targets the fragment's form through form=, so it stays inert until the
    // fragment is injected. It ships disabled and is only armed once that happened.
    this.toggleSubmitButton(false);
    modal.dataset.state = 'loading';
    this.requestedFormUrl = url;

    try {
      const response = await fetch(url, {method: 'GET'});

      if (!response.ok) {
        throw new Error(await response.text());
      }

      const html = await response.text();

      if (this.requestedFormUrl !== url) {
        return;
      }

      formContainer.innerHTML = html;
      this.toggleSubmitButton(true);

      window.prestaShopUiKit.init();
    } catch (error) {
      console.error('Error while loading the business entity status form:', error);
      this.toggleLoadingError(true);
    } finally {
      if (this.requestedFormUrl === url) {
        modal.dataset.state = 'loaded';
      }
    }
  }

  private onSubmit = (event: Event): void => {
    const form = (event.target as HTMLElement).closest<HTMLFormElement>(BusinessEntityListMap.editStatusForm);

    if (!form) {
      return;
    }

    event.preventDefault();
    this.sendStatusChange(form);
  };

  private async sendStatusChange(form: HTMLFormElement): Promise<void> {
    if (this.submitting) {
      return;
    }

    this.submitting = true;
    this.clearSubmissionErrors();
    this.toggleSubmitButton(false);

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        redirect: 'manual',
      });

      // A guard that answers with a redirect (demo mode, expired session) leaves its flash
      // unconsumed because the redirect is not followed: reloading is what displays it.
      if (response.ok || response.type === 'opaqueredirect') {
        window.location.reload();

        return;
      }

      const payload = await this.readJsonPayload(response);
      const generalError = payload.error ?? payload.message;

      if (payload.form_errors || generalError) {
        this.showSubmissionErrors(payload.form_errors ?? {}, generalError);
      } else {
        this.toggleSavingError(true);
      }
    } catch (error) {
      console.error('Error while saving the business entity status:', error);
      this.toggleSavingError(true);
    }

    this.submitting = false;
    this.toggleSubmitButton(true);
  }

  private async readJsonPayload(response: Response): Promise<Record<string, any>> {
    if (!response.headers.get('content-type')?.includes('application/json')) {
      return {};
    }

    try {
      return await response.json();
    } catch (error) {
      console.error('Unreadable JSON answer from the business entity status endpoint:', error);

      return {};
    }
  }

  private showSubmissionErrors(fieldErrors: Record<string, string[]>, generalError?: string): void {
    let attached = 0;
    let unattached: string[] = [];

    Object.entries(fieldErrors).forEach(([field, messages]) => {
      const group = document
        .querySelector(`#${BusinessEntityListMap.editStatusFieldPrefix}${field}`)
        ?.closest('.form-group');

      if (!group) {
        unattached = unattached.concat(messages);

        return;
      }

      group.appendChild(this.buildError(messages));
      attached += 1;
    });

    const nothingShownYet = attached === 0 && unattached.length === 0;

    if (nothingShownYet && generalError) {
      unattached = [generalError];
    }

    if (unattached.length > 0) {
      document
        .querySelector<HTMLElement>(BusinessEntityListMap.editStatusFormContainer)
        ?.prepend(this.buildError(unattached));
    }
  }

  private buildError(messages: string[]): HTMLElement {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger js-business-entity-status-error';
    alert.setAttribute('role', 'alert');

    const text = document.createElement('p');
    text.className = 'alert-text';
    text.textContent = messages.join(' ');
    alert.appendChild(text);

    return alert;
  }

  private clearSubmissionErrors(): void {
    document.querySelectorAll(BusinessEntityListMap.editStatusSubmissionError).forEach((alert) => alert.remove());
    this.toggleSavingError(false);
  }

  private toggleSavingError(visible: boolean): void {
    document.querySelector(BusinessEntityListMap.editStatusSavingError)?.classList.toggle('d-none', !visible);
  }

  private toggleLoadingError(visible: boolean): void {
    document.querySelector(BusinessEntityListMap.editStatusLoadingError)?.classList.toggle('d-none', !visible);
  }

  private toggleSubmitButton(enabled: boolean): void {
    const button = document.querySelector<HTMLButtonElement>(BusinessEntityListMap.editStatusSubmitButton);

    if (button) {
      button.disabled = !enabled;
    }
  }
}
