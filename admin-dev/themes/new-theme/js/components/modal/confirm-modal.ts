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
  ModalContainerType, ModalContainer, ModalType, ModalParams, Modal,
} from '@components/modal/modal';
import {isUndefined} from '@PSTypes/typeguard';

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
  confirmCallback: (event: Event) => void,
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
 */
export class ConfirmModalContainer extends ModalContainer implements ConfirmModalContainerType {
  footer!: HTMLElement;

  closeButton!: HTMLElement;

  confirmButton!: HTMLButtonElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ConfirmModalParams) {
    super(params);
  }

  protected buildModalContainer(params: ConfirmModalParams): void {
    super.buildModalContainer(params);

    // Modal message element
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
    this.footer.append(this.closeButton, ...params.customButtons, this.confirmButton);
    this.content.append(this.footer);
  }
}

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 * @param {Function} confirmCallback @deprecated You should rely on the confirmCallback param
 * @param {Function} cancelCallback @deprecated You should rely on the closeCallback param
 */
export class ConfirmModal extends Modal implements ConfirmModalType {
  modal!: ConfirmModalContainerType;

  constructor(
    inputParams: InputConfirmModalParams,
    confirmCallback?: (event: Event) => void,
    cancelCallback?: () => void,
  ) {
    let confirmModalCallback: (event: Event) => void;

    if (!isUndefined(inputParams.confirmCallback)) {
      confirmModalCallback = inputParams.confirmCallback;
    } else if (!isUndefined(confirmCallback)) {
      confirmModalCallback = confirmCallback;
    } else {
      // We kept the parameters for backward compatibility, this forces us to keep the param confirmCallback as optional
      // but when we remove deprecation it will become mandatory, a confirm callback should always be specified
      confirmModalCallback = (): void => {
        console.error('No confirm callback provided for ConfirmModal component.');
      };
    }

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
      confirmCallback: confirmModalCallback,
      closeCallback: inputParams.closeCallback ?? cancelCallback,
      ...inputParams,
    };

    super(params);
  }

  protected initContainer(params: ConfirmModalParams): void {
    this.modal = new ConfirmModalContainer(params);
    this.modal.confirmButton.addEventListener('click', params.confirmCallback);
    super.initContainer(params);
  }
}

export default ConfirmModal;
