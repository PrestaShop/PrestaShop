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

export interface ProgressModalContainerType extends ModalContainerType {
  message: HTMLElement;
  footer: HTMLElement;
  progress: HTMLElement;
  progressInfo: HTMLElement;
  progressSuccess: HTMLElement;
  progressFailure: HTMLElement;
  progressNext: HTMLElement;
  closeButton: HTMLElement;
}
export interface ProgressModalType extends ModalType {
  modal: ProgressModalContainerType;
}
export type ProgressModalParams = ModalParams & {
  modalTitle: string;
  total: number;
  completed: number;
  confirmCallback: (event: Event) => void,
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
}

export type ProgressErrorModalParams = ModalParams & {
  errors: Array<string>;
}
export type InputProgressModalParams = Partial<ProgressModalParams>;

/**
 * This class is used to build the modal DOM elements, it is not usable as is because it doesn't even have a show
 * method and the elements are created but not added to the DOM. It just creates a basic DOM structure of a
 * Bootstrap modal, thus keeping the logic class of the modal separated.
 *
 * This container is built on the basic ModalContainer and adds some confirm/cancel buttons along with a message
 * in the body, it is mostly used as a Rich confirm dialog box.
 */
export class ProgressModalContainer extends ModalContainer implements ProgressModalContainerType {
  footer!: HTMLElement;

  progress!: HTMLElement;

  progressInfo!: HTMLElement;

  progressSuccess!: HTMLElement;

  progressFailure!: HTMLElement;

  progressNext!: HTMLElement;

  closeButton!: HTMLElement;


  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    super(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    params.modalTitle = params.modalTitle.replace('%d', params.total.toString());
    super.buildModalContainer(params);
    let progressBarDone = params.completed * 100 / params.total;
    this.buildProgressBar(progressBarDone);

    let progressDetails = document.createElement('div');
    progressDetails.classList.add(
      'float-right',
      'progress-percent',
    );
    progressDetails.append(progressBarDone + '%');

    this.body.append(progressDetails);

    this.body.append(this.progress);

    let switchButtonContainer = document.createElement('div');
    switchButtonContainer.classList.add(
      'switch-to-errors-button',
      'btn',
      'btn-primary'
    );
    switchButtonContainer.innerHTML = 'Errors';
    this.body.append(switchButtonContainer);


    let errorContainer = document.createElement('div');
    errorContainer.classList.add('modal-error-container-single');
    this.body.append(errorContainer);
    // Modal footer element
    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');

    // Modal close button element
    this.closeButton = document.createElement('button');
    this.closeButton.setAttribute('type', 'button');
    this.closeButton.setAttribute('disabled', 'true');
    this.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
    this.closeButton.dataset.dismiss = 'modal';
    this.closeButton.innerHTML = 'Close';

    // Appending element to the modal
    this.footer.append(this.closeButton, ...params.customButtons);
    this.content.append(this.footer);
  }

  buildProgressBar(completed: number)
  {
    this.progress = document.createElement('div');
    this.progress.setAttribute('style', 'display: block; width: 100%');

    this.progress.classList.add
    (
      'progress',
      'active',
    );
    let progressBar = document.createElement('div');
    progressBar.classList.add('progress-bar', 'progress-bar-success');
    progressBar.setAttribute('role', 'progressbar');
    progressBar.setAttribute('style', 'width: ' + completed+ '%;');

    let progressDone = document.createElement('div');
    progressDone.setAttribute('style', 'width: ' + completed +  '%');

    progressDone.classList.add
    (
      'progress-bar',
      'progress-bar-success',
    );
    progressDone.setAttribute('role', 'progressbar');
    progressDone.setAttribute('id', 'modal_progressbar_done');
    this.progress.append(progressDone);
  }

  buildModalErrorContainer(params: ProgressErrorModalParams): void {
    super.buildModalContainer(params);
    this.container.classList.remove('fade');

    let errorContainer = document.createElement('div');
    errorContainer.classList.add('progress-modal-error-container');
    errorContainer.classList.add
    (
      'alert',
      'alert-warning',
      'd-print-none'
    );
    this.body.append(errorContainer);

    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');
    let switchButtonContainer = document.createElement('div');
    switchButtonContainer.classList.add(
      'switch-to-progress-button',
      'btn',
      'btn-secondary'
    );
    switchButtonContainer.innerHTML = 'Back to processing';

    let downloadButtonContainer = document.createElement('div');
    downloadButtonContainer.classList.add(
      'download-error-log',
      'btn',
      'btn-secondary'
    );
    downloadButtonContainer.innerHTML = 'Download error log';
    this.footer.append(switchButtonContainer);
    this.footer.append(downloadButtonContainer);
    this.body.append(this.footer);
  }
}

export class ProgressModalErrorContainer extends ModalContainer implements ModalContainerType {
  footer!: HTMLElement;
  closeButton!: HTMLElement;
  errors!: Array<string>;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressErrorModalParams) {
    super(params);
  }

  buildModalContainer(params: ProgressErrorModalParams): void {
    super.buildModalContainer(params);
    this.container.classList.remove('fade');

    let errorContainer = document.createElement('div');
    errorContainer.classList.add('progress-modal-error-container');
    params.errors.forEach(function (error) {
      let errorContent = document.createElement('p');
      errorContent.append(error);
      errorContainer.append(errorContent);
    });
    errorContainer.classList.add
    (
      'alert',
      'alert-warning',
      'd-print-none'
    );
    this.body.append(errorContainer);

    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');
    let switchButtonContainer = document.createElement('div');
    switchButtonContainer.classList.add(
      'switch-to-progress-button',
      'btn',
      'btn-secondary'
    );
    switchButtonContainer.innerHTML = 'Back to processing';

    let downloadButtonContainer = document.createElement('div');
    downloadButtonContainer.classList.add(
      'download-error-log',
      'btn',
      'btn-secondary'
    );
    downloadButtonContainer.innerHTML = 'Download error log';
    this.footer.append(switchButtonContainer);
    this.footer.append(downloadButtonContainer);
    this.body.append(this.footer);
  }
}

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 * @param {Function} confirmCallback @deprecated You should rely on the confirmCallback param
 * @param {Function} cancelCallback @deprecated You should rely on the closeCallback param
 */
export class ProgressModal extends Modal implements ProgressModalType {
  modal!: ProgressModalContainerType;
  doneCount !: number;
  total !: number;
  errors !: Array<string>;
  currentModal !: string;

  constructor(
    inputParams: InputProgressModalParams,
    total: number,
    confirmCallback: (event: Event) => void,
    cancelCallback = () => true,
  ) {
    const params: ProgressModalParams = {
      id: 'progress-modal',
      customButtons: [],
      closable: false,
      modalTitle: "Progress action",
      total: total,
      completed: 0,
      dialogStyle: {},
      confirmCallback,
      closeCallback: cancelCallback,
      ...inputParams,
    };

    super(params);
    this.total = total;
    this.doneCount = 0;
    this.errors = [];
    this.currentModal = 'progress';
  }

  protected initContainer(params: ProgressModalParams): void {
    let modal = this;
    this.modal = new ProgressModalContainer(params);

    super.initContainer(params);

    const switchToProgressButton = document.querySelector('.switch-to-progress-button') as HTMLElement;
    switchToProgressButton.addEventListener('click', () => {
      modal.currentModal = 'progress';
      params.completed = modal.doneCount;
      let container = new ProgressModalContainer(params);
      let progressModal = document.querySelector('#progress-modal .modal-content') as HTMLElement;
      progressModal.innerHTML = container.content.innerHTML;
    });

    const downloadErrorLogButton = document.querySelector('.download-error-log') as HTMLElement;
    downloadErrorLogButton.addEventListener('click', () => {
      let csvContent = 'data:text/csv;charset=utf-8,';
      modal.errors.forEach(function(error) {
        csvContent += error + "\r\n";
      });
      let link = document.createElement('a');
      link.href = encodeURI(csvContent);
      link.download = 'Errors.csv';
      link.click();
    });

    const switchToErrorsButton = document.querySelector('.switch-to-errors-button') as HTMLElement;
    switchToErrorsButton.addEventListener('click', () => {
      modal.currentModal = 'error';

      let params: ProgressErrorModalParams = {
        id: 'progress-modal',
        errors: modal.errors,
        closable: false,
        modalTitle: "Progress action",
      };

      let container = new ProgressModalErrorContainer(params);
      let progressModal = document.querySelector('#progress-modal .modal-content') as HTMLElement;
      progressModal.innerHTML = container.content.innerHTML;
    });
  }

  public modalActionSuccess()
  {
    this.doneCount++;
    if (this.currentModal == 'progress') {
      let progressBarDone = this.doneCount * 100 / this.total;
      $('#modal_progressbar_done').width(progressBarDone+'%');
      $('.progress-percent').text(progressBarDone+'%');
    }

  }

  public modalActionError(error: string)
  {
    this.doneCount++;
    this.errors.push(error);
    if (this.currentModal == 'progress') {
      let progressBarDone = this.doneCount * 100 / this.total;
      $('#modal_progressbar_done').width(progressBarDone + '%');
      $('.progress-percent').text(progressBarDone + '%');
    }

    if (this.currentModal == 'error') {
      let errorContent = document.createElement('p');
      errorContent.append(error);
      $('.progress-modal-error-container').append(errorContent);
    }
  }
}

export default ProgressModal;
