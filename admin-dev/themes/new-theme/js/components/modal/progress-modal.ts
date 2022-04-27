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
  ModalContainerType, ModalContainer, ModalType, ModalParams, Modal, InputModalParams,
} from '@components/modal/modal';

export interface ProgressModalParentContainerType extends ModalContainerType {
}
export interface ProgressModalType extends ModalType {
  modal: ProgressModalParentContainerType;
}
export type ProgressModalParams = ModalParams & {
  modalTitle: string;
  total: number;
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
  cancelCallback?: () => void;
}
export type InputProgressModalParams = Partial<ProgressModalParams> & {
  modalTitle: string;
};

export class ProgressModalParentContainer implements ProgressModalParentContainerType{
  container!: HTMLElement;

  dialog!: HTMLElement;

  content!: HTMLElement;

  body!: HTMLElement;

  closeIcon!: HTMLButtonElement;

  header!: HTMLElement;

  message!: HTMLElement;

  constructor(params: ProgressModalParams) {
    this.buildModalContainer(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
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

    this.container.appendChild(this.dialog);
  }
}

export class ProgressModalContainer {
  footer!: HTMLElement;

  container!: HTMLElement;

  dialog!: HTMLElement;

  content!: HTMLElement;

  message!: HTMLElement;

  header!: HTMLElement;

  title?: HTMLElement;

  body!: HTMLElement;

  stopProcessingButton!: HTMLElement;

  switchToErrorButton!: HTMLElement;

  progressDone!: HTMLElement;

  progressPercentage!: HTMLElement;

  errorCount!: HTMLElement;

  constructor(params: ProgressModalParams) {
    this.buildModalContainer(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    // Modal content element
    this.content = document.createElement('div');
    this.content.classList.add('modal-content');

    // Modal message element
    this.message = document.createElement('p');
    this.message.classList.add('modal-message');

    // Modal header element
    this.header = document.createElement('div');
    this.header.classList.add('modal-header');

    params.modalTitle = params.modalTitle.replace('%d', params.total.toString());

    this.progressPercentage = document.createElement('div');
    this.progressPercentage.classList.add(
      'float-right',
      'progress-percent',
    );
    this.progressPercentage.append('0%');

    this.body = document.createElement('div');
    this.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

    this.body.append(this.progressPercentage);
    this.body.append(this.buildProgressBar());

    this.switchToErrorButton = document.createElement('div');
    this.switchToErrorButton.classList.add(
      'switch-to-errors-button',
      'alert',
      'alert-warning'
    );

    this.errorCount = document.createElement('span');
    this.errorCount.classList.add(
      'modal-error-count'
    )
    this.errorCount.innerHTML = '0';

    let errorLogString = 'View %d error logs';
    this.switchToErrorButton.innerHTML = errorLogString.replace('%d', this.errorCount.outerHTML);

    this.header.append(this.switchToErrorButton);

    let errorContainer = document.createElement('div');
    errorContainer.classList.add('modal-error-container-single');
    this.body.append(errorContainer);
    // Modal footer element
    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');

    this.stopProcessingButton = document.createElement('button');
    this.stopProcessingButton.setAttribute('type', 'button');
    this.stopProcessingButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg', 'stop-processing');
    this.stopProcessingButton.innerHTML = 'Stop processing';

    // Appending element to the modal
    this.footer.append(this.stopProcessingButton, ...params.customButtons);
    this.content.append(this.footer);


    // Modal title element
    if (params.modalTitle) {
      this.title = document.createElement('h4');
      this.title.classList.add('modal-title');
      this.title.innerHTML = params.modalTitle;
    }

    // Constructing the modal
    if (this.title) {
      this.header.appendChild(this.title);
    }
    this.content.append(this.header, this.body);
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

    this.progressDone = document.createElement('div');
    this.progressDone.setAttribute('style', 'width: 0%');

    this.progressDone.classList.add
    (
      'progress-bar',
      'progress-bar-success',
    );
    this.progressDone.setAttribute('role', 'progressbar');
    this.progressDone.setAttribute('id', 'modal_progressbar_done');
    progress.append(this.progressDone);

    return progress;
  }
}
export class ProgressModalErrorContainer {
  footer!: HTMLElement;

  container!: HTMLElement;

  dialog!: HTMLElement;

  content!: HTMLElement;

  message!: HTMLElement;

  header!: HTMLElement;

  title?: HTMLElement;

  body!: HTMLElement;

  switchButton!: HTMLElement;

  downloadErrorsButton!: HTMLElement;

  errorContainer!: HTMLElement;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    this.buildModalContainer(params);
  }

  buildModalContainer(params: ProgressModalParams): void {
    this.content = document.createElement('div');
    this.content.classList.add('modal-content');

    // Modal message element
    this.message = document.createElement('p');
    this.message.classList.add('modal-message');

    // Modal header element
    this.header = document.createElement('div');
    this.header.classList.add('modal-header');
    this.body = document.createElement('div');

    this.errorContainer = document.createElement('div');
    this.errorContainer.classList.add('progress-modal-error-container');
    this.errorContainer.classList.add
    (
      'alert',
      'alert-warning',
      'd-print-none'
    );
    this.body.append(this.errorContainer);

    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');
    this.switchButton = document.createElement('div');
    this.switchButton.classList.add(
      'switch-to-progress-button',
      'btn',
      'btn-secondary'
    );
    this.switchButton.innerHTML = 'Back to processing';

    this.downloadErrorsButton = document.createElement('div');
    this.downloadErrorsButton.classList.add(
      'download-error-log',
      'btn',
      'btn-secondary'
    );
    this.downloadErrorsButton.innerHTML = 'Download error log';

    this.footer.append(this.switchButton);
    this.footer.append(this.downloadErrorsButton);
    this.body.append(this.footer);


    // Modal title element
    if (params.modalTitle) {
      this.title = document.createElement('h4');
      this.title.classList.add('modal-title');
      this.title.innerHTML = params.modalTitle;
    }

    // Constructing the modal
    if (this.title) {
      this.header.appendChild(this.title);
    }
    this.content.append(this.header, this.body);
  }
}

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 * @param {Function} cancelCallback @deprecated You should rely on the closeCallback param
 */
export class ProgressModal extends Modal implements ProgressModalType {
  modal!: ProgressModalParentContainerType;
  doneCount !: number;
  total !: number;
  errors !: Array<string>;
  cancelCallback !: () => void;
  progressModal !: ProgressModalContainer;
  errorModal !: ProgressModalErrorContainer;
  constructor(
    inputParams: InputProgressModalParams
  ) {
    const params: ProgressModalParams = {
      id: 'progress-modal',
      customButtons: [],
      closable: false,
      total: 0,
      dialogStyle: {},
      ...inputParams,
    };

    super(params);
    this.total = params.total;
    this.errors = [];
  }

  protected initContainer(params: ProgressModalParams): void {
    this.modal = new ProgressModalParentContainer(params);
    super.initContainer(params);
    this.progressModal = new ProgressModalContainer(params);
    this.errorModal = new ProgressModalErrorContainer(params);

    this.modal.dialog.appendChild(this.progressModal.content);

    this.initListeners(params);
  }

  public updateCount(doneCount: number)
  {
    this.updatePercentage(doneCount);
  }

  private updatePercentage(doneCount: number)
  {
    let progressBarDone = doneCount * 100 / this.total;
    this.progressModal.progressDone.style.width = progressBarDone+'%';
    this.progressModal.progressPercentage.innerHTML = progressBarDone+'%';
  }

  public addError(error: string)
  {
    this.errors.push(error);
    let errorContent = document.createElement('p');
    errorContent.append(error);
    this.errorModal.errorContainer.append(errorContent);
    this.progressModal.errorCount.innerHTML = this.errors.length.toString();
  }

  /** If I have variable append will still work, so need to try that */
  /** Modal container should not be extended for children */
  /** Return 401 on error */
  /** pass params and take cancelCallback from there */
  private initListeners(params: ProgressModalParams)
  {
    this.errorModal.downloadErrorsButton.addEventListener('click', () => {
      let csvContent = 'data:text/csv;charset=utf-8,';
      this.errors.forEach(function(error) {
        csvContent += error + "\r\n";
      });
      let link = document.createElement('a');
      link.href = encodeURI(csvContent);
      link.download = 'Errors.csv';
      link.click();
    });

    this.errorModal.switchButton.addEventListener('click', () => {
      this.modal.dialog.removeChild(this.errorModal.content);
      this.modal.dialog.appendChild(this.progressModal.content);
    });

    this.progressModal.switchToErrorButton.addEventListener('click', () => {
      this.modal.dialog.removeChild(this.progressModal.content);
      this.modal.dialog.appendChild(this.errorModal.content);
    });

    this.progressModal.stopProcessingButton.addEventListener('click', () => {
      if (params.cancelCallback) {
        params.cancelCallback();
      }
    });
  }
}

export default ProgressModal;
