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

/* eslint max-classes-per-file: ["error", 2] */

import ResizeObserver from 'resize-observer-polyfill';
import {
  ModalContainerType, ModalContainer, ModalType, ModalParams, Modal,
} from '@components/modal/modal';
import IframeEvent from '@components/modal/iframe-event';
import {isUndefined} from '@PSTypes/typeguard';

export interface IframeModalContainerType extends ModalContainerType {
  iframe: HTMLIFrameElement;
  loader: HTMLElement;
  spinner: HTMLElement;
  closeButton?: HTMLElement;
  confirmButton?: HTMLButtonElement;
}
export interface IframeModalType extends ModalType {
  modal: IframeModalContainerType;
  render: (content: string, hideIframe?: boolean) => void;
}
export type IframeCallbackFunction = (iframe:HTMLIFrameElement, event: Event) => void;
export type IframeEventCallbackFunction = (event: IframeEvent) => void;
export type IframeModalParams = ModalParams & {
  // Callback method executed each time the iframe loads an url
  onLoaded?: IframeCallbackFunction,
  // Callback method executed each time the iframe is about to unload its content
  onUnload?: IframeCallbackFunction,
  // The iframe can launch IframeEvent to communicate with its parent via this callback
  onIframeEvent?: IframeEventCallbackFunction,
  // Initial url of the iframe
  iframeUrl: string;
  // When true the iframe height is computed based on its content
  autoSize: boolean;
  // By default the body of the iframe is used as a reference of its content's size but this option can customize it
  autoSizeContainer: string;
  // Optional, when set a close button is added in the modal's footer
  closeButtonLabel?: string;
  // Optional, when set a confirm button is added in the modal's footer
  confirmButtonLabel?: string;
  // Callback when the confirm button is clicked
  confirmCallback?: (iframe: HTMLIFrameElement, event: Event) => void;
  // By default the iframe closes when confirm button is clicked, this options overrides this behaviour
  closeOnConfirm: boolean;
  // When the iframe is refreshed auto scroll up the body container (true by default)
  autoScrollUp: boolean;
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

  footer?: HTMLElement;

  closeButton?: HTMLElement;

  confirmButton?: HTMLButtonElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: IframeModalParams) {
    super(params);
  }

  protected buildModalContainer(params: IframeModalParams): void {
    super.buildModalContainer(params);
    this.container.classList.add('modal-iframe');

    // Message is hidden by default
    this.message.classList.add('d-none');

    this.iframe = document.createElement('iframe');
    this.iframe.frameBorder = '0';
    this.iframe.scrolling = 'no';
    this.iframe.width = '100%';
    this.iframe.setAttribute('name', `${params.id}-iframe`);
    if (!params.autoSize) {
      this.iframe.height = '100%';
    }

    this.loader = document.createElement('div');
    this.loader.classList.add('modal-iframe-loader');

    this.spinner = document.createElement('div');
    this.spinner.classList.add('spinner');

    this.loader.appendChild(this.spinner);
    this.body.append(this.loader, this.iframe);

    // Modal footer element
    if (!isUndefined(params.closeButtonLabel) || !isUndefined(params.confirmButtonLabel)) {
      this.footer = document.createElement('div');
      this.footer.classList.add('modal-footer');

      // Modal close button element
      if (!isUndefined(params.closeButtonLabel)) {
        this.closeButton = document.createElement('button');
        this.closeButton.setAttribute('type', 'button');
        this.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
        this.closeButton.dataset.dismiss = 'modal';
        this.closeButton.innerHTML = params.closeButtonLabel;
        this.footer.append(this.closeButton);
      }

      // Modal confirm button element
      if (!isUndefined(params.confirmButtonLabel)) {
        this.confirmButton = document.createElement('button');
        this.confirmButton.setAttribute('type', 'button');
        this.confirmButton.classList.add('btn', 'btn-primary', 'btn-lg', 'btn-confirm-submit');
        if (params.closeOnConfirm) {
          this.confirmButton.dataset.dismiss = 'modal';
        }
        this.confirmButton.innerHTML = params.confirmButtonLabel;
        this.footer.append(this.confirmButton);
      }

      // Appending element to the modal
      this.content.append(this.footer);
    }
  }
}

/**
 * This modal opens an url inside a modal, it then can handle two specific callbacks
 * - onLoaded: called when the iframe has juste been refreshed
 * - onUnload: called when the iframe is about to refresh (so it is unloaded)
 */
export class IframeModal extends Modal implements IframeModalType {
  modal!: IframeModalContainerType;

  protected autoSize!: boolean;

  protected autoSizeContainer!: string;

  protected resizeObserver?: ResizeObserver | null;

  constructor(
    inputParams: InputIframeModalParams,
  ) {
    const params: IframeModalParams = {
      id: 'iframe-modal',
      closable: false,
      autoSize: true,
      autoSizeContainer: 'body',
      closeOnConfirm: true,
      autoScrollUp: true,
      ...inputParams,
    };
    super(params);
  }

  protected initContainer(params: IframeModalParams): void {
    // Construct the container
    this.modal = new IframeModalContainer(params);
    super.initContainer(params);

    this.autoSize = params.autoSize;
    this.autoSizeContainer = params.autoSizeContainer;
    this.modal.iframe.addEventListener('load', (loadedEvent: Event) => {
      // Scroll the body container back to the top after iframe loaded
      this.modal.body.scroll(0, 0);
      this.hideLoading();
      if (params.onLoaded) {
        params.onLoaded(this.modal.iframe, loadedEvent);
      }

      if (this.modal.iframe.contentWindow) {
        this.modal.iframe.contentWindow.addEventListener('beforeunload', (unloadEvent: BeforeUnloadEvent) => {
          if (params.onUnload) {
            params.onUnload(this.modal.iframe, unloadEvent);
          }
          this.showLoading();
        });

        // Auto resize the iframe container
        this.initAutoResize();
      }
    });

    this.$modal.on('shown.bs.modal', () => {
      this.modal.iframe.src = params.iframeUrl;
    });

    window.addEventListener(IframeEvent.parentWindowEvent, ((event: IframeEvent) => {
      if (params.onIframeEvent) {
        params.onIframeEvent(event);
      }
    }) as EventListener);

    if (this.modal.confirmButton && params.confirmCallback) {
      this.modal.confirmButton.addEventListener('click', (event) => {
        if (params.confirmCallback) {
          params.confirmCallback(this.modal.iframe, event);
        }
      });
    }
  }

  render(content: string, hideIframe: boolean = true): this {
    this.modal.message.innerHTML = content;
    this.modal.message.classList.remove('d-none');

    if (hideIframe) {
      this.hideIframe();
    }

    this.autoResize();
    this.hideLoading();

    return this;
  }

  showLoading(): this {
    const bodyHeight = this.getOuterHeight(this.modal.body);
    const bodyWidth = this.getOuterWidth(this.modal.body);
    this.modal.loader.style.height = `${bodyHeight}px`;
    this.modal.loader.style.width = `${bodyWidth}px`;
    this.modal.loader.classList.remove('d-none');
    this.modal.iframe.classList.remove('invisible');
    this.modal.iframe.classList.add('invisible');

    return this;
  }

  hideLoading(): this {
    this.modal.iframe.classList.remove('invisible');
    this.modal.iframe.classList.add('visible');
    this.modal.loader.classList.add('d-none');

    return this;
  }

  hide(): this {
    super.hide();
    this.cleanResizeObserver();

    return this;
  }

  hideIframe(): void {
    this.modal.iframe.classList.add('d-none');
  }

  private getResizableContainer(): HTMLElement | null {
    if (this.autoSize && this.modal.iframe.contentWindow) {
      return this.modal.iframe.contentWindow.document.querySelector(this.autoSizeContainer);
    }

    return null;
  }

  private initAutoResize(): void {
    const iframeContainer: HTMLElement | null = this.getResizableContainer();

    if (iframeContainer) {
      this.cleanResizeObserver();
      this.resizeObserver = new ResizeObserver(() => {
        this.autoResize();
      });

      this.resizeObserver.observe(iframeContainer);
    }
    this.autoResize();
  }

  private cleanResizeObserver(): void {
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
      this.resizeObserver = null;
    }
  }

  private autoResize(): void {
    const iframeContainer: HTMLElement | null = this.getResizableContainer();

    if (iframeContainer) {
      const iframeScrollHeight = iframeContainer.scrollHeight;
      const contentHeight = this.getOuterHeight(this.modal.message)
        + iframeScrollHeight;

      // Avoid applying height of 0 (on first load for example)
      if (contentHeight) {
        // We force the iframe to its real height and it's the container that handles the overflow with scrollbars
        this.modal.iframe.style.height = `${contentHeight}px`;
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

  private getOuterWidth(element: HTMLElement): number {
    // If the element height is 0 it is likely empty or hidden, then no need to compute the margin
    if (!element.offsetWidth) {
      return 0;
    }

    let width = element.offsetWidth;
    const style: CSSStyleDeclaration = getComputedStyle(element);

    width += parseInt(style.marginLeft, 10) + parseInt(style.marginRight, 10);

    return width;
  }
}

export default IframeModal;
