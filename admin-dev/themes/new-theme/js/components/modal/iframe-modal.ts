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

/* eslint max-classes-per-file: ["error", 2] */

import {
  ModalContainerType, ModalContainer, ModalType, ModalParams,
} from '@components/modal/modal-container';

const {$} = window;

export interface IframeModalContainerType extends ModalContainerType {
  iframe: HTMLIFrameElement;
  loader: HTMLElement;
  spinner: HTMLElement;
}
export interface IframeModalType extends ModalType {
  modal: IframeModalContainerType;
  displayMessage: (message: string, hideIframe?: boolean) => void;
}
export type IframeCallbackFunction = (iframe:HTMLIFrameElement, event: Event) => void;
export type IframeModalParams = ModalParams & {
  modalTitle?: string;
  onLoaded?: IframeCallbackFunction,
  onUnload?: IframeCallbackFunction,
  iframeUrl: string;
  autoSize: boolean;
}
export type InputIframeModalParams = Partial<IframeModalParams> & {
  iframeUrl: string; // iframeUrl is mandatory in input
};

/**
 * This class is used to build the modal DOM elements, it is not usable as is because it doesn't even have a show
 * method and the elements are created but not added to the DOM. It just creates a basic DOM structure of a
 * Bootstrap modal, thus keeping the logic class of the modal separated.
 *
 * This container is built on the basic ModalContainer and adds an iframe to load external content along with a
 * loader div on top of it.
 *
 * @param {InputIframeModalParams} inputParams
 */
export class IframeModalContainer extends ModalContainer implements IframeModalContainerType {
  iframe!: HTMLIFrameElement;

  loader!: HTMLElement;

  spinner!: HTMLElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: IframeModalParams) {
    super(params);
  }

  buildModalContainer(params: IframeModalParams): void {
    super.buildModalContainer(params);
    this.container.classList.add('modal-iframe');

    // Message is hidden by default
    this.message.classList.add('d-none');

    this.iframe = document.createElement('iframe');
    this.iframe.frameBorder = '0';
    this.iframe.scrolling = 'auto';
    this.iframe.width = '100%';
    this.iframe.height = '100%';

    this.loader = document.createElement('div');
    this.loader.classList.add('modal-iframe-loader');

    this.spinner = document.createElement('div');
    this.spinner.classList.add('spinner');

    this.loader.appendChild(this.spinner);
    this.body.append(this.loader, this.iframe);
  }
}

/**
 * This modal opens an url inside a modal, it then can handle two specific callbacks
 * - onLoaded: called when the iframe has juste been refreshed
 * - onUnload: called when the iframe is about to refresh (so it is unloaded)
 */
export class IframeModal implements IframeModalType {
  modal: IframeModalContainerType;

  protected $modal: JQuery;

  protected autoSize: boolean;

  constructor(
    inputParams: InputIframeModalParams,
  ) {
    const params: IframeModalParams = {
      id: 'iframe-modal',
      closable: false,
      autoSize: true,
      ...inputParams,
    };

    // Construct the container
    this.autoSize = params.autoSize;
    this.modal = new IframeModalContainer(params);

    const {id, closable} = params;

    // jQuery modal object
    this.$modal = $(this.modal.container);
    this.modal.iframe.addEventListener('load', (loadedEvent) => {
      this.hideLoading();
      if (params.onLoaded) {
        params.onLoaded(this.modal.iframe, loadedEvent);
      }

      if (this.modal.iframe.contentWindow) {
        this.modal.iframe.contentWindow.addEventListener('beforeunload', (unloadEvent) => {
          if (params.onUnload) {
            params.onUnload(this.modal.iframe, unloadEvent);
          }
          this.showLoading();
        });

        // Auto resize the iframe container
        this.autoResize();
      }
    });

    this.$modal.modal({
      backdrop: closable ? true : 'static',
      keyboard: closable !== undefined ? closable : true,
      show: false,
    });

    this.$modal.on('hidden.bs.modal', () => {
      const modal = document.querySelector(`#${id}`);

      if (modal) {
        modal.remove();
      }
    });
    this.$modal.on('shown.bs.modal', () => {
      this.modal.iframe.src = params.iframeUrl;
    });

    document.body.appendChild(this.modal.container);
  }

  displayMessage(message: string, hideIframe: boolean = true): void {
    this.modal.message.innerHTML = message;
    this.modal.message.classList.remove('d-none');

    if (hideIframe) {
      this.hideIframe();
    }

    this.autoResize();
    this.hideLoading();
  }

  show(): void {
    this.$modal.modal('show');
  }

  hide(): void {
    this.$modal.modal('hide');
  }

  private hideIframe(): void {
    this.modal.iframe.classList.add('d-none');
  }

  private showLoading(): void {
    this.modal.loader.classList.remove('d-none');
  }

  private hideLoading(): void {
    this.modal.loader.classList.add('d-none');
  }

  private autoResize(): void {
    if (this.autoSize) {
      const iframeScrollHeight = this.modal.iframe.contentWindow ? this.modal.iframe.contentWindow.document.body.scrollHeight : 0;
      const contentHeight = this.getOuterHeight(this.modal.header)
        + this.getOuterHeight(this.modal.message)
        + iframeScrollHeight;

      // Avoid applying height of 0 (on first load for example)
      if (contentHeight) {
        this.modal.dialog.style.height = `${contentHeight}px`;
      }
    }
  }

  private getOuterHeight(element: HTMLElement): number {
    // If the element height is 0 it is likely empty or hidden, then no need to compute the margin
    if (!element.offsetHeight) {
      return 0;
    }

    let height = element.offsetHeight;
    const style: CSSStyleDeclaration = getComputedStyle(element);

    height += parseInt(style.marginTop, 10) + parseInt(style.marginBottom, 10);

    return height;
  }
}

export default IframeModal;
