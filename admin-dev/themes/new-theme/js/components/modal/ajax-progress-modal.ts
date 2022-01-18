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

export interface AjaxProgressModalContainerType extends ModalContainerType {
  message: HTMLElement;
  footer: HTMLElement;
  progress: HTMLElement;
  progressInfo: HTMLElement;
  progressSuccess: HTMLElement;
  progressNext: HTMLElement;
  closeButton: HTMLElement;
  confirmButton: HTMLButtonElement;
}
export interface AjaxProgressModalType extends ModalType {
  modal: AjaxProgressModalContainerType;
}
export type AjaxProgressModalParams = ModalParams & {
  confirmMessage: string;
  closeButtonLabel: string;
  confirmButtonLabel: string;
  confirmButtonClass: string;
  modalTitle: string;
  modalProgressTitle: string
  confirmCallback: (event: Event) => void,
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
}
export type InputAjaxProgressModalParams = Partial<AjaxProgressModalParams>;

/**
 * This class is used to build the modal DOM elements, it is not usable as is because it doesn't even have a show
 * method and the elements are created but not added to the DOM. It just creates a basic DOM structure of a
 * Bootstrap modal, thus keeping the logic class of the modal separated.
 *
 * This container is built on the basic ModalContainer and adds some confirm/cancel buttons along with a message
 * in the body, it is mostly used as a Rich confirm dialog box.
 */
export class AjaxProgressModalContainer extends ModalContainer implements AjaxProgressModalContainerType {
  footer!: HTMLElement;

  progress!: HTMLElement;

  progressInfo!: HTMLElement;

  progressSuccess!: HTMLElement;

  progressNext!: HTMLElement;

  closeButton!: HTMLElement;

  confirmButton!: HTMLButtonElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: AjaxProgressModalParams) {
    super(params);
  }

  buildModalContainer(params: AjaxProgressModalParams): void {
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
    if (params.confirmButtonClass) {
      this.confirmButton.classList.add(
        'btn',
        params.confirmButtonClass,
        'btn-lg',
        'btn-confirm-submit',
      );
    } else {
      this.confirmButton.classList.add(
        'btn',
        'btn-lg',
        'btn-confirm-submit',
      );
    }

    this.confirmButton.dataset.dismiss = 'modal';
    this.confirmButton.innerHTML = params.confirmButtonLabel;

    // Appending element to the modal
    this.footer.append(this.closeButton, ...params.customButtons, this.confirmButton);

    this.buildProgressBar();
    this.buildProgressInfo(params.modalTitle, params.modalProgressTitle);
    this.progress.append(this.progressSuccess);
    this.progress.append(this.progressInfo);
    //this.progress.append(this.progressNext);
    this.content.append(this.progress);


    this.content.append(this.footer);
  }

  buildProgressBar()
  {

    this.progress = document.createElement('div');
    this.progress.setAttribute('style', 'display: block; width: 100%');

    this.progress.classList.add
    (
      'progress',
      'active',
      'progress-striped'
    );
    let progressBar = document.createElement('div');
    progressBar.classList.add('progress-bar', 'progress-bar-success');
    progressBar.setAttribute('role', 'progressbar');
    progressBar.setAttribute('style', 'width: 0%;');
    let span = document.createElement('span');
    span.append('0%');

    progressBar.append(span);
    this.progress.append(progressBar);
  }

  buildProgressInfo(actionName: string, modalProgressName: string)
  {
    this.progressInfo = document.createElement('div');
    this.progressInfo.setAttribute('style', 'width: 0%');

    this.progressInfo.classList.add
    (
      'progress-bar',
      'progress-bar-info',
    );
    this.progressInfo.setAttribute('role', 'progressbar');
    this.progressInfo.setAttribute('id', 'ajax_progressbar_done')

    let infoSpan = document.createElement('span');
    infoSpan.append(actionName);

    this.progressInfo.append(infoSpan);

    this.progressSuccess = document.createElement('div');
    this.progressSuccess.setAttribute('style', 'width: 0%');

    this.progressSuccess.classList.add
    (
      'progress-bar',
      'progress-bar-success',
    );
    this.progressSuccess.setAttribute('role', 'progressbar');
    this.progressSuccess.setAttribute('id', 'ajax_progressbar_info')

    let successSpan = document.createElement('span');
    let successCountSpan = document.createElement('span');
    successCountSpan.classList.add(
      'progress-success-count'
    );
    successSpan.append(modalProgressName + ' ');
    successSpan.append(successCountSpan);

    this.progressSuccess.append(successSpan);
    // this.progressNext = document.createElement('div');
    // this.progressNext.setAttribute('style', 'width: 0%; opacity: 0.5;');

    // this.progressNext.classList.add
    // (
    //   'progress-bar',
    //   'progress-bar-success',
    //   'progress-bar-stripes',
    //   'active'
    // );
    // this.progressNext.setAttribute('role', 'progressbar');
    // this.progressNext.setAttribute('id', 'ajax_progressbar_next')
    //
    // let nextSpan = document.createElement('span');
    // nextSpan.append('Next');
    //
    // this.progressNext.append(nextSpan);
  }

}

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 * @param {Function} confirmCallback @deprecated You should rely on the confirmCallback param
 * @param {Function} cancelCallback @deprecated You should rely on the closeCallback param
 */
export class AjaxProgressModal extends Modal implements AjaxProgressModalType {
  modal!: AjaxProgressModalContainerType;

  constructor(
    inputParams: InputAjaxProgressModalParams,
    confirmCallback: (event: Event) => void,
    cancelCallback = () => true,
  ) {
    const params: AjaxProgressModalParams = {
      id: 'confirm-modal',
      confirmMessage: 'Are you sure?',
      closeButtonLabel: 'Close',
      confirmButtonLabel: 'Accept',
      confirmButtonClass: 'btn-primary',
      customButtons: [],
      closable: false,
      modalTitle: "Ajax action",
      modalProgressTitle: "Processing..",
      dialogStyle: {},
      confirmCallback,
      closeCallback: cancelCallback,
      ...inputParams,
    };

    super(params);
  }

  protected initContainer(params: AjaxProgressModalParams): void {
    this.modal = new AjaxProgressModalContainer(params);
    this.modal.confirmButton.addEventListener('click', params.confirmCallback);
    super.initContainer(params);
  }
}

export default AjaxProgressModal;
