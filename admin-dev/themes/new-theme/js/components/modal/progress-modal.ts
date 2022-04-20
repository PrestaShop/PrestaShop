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
  modalDescription: string;
  closeButtonLabel: string;
  modalTitle: string;
  modalProgressTitle: string;
  modalFailureTitle: string;
  confirmCallback: (event: Event) => void,
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
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

  successCount = 0;

  failCount = 0;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    super(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    super.buildModalContainer(params);

    this.buildProgress(params.modalTitle, params.modalProgressTitle, params.modalFailureTitle);

    let progressDetails = document.createElement('div');
    progressDetails.classList.add(
      'float-right',
      'progress-details-text',
    );

    this.body.append(progressDetails);

    this.body.append(this.progress);

    let errorContainer = document.createElement('div');
    errorContainer.classList.add('modal-error-container');
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
    this.closeButton.innerHTML = params.closeButtonLabel;

    // Appending element to the modal
    this.footer.append(this.closeButton, ...params.customButtons);
    this.content.append(this.footer);
  }

  buildProgress(modalTitle: string, modalProgressTitle: string, modalFailureTitle: string)
  {
    this.buildProgressBar();
    this.buildProgressInfo(modalTitle);
    this.buildProgressSuccess(modalProgressTitle);
    this.buildProgressError(modalFailureTitle);

    this.progress.append(this.progressSuccess);
    this.progress.append(this.progressFailure);
    this.progress.append(this.progressInfo);
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

  buildProgressSuccess(modalProgressName: string)
  {
    this.progressSuccess = document.createElement('div');
    this.progressSuccess.setAttribute('style', 'width: 0%');

    this.progressSuccess.classList.add
    (
      'progress-bar',
      'progress-bar-success',
    );
    this.progressSuccess.setAttribute('role', 'progressbar');
    this.progressSuccess.setAttribute('id', 'modal_progressbar_info')

    let successSpan = document.createElement('span');
    let successCountSpan = document.createElement('span');
    successCountSpan.classList.add(
      'progress-success-count'
    );
    successSpan.append(modalProgressName + ' ');
    successSpan.append(successCountSpan);

    this.progressSuccess.append(successSpan);
  }

  buildProgressError(modalFailureName: string)
  {

    this.progressFailure = document.createElement('div');
    this.progressFailure.setAttribute('style', 'width: 0%');

    this.progressFailure.classList.add
    (
      'progress-bar',
      'progress-bar-danger',
    );
    this.progressFailure.setAttribute('role', 'progressbar');
    this.progressFailure.setAttribute('id', 'modal;_progressbar_failure')

    let failureSpan = document.createElement('span');
    let failureCountSpan = document.createElement('span');
    failureCountSpan.classList.add(
      'progress-failure-count'
    );
    failureSpan.append(modalFailureName + ' ');
    failureSpan.append(failureCountSpan);
    this.progressFailure.append(failureSpan);
  }

  buildProgressInfo(actionName: string)
  {
    this.progressInfo = document.createElement('div');
    this.progressInfo.setAttribute('style', 'width: 0%');

    this.progressInfo.classList.add
    (
      'progress-bar',
      'progress-bar-info',
    );
    this.progressInfo.setAttribute('role', 'progressbar');
    this.progressInfo.setAttribute('id', 'modal_progressbar_done')

    let infoSpan = document.createElement('span');
    infoSpan.append(actionName);

    this.progressInfo.append(infoSpan);
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
  successCount !: number;
  errorCount !: number;
  totalDoneCount !: number;
  total !: number;

  constructor(
    inputParams: InputProgressModalParams,
    total: number,
    confirmCallback: (event: Event) => void,
    cancelCallback = () => true,
  ) {
    const params: ProgressModalParams = {
      id: 'confirm-modal',
      modalDescription: 'Description',
      closeButtonLabel: 'Close',
      customButtons: [],
      closable: false,
      modalTitle: "Progress action",
      modalProgressTitle: "Processing..",
      modalFailureTitle: "Failed to process",
      dialogStyle: {},
      confirmCallback,
      closeCallback: cancelCallback,
      ...inputParams,
    };

    super(params);
    this.total = total;
    this.successCount = 0;
    this.errorCount = 0;
    this.totalDoneCount = 0;
  }

  protected initContainer(params: ProgressModalParams): void {
    this.modal = new ProgressModalContainer(params);
    super.initContainer(params);
  }

  public addProgressDetail(id: number)
  {
    $('.progress-details-text').text('Activating: #' + id);
  }

  public modalActionSuccess()
  {
    this.successCount++;
    this.totalDoneCount++;
    let progressBarSuccess = this.successCount * 100 / this.total;
    $('#modal_progressbar_info').width(progressBarSuccess+'%');
    $('.progress-success-count').html(this.successCount + '/' + this.total);
    let progressionDone = this.totalDoneCount * 100 / this.total;
    $('#modal_progressbar_done').width((100-progressionDone)+'%');
  }

  public modalActionError(error: string)
  {
    this.errorCount++;
    this.totalDoneCount++;
    let progressionError = this.errorCount * 100 / this.total;
    $('#modal_progressbar_failure').width(progressionError+'%');
    $('.progress-failure-count').html(this.errorCount + '/' + this.total);
    let progressionDone = this.totalDoneCount * 100 / this.total;
    $('#modal_progressbar_done').width((100-progressionDone)+'%');


    let errorContainer = document.createElement('div');
    errorContainer.classList.add
    (
      'alert',
      'alert-danger',
      'd-print-none'
    );
    let errorTextContainer = document.createElement('div');
    let errorContent = document.createElement('p');
    errorContent.append(error);
    errorTextContainer.classList.add
    (
      'text-danger',
    );
    errorTextContainer.append(errorContent);
    errorContainer.append(errorTextContainer);
    $('.modal-error-container').append(errorContainer);

  }
}

export default ProgressModal;
