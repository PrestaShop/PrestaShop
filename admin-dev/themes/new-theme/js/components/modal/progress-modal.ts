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

/* eslint max-classes-per-file: ["error", 4] */
import ComponentsMap from '@components/components-map';
import {
  ModalParams, Modal, ModalCoreType, ModalContainerType, ModalContainer,
} from '@components/modal/modal';

export interface ProgressModalContainerType extends ModalContainerType {
  progressView: ProgressView;
  errorView: ErrorView;
  switchView(view: string): void;
}
export interface ProgressModalType extends ModalCoreType {
  modal: ProgressModalContainerType;
}
export type ProgressModalParams = Omit<ModalParams, 'modalTitle'> & {
  /**
   * Progression view title (optional) ex: 'Completing %total% actions'
   */
  progressionTitle?: string;
  /**
   * Progression message in progress view Default: 'Processing %done% / %total% elements.'
   */
  progressionMessage: string;
  /**
   * Close button label Default: 'Close'
   */
  closeLabel: string;
  /**
   * Abort process button label Default: 'Stop processing'
   */
  abortProcessingLabel: string;
  /**
   * Errors message in error view, Default: '%error_count% errors occurred. You can download the logs for future reference.'
   */
  errorsMessage: string;
  /**
   * Back to processing button label Default: 'Back to processing'
   */
  backToProcessingLabel: string;
  /**
   * Download error log label Default: 'Download error log'
   */
  downloadErrorLogLabel: string;
  /**
   * View error log label Default: 'View %error_count% error logs'
   */
  viewErrorLogLabel: string;
  /**
   * Error view title Default: 'Error log'
   */
  viewErrorTitle: string;
  /**
   * Total number of elements to process (mandatory)
   */
  total: number;
  /**
   * Additional buttons to display in the progress view footer (optional)
   */
  customButtons: Array<HTMLButtonElement | HTMLAnchorElement>;
  /**
   * Abort callback triggered when the user stops/abort the bulk progress (optional)
   */
  abortCallback?: () => void;
}
export type InputProgressModalParams = Partial<ProgressModalParams> & {
  /**
   * Total must be provided in input params, the other field are either optional or have default values.
   */
  total: number;
};

/**
 * Common interface for all views
 */
export type ViewContainerType = {
  content: HTMLElement;
  body: HTMLElement;
  message: HTMLElement;
  header: HTMLElement;
  title?: HTMLElement;
};

const ProgressModalMap = ComponentsMap.progressModal;

export class ProgressModalContainer extends ModalContainer implements ProgressModalContainerType {
  static readonly PROGRESS_VIEW: string = 'progress_view';

  static readonly ERROR_VIEW: string = 'error_view';

  progressView!: ProgressView;

  errorView!: ErrorView;

  protected currentView!: string;

  /* This constructor is important to force the input type but ESLint is not happy about it*/
  /* eslint-disable no-useless-constructor */
  constructor(params: ProgressModalParams) {
    super(params);
  }

  /**
   * This container is a bit different it
   */
  protected buildModalContainer(params: ProgressModalParams): void {
    this.container = document.createElement('div');
    this.container.classList.add('modal', 'fade', ProgressModalMap.classes.modal);
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

    this.progressView = new ProgressView(params);
    this.errorView = new ErrorView(params);
    this.container.appendChild(this.dialog);
    this.toggleView(this.progressView);
    this.currentView = ProgressModalContainer.PROGRESS_VIEW;
  }

  switchView(view: string): void {
    if (this.currentView === view) {
      return;
    }

    if (view === ProgressModalContainer.PROGRESS_VIEW) {
      this.toggleView(this.progressView);
    } else if (view === ProgressModalContainer.ERROR_VIEW) {
      this.toggleView(this.errorView);
    } else {
      console.error(`Unknown view ${view}`);
      return;
    }

    this.currentView = view;
  }

  protected toggleView(viewContainer: ViewContainerType): void {
    if (this.dialog.contains(this.progressView.content)) {
      this.dialog.removeChild(this.progressView.content);
    }

    if (this.dialog.contains(this.errorView.content)) {
      this.dialog.removeChild(this.errorView.content);
    }

    // Update references to modal usual elements
    this.content = viewContainer.content;
    this.message = viewContainer.message;
    this.header = viewContainer.header;
    this.title = viewContainer.title;
    this.body = viewContainer.body;
    this.dialog.appendChild(viewContainer.content);
  }
}

export class ProgressView implements ViewContainerType {
  footer: HTMLElement;

  content: HTMLElement;

  message: HTMLElement;

  header: HTMLElement;

  title?: HTMLElement;

  body: HTMLElement;

  abortProcessingButton: HTMLElement;

  closeModalButton: HTMLElement;

  switchToErrorButton: HTMLElement;

  progressDone!: HTMLElement;

  lastError: HTMLElement;

  progressMessage: HTMLElement;

  progressPercent: HTMLElement;

  progressIcon: HTMLElement;

  constructor(params: ProgressModalParams) {
    // Modal content element
    this.content = document.createElement('div');
    this.content.classList.add('modal-content');

    // Modal message element
    this.message = document.createElement('p');
    this.message.classList.add('modal-message');

    // Modal header element
    this.header = document.createElement('div');
    this.header.classList.add('modal-header');

    if (params.progressionTitle) {
      this.title = document.createElement('h4');
      this.title.classList.add('modal-title');
      this.title.innerHTML = params.progressionTitle.replace('%total%', String(params.total));
      this.header.append(this.title);
    }

    this.switchToErrorButton = document.createElement('div');
    this.switchToErrorButton.classList.add(
      ProgressModalMap.classes.switchToErrorButton,
      'alert',
      'alert-warning',
      'd-none',
    );

    this.switchToErrorButton.innerHTML = params.viewErrorLogLabel.replace('%error_count%', '0');
    this.header.append(this.switchToErrorButton);

    // Modal body element
    this.body = document.createElement('div');
    this.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

    // Progress headline with icon and progression message embedded in a parent div
    const progressHeadline = document.createElement('div');
    progressHeadline.classList.add(ProgressModalMap.classes.progressHeadline);
    this.progressMessage = document.createElement('div');
    this.progressMessage.classList.add(ProgressModalMap.classes.progressMessage);
    this.progressMessage.innerHTML = params.progressionMessage
      .replace('%done%', '0')
      .replace('%total%', String(params.total));

    this.progressIcon = document.createElement('span');
    this.progressIcon.classList.add(ProgressModalMap.classes.progressIcon);
    const spinner = document.createElement('div');
    spinner.classList.add('spinner');
    this.progressIcon.appendChild(spinner);

    this.progressPercent = document.createElement('span');
    this.progressPercent.classList.add(ProgressModalMap.classes.progressPercent);
    this.progressPercent.innerHTML = '0%';

    progressHeadline.append(this.progressIcon);
    progressHeadline.append(this.progressMessage);
    progressHeadline.append(this.progressPercent);
    this.body.append(progressHeadline);

    // Then  add progress bar
    this.body.append(this.buildProgressBar());

    this.lastError = document.createElement('div');
    this.lastError.classList.add('alert', 'alert-warning', 'd-print-none', 'd-none');
    this.body.append(this.lastError);

    // Modal footer element
    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');

    this.abortProcessingButton = document.createElement('button');
    this.abortProcessingButton.setAttribute('type', 'button');
    this.abortProcessingButton.classList.add('btn', 'btn-secondary', 'btn-lg', ProgressModalMap.classes.stopProcessing);
    this.abortProcessingButton.innerHTML = params.abortProcessingLabel;

    this.closeModalButton = document.createElement('button');
    this.closeModalButton.setAttribute('type', 'button');
    this.closeModalButton.classList.add('btn', 'btn-primary', 'btn-lg', ProgressModalMap.classes.closeModalButton, 'd-none');
    this.closeModalButton.innerHTML = params.closeLabel;
    this.closeModalButton.dataset.dismiss = 'modal';

    // Appending element to the modal
    this.footer.append(this.abortProcessingButton, this.closeModalButton, ...params.customButtons);

    this.content.append(this.header, this.body, this.footer);
  }

  protected buildProgressBar(): HTMLElement {
    const progressBar = document.createElement('div');
    progressBar.setAttribute('style', 'display: block; width: 100%');

    progressBar.classList.add(
      'progress',
      'active',
    );

    this.progressDone = document.createElement('div');
    this.progressDone.classList.add(
      'progress-bar',
      'progress-bar-success',
    );
    this.progressDone.setAttribute('style', 'width: 0%');
    this.progressDone.setAttribute('role', 'progressbar');
    this.progressDone.setAttribute('aria-valuemax', '100');
    this.progressDone.id = ProgressModalMap.classes.progressBarDone;
    progressBar.append(this.progressDone);

    return progressBar;
  }
}

export class ErrorView implements ViewContainerType {
  footer: HTMLElement;

  content: HTMLElement;

  message: HTMLElement;

  header: HTMLElement;

  title?: HTMLElement;

  body: HTMLElement;

  errorMessage: HTMLElement;

  switchToProgressButton: HTMLElement;

  downloadErrorsButton: HTMLElement;

  errorContainer: HTMLElement;

  constructor(params: ProgressModalParams) {
    this.content = document.createElement('div');
    this.content.classList.add('modal-content');

    // Modal message element
    this.message = document.createElement('p');
    this.message.classList.add('modal-message');

    // Modal header element
    this.header = document.createElement('div');
    this.header.classList.add('modal-header');

    // Modal title element
    this.title = document.createElement('h4');
    this.title.classList.add('modal-title');
    this.title.innerHTML = params.viewErrorTitle;
    this.header.appendChild(this.title);

    this.body = document.createElement('div');
    this.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

    this.errorMessage = document.createElement('div');
    this.errorMessage.classList.add(ProgressModalMap.classes.errorMessage);
    this.errorMessage.innerHTML = params.errorsMessage.replace('%error_count%', '0');
    this.body.append(this.errorMessage);

    this.errorContainer = document.createElement('div');
    this.errorContainer.classList.add(
      ProgressModalMap.classes.errorContainer,
      'd-print-none',
    );
    this.body.append(this.errorContainer);

    this.footer = document.createElement('div');
    this.footer.classList.add('modal-footer');

    this.switchToProgressButton = document.createElement('div');
    this.switchToProgressButton.classList.add(
      ProgressModalMap.classes.switchToProgressButton,
      'btn',
      'btn-secondary',
    );
    this.switchToProgressButton.innerHTML = params.backToProcessingLabel;

    this.downloadErrorsButton = document.createElement('div');
    this.downloadErrorsButton.classList.add(
      ProgressModalMap.classes.downloadErrorLogButton,
      'btn',
      'btn-secondary',
    );
    const downloadIcon = ProgressModal.getProgressIcon('download');
    this.downloadErrorsButton.innerHTML = `${downloadIcon.outerHTML} ${params.downloadErrorLogLabel}`;

    this.footer.append(this.switchToProgressButton);
    this.footer.append(this.downloadErrorsButton);

    this.content.append(this.header, this.body, this.footer);
  }
}

/**
 * ConfirmModal component
 *
 * @param {InputConfirmModalParams} params
 */
export class ProgressModal extends Modal implements ProgressModalType {
  modal!: ProgressModalContainerType;

  protected doneCount: number;

  protected total: number;

  protected errors: Array<string>;

  protected progressStopped: boolean;

  protected params: ProgressModalParams;

  constructor(inputParams: InputProgressModalParams) {
    const params: ProgressModalParams = {
      id: 'progress-modal',
      customButtons: [],
      closable: false,
      dialogStyle: {},
      progressionMessage: 'Processing %done% / %total% elements.',
      closeLabel: 'Close',
      abortProcessingLabel: 'Stop processing',
      errorsMessage: '%error_count% errors occurred. You can download the logs for future reference.',
      backToProcessingLabel: 'Back to processing',
      downloadErrorLogLabel: 'Download error log',
      viewErrorLogLabel: 'View %error_count% error logs',
      viewErrorTitle: 'Error log',
      ...inputParams,
    };

    super(params);
    this.doneCount = 0;
    this.total = params.total;
    this.errors = [];
    this.progressStopped = false;
    this.params = params;
  }

  protected initContainer(params: ProgressModalParams): void {
    this.modal = new ProgressModalContainer(params);
    super.initContainer(params);
    this.initListeners(params);
  }

  public updateProgress(doneCount: number): void {
    this.doneCount = doneCount;

    const percentDone = (this.doneCount * 100) / this.total;
    this.modal.progressView.progressDone.style.width = `${String(percentDone)}%`;
    // This attribute is used in CSS rules for low values
    this.modal.progressView.progressDone.setAttribute('aria-valuenow', percentDone.toFixed());
    this.modal.progressView.progressMessage.innerHTML = this.params.progressionMessage
      .replace('%done%', String(this.doneCount))
      .replace('%total%', String(this.params.total));
    this.modal.progressView.progressPercent.innerHTML = `${String(percentDone.toFixed())}%`;
  }

  public addError(error: string): void {
    this.errors.push(error);

    const errorContent = document.createElement('p');
    errorContent.classList.add(ProgressModalMap.classes.progressModalError);
    errorContent.append(this.getWarningIcon());
    errorContent.append(error);

    this.modal.errorView.errorContainer.append(errorContent);
    this.modal.progressView.switchToErrorButton.innerHTML = this.params.viewErrorLogLabel.replace(
      '%error_count%',
      this.errors.length.toString(),
    );
    this.modal.errorView.errorMessage.innerHTML = this.params.errorsMessage.replace(
      '%error_count%',
      this.errors.length.toFixed(),
    );
    this.modal.progressView.lastError.classList.remove('d-none');
    this.modal.progressView.lastError.innerHTML = error;

    this.modal.progressView.switchToErrorButton.classList.remove('d-none');
  }

  public completeProgress(): void {
    this.stopProgress(this.errors.length > 0 ? this.getWarningIcon() : this.getCompleteIcon());
  }

  public interruptProgress(): void {
    this.stopProgress(this.getStopIcon());
  }

  protected stopProgress(progressIcon: HTMLElement): void {
    if (this.progressStopped) {
      return;
    }

    this.replaceStopProcessButton();
    this.modal.progressView.progressIcon.innerHTML = progressIcon.outerHTML;
    this.progressStopped = true;
  }

  protected initListeners(params: ProgressModalParams): void {
    this.modal.errorView.downloadErrorsButton.addEventListener('click', () => {
      let csvContent = 'data:text/csv;charset=utf-8,';
      this.errors.forEach((error) => {
        csvContent += `${error}\r\n`;
      });

      const link = document.createElement('a');
      link.href = encodeURI(csvContent);
      link.download = 'errors.csv';
      link.click();
    });

    this.modal.errorView.switchToProgressButton.addEventListener('click', () => {
      this.modal.switchView(ProgressModalContainer.PROGRESS_VIEW);
    });

    this.modal.progressView.switchToErrorButton.addEventListener('click', () => {
      this.modal.switchView(ProgressModalContainer.ERROR_VIEW);
    });

    this.modal.progressView.abortProcessingButton.addEventListener('click', () => {
      this.interruptProgress();
      if (params.abortCallback) {
        params.abortCallback();
      }
    });

    this.modal.progressView.closeModalButton.addEventListener('click', () => {
      if (params.closeCallback) {
        params.closeCallback();
      }
    });
  }

  protected replaceStopProcessButton(): void {
    this.modal.progressView.abortProcessingButton.classList.add('d-none');
    this.modal.progressView.closeModalButton.classList.remove('d-none');
  }

  protected getWarningIcon(): HTMLElement {
    return ProgressModal.getProgressIcon('warning');
  }

  protected getCompleteIcon(): HTMLElement {
    return ProgressModal.getProgressIcon('complete');
  }

  protected getStopIcon(): HTMLElement {
    return ProgressModal.getProgressIcon('stop');
  }

  public static getProgressIcon(progressStatus: string): HTMLElement {
    let iconContent: string;

    switch (progressStatus) {
      case 'complete':
        iconContent = 'check';
        break;
      case 'stop':
        iconContent = 'close';
        break;
      case 'download':
        iconContent = 'file_download';
        break;
      default:
        iconContent = progressStatus;
        break;
    }

    const progressIcon = document.createElement('span');
    progressIcon.classList.add(
      'material-icons',
      ProgressModalMap.classes.progressStatusIcon(progressStatus),
    );
    progressIcon.innerHTML = iconContent;

    return progressIcon;
  }
}

export default ProgressModal;
