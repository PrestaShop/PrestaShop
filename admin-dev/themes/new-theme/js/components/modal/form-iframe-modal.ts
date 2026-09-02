/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import IframeModal, {
  IframeModalParams,
  IframeModalType, InputIframeModalParams,
} from '@components/modal/iframe-modal';

export type FormIframeModalType = IframeModalType
export type FormIframeCallbackFunction = (
  form: HTMLFormElement,
  formData: FormData,
  dataAttributes: DOMStringMap | null,
  event: Event,
) => void;

export type FormIframeConfirmCallback = (
  form: HTMLFormElement,
  iframe: HTMLIFrameElement,
  event: Event
) => void;

export type FormIframeModalParams = Omit<IframeModalParams, 'iframeUrl' | 'onLoaded' | 'confirmCallback'> & {
  formUrl: string;
  formSelector: string;
  cancelButtonSelector: string;
  modalTitle?: string;
  onFormLoaded?: FormIframeCallbackFunction,
  formConfirmCallback?: FormIframeConfirmCallback,
}
export type InputFormIframeModalParams = Partial<FormIframeModalParams> & {
  formUrl: string; // formUrl is mandatory in params
};

/**
 * This modal opens an url containing a form inside a modal and watches for the submit (via iframe loading)
 * On each load it is able to return data from the form via the onFormLoaded callback
 */
export class FormIframeModal extends IframeModal implements FormIframeModalType {
  constructor(
    params: InputFormIframeModalParams,
  ) {
    const iframeParams: InputIframeModalParams = {
      iframeUrl: params.formUrl,
      onLoaded: (iframe: HTMLIFrameElement, event: Event) => {
        this.onIframeLoaded(
          iframe,
          event,
          params.onFormLoaded,
          params.cancelButtonSelector ?? '.cancel-btn',
          params.formSelector ?? 'form',
        );
      },
      confirmCallback: (iframe: HTMLIFrameElement, event: Event) => {
        this.onConfirmCallback(iframe, event, params.formConfirmCallback, params.formSelector ?? 'form');
      },
      ...params,
    };

    super(iframeParams);
  }

  private onIframeLoaded(
    iframe: HTMLIFrameElement,
    event: Event,
    onFormLoaded: FormIframeCallbackFunction | undefined,
    cancelButtonSelector: string,
    formSelector: string,
  ): void {
    if (!onFormLoaded) {
      return;
    }

    const iframeForm: HTMLFormElement | null = this.getForm(iframe, formSelector);

    if (!iframeForm) {
      return;
    }

    // Close modal when cancel button is clicked
    const cancelButtons = iframeForm.querySelectorAll(cancelButtonSelector);
    cancelButtons.forEach((cancelButton) => {
      cancelButton.addEventListener('click', () => {
        this.hide();
      });
    });

    onFormLoaded(iframeForm, new FormData(iframeForm), iframeForm.dataset ?? null, event);
  }

  private onConfirmCallback(
    iframe: HTMLIFrameElement,
    event: Event,
    formConfirmCallback: FormIframeConfirmCallback | undefined,
    formSelector: string,
  ): void {
    if (!formConfirmCallback) {
      return;
    }

    const iframeForm: HTMLFormElement | null = this.getForm(iframe, formSelector);

    if (!iframeForm) {
      return;
    }

    formConfirmCallback(iframeForm, iframe, event);
  }

  private getForm(iframe: HTMLIFrameElement, formSelector: string): HTMLFormElement | null {
    if (!iframe.contentWindow) {
      return null;
    }

    return iframe.contentWindow.document.querySelector<HTMLFormElement>(formSelector);
  }
}
