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
import componentMap from './../components-map';

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

  lastError!: HTMLElement;

  progressIconContainer!: HTMLElement;

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

    if (params.modalTitle) {
      this.title = document.createElement('h4');
      this.title.classList.add('modal-title');
      this.title.innerHTML = params.modalTitle.replace('%d', '');
      this.header.append(this.title);
    }

    this.switchToErrorButton = document.createElement('div');
    this.switchToErrorButton.classList.add(
      componentMap.progressModal.switchToErrorButton,
      'alert',
      'alert-warning'
    );

    let errorLogString = 'View %d error logs';
    this.switchToErrorButton.innerHTML = errorLogString.replace('%d', '0');
    this.header.append(this.switchToErrorButton);

    // Modal body element
    this.body = document.createElement('div');
    this.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

    this.progressPercentage = document.createElement('div');
    this.progressPercentage.classList.add(
      'float-right',
      componentMap.progressModal.progressPercent,
    );
    this.progressPercentage.append('0%');
    this.progressIconContainer = document.createElement('span');
    let progressBarHeadline = document.createElement('div');
    progressBarHeadline.classList.add('progress-headline')
    progressBarHeadline.append(this.progressIconContainer);

    if (params.modalTitle) {
      let progressBarTitle = document.createElement('span');
      progressBarTitle.innerHTML = params.modalTitle.replace('%d', params.total.toString());
      progressBarHeadline.append(progressBarTitle);
    }

    progressBarHeadline.append(this.progressPercentage);
    this.body.append(progressBarHeadline);
    this.body.append(this.buildProgressBar());

    this.lastError = document.createElement('div');
    this.lastError.classList.add(
      'alert',
      'alert-warning',
      'd-print-none',
      'd-none'
    )

    this.body.append(this.lastError);

    let errorContainer = document.createElement('div');
    errorContainer.classList.add(componentMap.progressModal.modalSingleErrorContainer);
    this.body.append(errorContainer);

    // Modal footer element
    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');

    this.stopProcessingButton = document.createElement('button');
    this.stopProcessingButton.setAttribute('type', 'button');
    this.stopProcessingButton.classList.add('btn', 'btn-primary', 'btn-lg', componentMap.progressModal.stopProcessingButton);
    this.stopProcessingButton.innerHTML = 'Stop processing';

    // Appending element to the modal
    this.footer.append(this.stopProcessingButton, ...params.customButtons);

    this.content.append(this.header, this.body, this.footer);
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
    this.progressDone.setAttribute('id', componentMap.progressModal.progressBarDone);
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
    // Modal title element
    if (params.modalTitle) {
      this.title = document.createElement('h4');
      this.title.classList.add('modal-title');
      this.title.innerHTML = 'Error log';
      this.header.appendChild(this.title);
    }

    this.body = document.createElement('div');

    let errorTitle = document.createElement('span');
    let errorsString = '%d errors occurred. You can download the logs for future reference.';
    errorTitle.innerHTML = errorsString.replace('%d', '0');
    this.body.append(errorTitle);
    this.errorContainer = document.createElement('div');
    this.errorContainer.classList.add(componentMap.progressModal.modalErrorContainer);
    this.errorContainer.classList.add
    (
      'd-print-none'
    );
    this.body.append(this.errorContainer);

    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');
    this.switchButton = document.createElement('div');
    this.switchButton.classList.add(
      componentMap.progressModal.switchToProgressButton,
      'btn',
      'btn-secondary'
    );
    this.switchButton.innerHTML = 'Back to processing';

    this.downloadErrorsButton = document.createElement('div');
    this.downloadErrorsButton.classList.add(
      componentMap.progressModal.downloadErrorLogButton,
      'btn',
      'btn-secondary'
    );
    this.downloadErrorsButton.innerHTML = 'Download error log';

    this.footer.append(this.switchButton);
    this.footer.append(this.downloadErrorsButton);

    this.content.append(this.header, this.body, this.footer);
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
  processStopped !: boolean;
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
    this.processStopped = false;
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
    this.progressModal.progressPercentage.innerHTML = progressBarDone.toFixed()+'%';
  }

  public addError(error: string)
  {
    this.errors.push(error);
    let errorContent = document.createElement('p');
    errorContent.classList.add(
      'progress-modal-error',
    );
    errorContent.append(this.getErrorIcon());
    errorContent.append(error);
    this.errorModal.errorContainer.append(errorContent);
    let errorLogString = 'View %d error logs';
    this.progressModal.switchToErrorButton.innerHTML = errorLogString.replace('%d', this.errors.length.toString());
    this.progressModal.lastError.classList.remove('d-none');
    this.progressModal.lastError.innerHTML = error;
    if (!this.processStopped) {
      this.progressModal.progressIconContainer.innerHTML = this.getErrorIcon().outerHTML;
    }
  }

  /** Return 401 on error */
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
        this.stopProcess();
      }
    });
  }

  public finishProcess()
  {
    if (this.processStopped) {
      return
    }

    this.replaceStopProcessButton();

    if (!this.errors.length) {
      this.progressModal.progressIconContainer.innerHTML = this.getCompleteIcon().outerHTML;
    }
    this.processStopped = true;
  }

  public stopProcess()
  {
    if (this.processStopped) {
      return
    }

    this.replaceStopProcessButton();
    this.progressModal.progressIconContainer.innerHTML = this.getStopIcon().outerHTML;
    this.processStopped = true;
  }

  private replaceStopProcessButton()
  {
    this.progressModal.stopProcessingButton.remove();
    let closeButton = document.createElement('button');
    closeButton.setAttribute('type', 'button');
    closeButton.classList.add('btn', 'btn-primary', 'btn-lg', 'stop-processing');
    closeButton.innerHTML = 'Close';
    closeButton.dataset.dismiss = 'modal';
    this.progressModal.footer.append(closeButton);
  }


  private getErrorIcon()
  {
    let errorIcon = document.createElement('span');
    errorIcon.classList.add(
      'material-icons',
      'progress-warning-icon'
    );
    errorIcon.innerHTML = 'warning';
    return errorIcon;
  }

  private getCompleteIcon()
  {
    let errorIcon = document.createElement('span');
    errorIcon.classList.add(
      'material-icons',
      'progress-complete-icon'
    );
    errorIcon.innerHTML = 'check';
    return errorIcon;
  }

  private getStopIcon()
  {
    let errorIcon = document.createElement('span');
    errorIcon.classList.add(
      'material-icons',
      'progress-stop-icon'
    );
    errorIcon.innerHTML = 'close';
    return errorIcon;
  }
}

export default ProgressModal;
