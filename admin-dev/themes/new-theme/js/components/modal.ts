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

/* eslint max-classes-per-file: ["error", 42] */

const {$} = window;

export interface ModalType {
  container: HTMLElement;
  dialog: HTMLElement;
  content: HTMLElement;
  header: HTMLElement;
  title: HTMLElement;
  closeIcon: HTMLButtonElement;
  body: HTMLElement;
  message: HTMLElement;
  footer: HTMLElement;
  closeButton: HTMLElement;
  confirmButton: HTMLButtonElement;
}

export interface ConfirmModalType {
  show: () => void;
  hide: () => void;
}

export interface ModalParams {
  id: string;
  confirmTitle?: string;
  confirmMessage?: string;
  closeButtonLabel?: string;
  confirmButtonLabel?: string;
  confirmButtonClass?: string;
  customButtons?: Array<HTMLButtonElement | HTMLAnchorElement>;
  closable?: boolean;
}

/**
 * This class us used as a base class and handles the building of the modal DOM elements
 * however it is not usable as is because it doesn't even have a show method so it is more
 * to be considered and "abstract" class that is used by other children implementations.
 *
 * @param {ModalParams} params
 *
 */
export class Modal {
  protected modal: ModalType;

  constructor({
    id = 'confirm-modal',
    confirmTitle,
    confirmMessage = '',
    closeButtonLabel = 'Close',
    confirmButtonLabel = 'Accept',
    confirmButtonClass = 'btn-primary',
    customButtons = [],
  }: ModalParams) {
    this.modal = <ModalType>{};

    // Main modal element
    this.modal.container = document.createElement('div');
    this.modal.container.classList.add('modal', 'fade');
    this.modal.container.id = id;

    // Modal dialog element
    this.modal.dialog = document.createElement('div');
    this.modal.dialog.classList.add('modal-dialog');

    // Modal content element
    this.modal.content = document.createElement('div');
    this.modal.content.classList.add('modal-content');

    // Modal header element
    this.modal.header = document.createElement('div');
    this.modal.header.classList.add('modal-header');

    // Modal title element
    if (confirmTitle) {
      this.modal.title = document.createElement('h4');
      this.modal.title.classList.add('modal-title');
      this.modal.title.innerHTML = confirmTitle;
    }

    // Modal close button icon
    this.modal.closeIcon = document.createElement('button');
    this.modal.closeIcon.classList.add('close');
    this.modal.closeIcon.setAttribute('type', 'button');
    this.modal.closeIcon.dataset.dismiss = 'modal';
    this.modal.closeIcon.innerHTML = '×';

    // Modal body element
    this.modal.body = document.createElement('div');
    this.modal.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

    // Modal message element
    this.modal.message = document.createElement('p');
    this.modal.message.classList.add('confirm-message');
    this.modal.message.innerHTML = confirmMessage;

    // Modal footer element
    this.modal.footer = document.createElement('div');
    this.modal.footer.classList.add('modal-footer');

    // Modal close button element
    this.modal.closeButton = document.createElement('button');
    this.modal.closeButton.setAttribute('type', 'button');
    this.modal.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
    this.modal.closeButton.dataset.dismiss = 'modal';
    this.modal.closeButton.innerHTML = closeButtonLabel;

    // Modal confirm button element
    this.modal.confirmButton = document.createElement('button');
    this.modal.confirmButton.setAttribute('type', 'button');
    this.modal.confirmButton.classList.add(
      'btn',
      confirmButtonClass || 'btn-primary',
      'btn-lg',
      'btn-confirm-submit',
    );
    this.modal.confirmButton.dataset.dismiss = 'modal';
    this.modal.confirmButton.innerHTML = confirmButtonLabel;

    // Constructing the modal
    if (confirmTitle) {
      this.modal.header.append(this.modal.title, this.modal.closeIcon);
    } else {
      this.modal.header.appendChild(this.modal.closeIcon);
    }

    this.modal.body.appendChild(this.modal.message);
    this.modal.footer.append(this.modal.closeButton, ...customButtons, this.modal.confirmButton);
    this.modal.content.append(this.modal.header, this.modal.body, this.modal.footer);
    this.modal.dialog.appendChild(this.modal.content);
    this.modal.container.appendChild(this.modal.dialog);
  }
}

/**
 * ConfirmModal component
 *
 * @param {String} id
 * @param {String} confirmTitle
 * @param {String} confirmMessage
 * @param {String} closeButtonLabel
 * @param {String} confirmButtonLabel
 * @param {String} confirmButtonClass
 * @param {Array} customButtons
 * @param {Boolean} closable
 * @param {Function} confirmCallback
 * @param {Function} cancelCallback
 *
 */
export class ConfirmModal extends Modal implements ConfirmModalType {
  protected $modal: JQuery;

  constructor(
    params: ModalParams,
    confirmCallback: (event: Event) => void,
    cancelCallback = () => true,
  ) {
    // Construct the modal
    super(params);

    const {id, closable} = params;

    // jQuery modal object
    this.$modal = $(this.modal.container);

    this.modal.confirmButton.addEventListener('click', confirmCallback);

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

      if (cancelCallback) {
        cancelCallback();
      }
    });

    document.body.appendChild(this.modal.container);
  }

  show(): void {
    this.$modal.modal('show');
  }

  hide(): void {
    this.$modal.modal('hide');
  }
}

export default ConfirmModal;
