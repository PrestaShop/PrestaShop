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

import IframeModal, {
  IframeModalType, InputIframeModalParams,
} from '@components/modal/iframe-modal';
import {ModalParams} from '@components/modal/modal';

export type FormIframeModalType = IframeModalType
export type FormIframeCallbackFunction = (
  form: HTMLElement,
  formData: JQuery.NameValuePair[] | null,
  dataAttributes: DOMStringMap | null,
  event: Event
) => void;
export type FormIframeModalParams = ModalParams & {
  formUrl: string;
  formSelector: string;
  cancelButtonSelector: string;
  modalTitle?: string;
  onFormLoaded?: FormIframeCallbackFunction,
}
export type InputFormIframeModalParams = Partial<FormIframeModalParams> & {
  formUrl: string; // formUrl is mandatory in params
};

/**
 * This modal opens an url containing a form inside a modal and watches for the submit (via iframe loading)
 * On each load it is able to return data from the form via the onFormLoaded callback
 */
export class FormIframeModal extends IframeModal implements FormIframeModalType {
  private readonly onFormLoaded?: FormIframeCallbackFunction;

  private readonly cancelButtonSelector: string;

  private readonly formSelector: string;

  constructor(
    params: InputFormIframeModalParams,
  ) {
    const iframeParams: InputIframeModalParams = {
      iframeUrl: params.formUrl,
      onLoaded: (iframe: HTMLIFrameElement, event: Event) => this.onIframeLoaded(iframe, event),
      ...params,
    };
    super(iframeParams);

    this.onFormLoaded = params.onFormLoaded;
    this.cancelButtonSelector = params.cancelButtonSelector || '.cancel-btn';
    this.formSelector = params.formSelector || 'form';
  }

  private onIframeLoaded(iframe: HTMLIFrameElement, event: Event): void {
    if (!iframe.contentWindow) {
      return;
    }
    const iframeForm: HTMLElement | null = iframe.contentWindow.document.querySelector(this.formSelector);

    if (!iframeForm) {
      return;
    }

    // Close modal when cancel button is clicked
    const cancelButtons = iframeForm.querySelectorAll(this.cancelButtonSelector);
    cancelButtons.forEach((cancelButton) => {
      cancelButton.addEventListener('click', () => {
        this.hide();
      });
    });

    if (!this.onFormLoaded) {
      return;
    }

    let dataAttributes: DOMStringMap | null = null;
    const formData: JQuery.NameValuePair[] | null = $(iframeForm).serializeArray();

    if (iframeForm.dataset) {
      dataAttributes = iframeForm.dataset;
    }

    this.onFormLoaded(iframeForm, formData, dataAttributes, event);
  }
}
