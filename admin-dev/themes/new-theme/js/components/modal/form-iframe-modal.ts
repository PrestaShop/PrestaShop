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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
