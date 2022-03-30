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

export interface ProgressModalParentContainerType extends ModalContainerType {
}
export interface ProgressModalType extends ModalType {
  modal: ProgressModalParentContainerType;
}
export type ProgressModalParams = ModalParams & {
  modalTitle: string;
  total: number;
  confirmCallback: (event: Event) => void,
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
}
export type InputProgressModalParams = Partial<ProgressModalParams>;

export class ProgressModalParentContainer extends ModalContainer implements ProgressModalParentContainerType {
  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    super(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    super.buildModalContainer(params);

    let progressContainer = document.createElement('div');
    progressContainer.id = 'progress-modal-container';
    this.dialog.append(progressContainer)

    let errorContainer = document.createElement('div');
    errorContainer.id = 'error-modal-container';
    this.dialog.append(errorContainer)
  }
}

export class ProgressModalContainer extends ModalContainer implements ProgressModalParentContainerType {
  footer!: HTMLElement;
  closeButton!: HTMLElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    super(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    params.modalTitle = params.modalTitle.replace('%d', params.total.toString());
    super.buildModalContainer(params);

    let progressDetails = document.createElement('div');
    progressDetails.classList.add(
      'float-right',
      'progress-percent',
    );
    progressDetails.append('0%');

    this.body.append(progressDetails);
    this.body.append(this.buildProgressBar());

    let switchButtonContainer = document.createElement('div');
    switchButtonContainer.classList.add(
      'switch-to-errors-button',
      'alert',
      'alert-warning'
    );

    let errorCountSpan = document.createElement('span');
    errorCountSpan.classList.add(
      'modal-error-count'
    )
    errorCountSpan.innerHTML = '0';

    let errorLogString = 'View %d error logs';
    switchButtonContainer.innerHTML = errorLogString.replace('%d', errorCountSpan.outerHTML);


    this.header.insertBefore(switchButtonContainer, this.closeIcon);

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

  private buildProgressBar(): HTMLElement
  {
    let progress = document.createElement('div');
    progress.setAttribute('style', 'display: block; width: 100%');

    progress.classList.add
    (
      'progress',
      'active',
    );
    let progressBar = document.createElement('div');
    progressBar.classList.add('progress-bar', 'progress-bar-success');
    progressBar.setAttribute('role', 'progressbar');
    progressBar.setAttribute('style', 'width: 0%;');

    let progressDone = document.createElement('div');
    progressDone.setAttribute('style', 'width: 0%');

    progressDone.classList.add
    (
      'progress-bar',
      'progress-bar-success',
    );
    progressDone.setAttribute('role', 'progressbar');
    progressDone.setAttribute('id', 'modal_progressbar_done');
    progress.append(progressDone);

    return progress;
  }
}
export class ProgressModalErrorContainer extends ModalContainer implements ModalContainerType {
  footer!: HTMLElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    super(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    super.buildModalContainer(params);

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

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 * @param {Function} confirmCallback @deprecated You should rely on the confirmCallback param
 * @param {Function} cancelCallback @deprecated You should rely on the closeCallback param
 */
export class ProgressModal extends Modal implements ProgressModalType {
  modal!: ProgressModalParentContainerType;
  doneCount !: number;
  total !: number;
  errors !: Array<string>;
  progressModalElement !: HTMLElement;
  errorModalElement !: HTMLElement;
  progressDoneElement !: HTMLElement;
  progressPercentElement !: HTMLElement;
  errorElement !: HTMLElement;

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
      dialogStyle: {},
      confirmCallback,
      closeCallback: cancelCallback,
      ...inputParams,
    };

    super(params);
    this.total = total;
    this.errors = [];
  }

  protected initContainer(params: ProgressModalParams): void {
    /** I still need to init those things in order for them to appear. How do I do that? */
    this.modal = new ProgressModalParentContainer(params);
    super.initContainer(params);
    this.initModalContainers(params);
    this.initDynamicContentVariables();
    this.initListeners();
  }

  public modalActionSuccess(doneCount: number)
  {
    this.updatePercentage(doneCount);
  }

  public modalActionError(doneCount: number, error: string)
  {
    this.updatePercentage(doneCount);
    this.addError(error);
  }

  private updatePercentage(doneCount: number)
  {
    let progressBarDone = doneCount * 100 / this.total;
    this.progressDoneElement.style.width = progressBarDone+'%';
    this.progressPercentElement.innerHTML = progressBarDone+'%';
  }

  private addError(error: string)
  {
    this.errors.push(error);
    let errorContent = document.createElement('p');
    errorContent.append(error);
    $('.progress-modal-error-container').append(errorContent);
  }

  private initModalContainers(params: ProgressModalParams)
  {
    let progressModal = new ProgressModalContainer(params);
    let errorModal = new ProgressModalErrorContainer(params);
    this.progressModalElement = document.getElementById('progress-modal-container') as HTMLElement;
    this.progressModalElement.innerHTML = progressModal.container.innerHTML;

    this.errorModalElement = document.getElementById('error-modal-container') as HTMLElement;
    this.errorModalElement.innerHTML = errorModal.container.innerHTML;
    this.errorModalElement.hidden = true;
  }

  private initListeners()
  {
    const switchToErrorsButton = document.querySelector('.switch-to-errors-button') as HTMLElement;
    switchToErrorsButton.addEventListener('click', () => {
      this.progressModalElement.hidden = true;
      this.errorModalElement.hidden = false;
    });

    const downloadErrorLogButton = document.querySelector('.download-error-log') as HTMLElement;
    downloadErrorLogButton.addEventListener('click', () => {
      let csvContent = 'data:text/csv;charset=utf-8,';
      this.errors.forEach(function(error) {
        csvContent += error + "\r\n";
      });
      let link = document.createElement('a');
      link.href = encodeURI(csvContent);
      link.download = 'Errors.csv';
      link.click();
    });

    const switchButtonContainer = document.querySelector('.switch-to-progress-button') as HTMLElement;
    switchButtonContainer.addEventListener('click', () => {
      this.progressModalElement.hidden = false;
      this.errorModalElement.hidden = true;
    });
  }

  /** Initializes variables for dynamic content that can be altered */
  private initDynamicContentVariables()
  {
    this.progressDoneElement = document.getElementById('modal_progressbar_done') as HTMLElement;
    this.progressPercentElement = document.querySelector('.progress-percent') as HTMLElement;
    this.errorElement = document.querySelector('.progress-modal-error-container') as HTMLElement;
  }
}

export default ProgressModal;
