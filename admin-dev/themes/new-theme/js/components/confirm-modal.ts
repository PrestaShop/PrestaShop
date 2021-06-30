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
} from '@components/modal-container';

const {$} = window;

export interface ConfirmModalContainerType extends ModalContainerType {
  message: HTMLElement;
  footer: HTMLElement;
  closeButton: HTMLElement;
  confirmButton: HTMLButtonElement;
}
export interface ConfirmModalType extends ModalType {
  modal: ConfirmModalContainerType;
}
export type ConfirmModalParams = ModalParams & {
  confirmTitle?: string;
  confirmMessage: string;
  closeButtonLabel: string;
  confirmButtonLabel: string;
  confirmButtonClass: string;
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
}
export type InputConfirmModalParams = Partial<ConfirmModalParams>;

/**
 * This class is used to build the modal DOM elements, it is not usable as is because it doesn't even have a show
 * method and the elements are created but not added to the DOM. It just creates a basic DOM structure of a
 * Bootstrap modal, thus keeping the logic class of the modal separated.
 *
 * This container is built on the basic ModalContainer and adds some confirm/cancel buttons along with a message
 * in the body, it is mostly used as a Rich confirm dialog box.
 *
 * @param {InputConfirmModalParams} inputParams
 */
class ConfirmModalContainer extends ModalContainer implements ConfirmModalContainerType {
  message!: HTMLElement;

  footer!: HTMLElement;

  closeButton!: HTMLElement;

  confirmButton!: HTMLButtonElement;

  constructor(inputParams: InputConfirmModalParams) {
    const params: ConfirmModalParams = {
      id: 'confirm-modal',
      confirmMessage: 'Are you sure?',
      closeButtonLabel: 'Close',
      confirmButtonLabel: 'Accept',
      confirmButtonClass: 'btn-primary',
      customButtons: [],
      closable: false,
      modalTitle: inputParams.confirmTitle,
      dialogStyle: {},
      ...inputParams,
    };

    super(params);
  }

  buildModalContainer(params: ConfirmModalParams) {
    super.buildModalContainer(params);

    // Modal message element
    this.message = document.createElement('p');
    this.message.classList.add('confirm-message');
    this.message.innerHTML = params.confirmMessage;

    // Modal footer element
    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');

    // Modal close button element
    this.closeButton = document.createElement('button');
    this.closeButton.setAttribute('type', 'button');
    this.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
    this.closeButton.dataset.dismiss = 'modal';
    this.closeButton.innerHTML = params.closeButtonLabel;

    // Modal confirm button element
    this.confirmButton = document.createElement('button');
    this.confirmButton.setAttribute('type', 'button');
    this.confirmButton.classList.add(
      'btn',
      params.confirmButtonClass,
      'btn-lg',
      'btn-confirm-submit',
    );
    this.confirmButton.dataset.dismiss = 'modal';
    this.confirmButton.innerHTML = params.confirmButtonLabel;

    // Appending element to the modal
    this.body.appendChild(this.message);
    this.footer.append(this.closeButton, ...params.customButtons, this.confirmButton);
    this.content.append(this.footer);
  }
}

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 * @param {Function} confirmCallback
 * @param {Function} cancelCallback
 */
export class ConfirmModal implements ConfirmModalType {
  modal: ConfirmModalContainerType;

  protected $modal: JQuery;

  constructor(
    params: InputConfirmModalParams,
    confirmCallback: (event: Event) => void,
    cancelCallback = () => true,
  ) {
    // Construct the modal
    this.modal = new ConfirmModalContainer(params);

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
