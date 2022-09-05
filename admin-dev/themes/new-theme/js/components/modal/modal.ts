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

export interface ModalContainerType {
  container: HTMLElement;
  dialog: HTMLElement;
  content: HTMLElement;
  body: HTMLElement;
  message: HTMLElement;
  header: HTMLElement;
  title?: HTMLElement;
  closeIcon?: HTMLButtonElement;
}
export interface ModalCoreType {
  show: () => void;
  hide: () => void;
}
export interface ModalType extends ModalCoreType {
  modal: ModalContainerType;
  render: (content: string) => void;
}
export type CssProps = Record<string, string>;
export type ModalParams = {
  id: string;
  closable?: boolean;
  modalTitle?: string
  dialogStyle?: CssProps;
  closeCallback?: () => void;
}
export type InputModalParams = Partial<ModalParams>;

/**
 * This class is used to build the modal DOM elements, it is not usable as is because it doesn't even have a show
 * method and the elements are created but not added to the DOM. It just creates a basic DOM structure of a
 * Bootstrap modal, thus keeping the logic class of the modal separated.
 *
 * This is the most basic modal container (only the modal and dialog box, with a close icon
 * and an optional title). No footer and no content is handled.
 *
 * @param {ModalParams} params
 */
export class ModalContainer implements ModalContainerType {
  container!: HTMLElement;

  dialog!: HTMLElement;

  content!: HTMLElement;

  message!: HTMLElement;

  header!: HTMLElement;

  title?: HTMLElement;

  closeIcon?: HTMLButtonElement;

  body!: HTMLElement;

  constructor(inputParams: InputModalParams) {
    const params: ModalParams = {
      id: 'confirm-modal',
      closable: false,
      ...inputParams,
    };

    this.buildModalContainer(params);
  }

  protected buildModalContainer(params: ModalParams): void {
    // Main modal element
    this.container = document.createElement('div');
    this.container.classList.add('modal', 'fade');
    this.container.id = params.id;

    // Modal dialog element
    this.dialog = document.createElement('div');
    this.dialog.classList.add('modal-dialog');
    if (params.dialogStyle) {
      Object.keys(params.dialogStyle).forEach((key: string) => {
        // @ts-ignore
        this.dialog.style[key] = params.dialogStyle[key];
      });
    }

    // Modal content element
    this.content = document.createElement('div');
    this.content.classList.add('modal-content');

    // Modal message element
    this.message = document.createElement('p');
    this.message.classList.add('modal-message');

    // Modal header element
    this.header = document.createElement('div');
    this.header.classList.add('modal-header');

    // Modal title element
    if (params.modalTitle) {
      this.title = document.createElement('h4');
      this.title.classList.add('modal-title');
      this.title.innerHTML = params.modalTitle;
    }

    // Modal close button icon
    this.closeIcon = document.createElement('button');
    this.closeIcon.classList.add('close');
    this.closeIcon.setAttribute('type', 'button');
    this.closeIcon.dataset.dismiss = 'modal';
    this.closeIcon.innerHTML = '×';

    // Modal body element
    this.body = document.createElement('div');
    this.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

    // Constructing the modal
    if (this.title) {
      this.header.appendChild(this.title);
    }
    this.header.appendChild(this.closeIcon);
    this.content.append(this.header, this.body);
    this.body.appendChild(this.message);
    this.dialog.appendChild(this.content);
    this.container.appendChild(this.dialog);
  }
}

/**
 * Modal component
 *
 * @param {InputModalParams} params
 * @param {Function} closeCallback
 */
export class Modal implements ModalType {
  modal!: ModalContainerType;

  protected $modal!: JQuery;

  constructor(
    inputParams: InputModalParams,
  ) {
    const params: ModalParams = {
      id: 'confirm-modal',
      closable: false,
      dialogStyle: {},
      ...inputParams,
    };

    this.initContainer(params);
  }

  protected initContainer(params: ModalParams): void {
    // Construct the modal, check if it already exists This allows child classes to use their custom container
    if (!this.modal) {
      this.modal = new ModalContainer(params);
    }

    // jQuery modal object
    this.$modal = $(this.modal.container);

    const {id, closable} = params;
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

      if (params.closeCallback) {
        params.closeCallback();
      }
    });

    document.body.appendChild(this.modal.container);
  }

  setTitle(modalTitle: string): void {
    if (!this.modal.title) {
      this.modal.title = document.createElement('h4');
      this.modal.title.classList.add('modal-title');
      if (this.modal.closeIcon) {
        this.modal.header.insertBefore(this.modal.title, this.modal.closeIcon);
      } else {
        this.modal.header.appendChild(this.modal.title);
      }
    }

    this.modal.title.innerHTML = modalTitle;
  }

  render(content: string): void {
    this.modal.message.innerHTML = content;
  }

  show(): void {
    this.$modal.modal('show');
  }

  hide(): void {
    this.$modal.modal('hide');
    // Sometimes modal animation is still in progress and hiding fails, so we attach event listener for that case.
    this.$modal.on('shown.bs.modal', () => {
      this.$modal.modal('hide');
      this.$modal.off('shown.bs.modal');
    });
  }
}

export default Modal;
