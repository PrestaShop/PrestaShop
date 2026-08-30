/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import DynamicPaginator from '@components/pagination/dynamic-paginator';
import {FormIframeModal} from '@components/modal';
import PaginatedTaxRulesService from '@pages/tax-rules/service/paginated-tax-rules-service';
import TaxRulesListRenderer from '@pages/tax-rules/tax-rules-list-renderer';

import ClickEvent = JQuery.ClickEvent;

const {$} = window;

const LIST_CONTAINER_ID = '#tax-rules-list-container';
const PAGINATION_CONTAINER_ID = '#tax-rules-pagination';
const MODAL_ID = 'tax-rule-form-modal';
const FORM_SELECTOR = 'form[name="tax_rule"]';

export default class TaxRulesManager {
  private paginator!: DynamicPaginator;

  private listContainer!: HTMLElement;

  constructor() {
    const container = document.querySelector<HTMLElement>(LIST_CONTAINER_ID);

    if (!container) {
      return;
    }

    this.listContainer = container;
    const listUrl = container.dataset.listUrl!;
    const createUrl = container.dataset.createUrl!;

    this.paginator = new DynamicPaginator(
      PAGINATION_CONTAINER_ID,
      new PaginatedTaxRulesService(listUrl),
      new TaxRulesListRenderer(() => this.paginator.paginate(1)),
      1,
    );

    this.initAddButton(createUrl);
    this.initEditButtons();
  }

  private initAddButton(createUrl: string): void {
    const addButton = document.querySelector<HTMLElement>('.js-add-tax-rule-btn');

    if (!addButton) {
      return;
    }

    addButton.addEventListener('click', (e) => {
      e.stopImmediatePropagation();
      this.openModal(
        `${createUrl}${createUrl.includes('?') ? '&' : '?'}liteDisplaying=1`,
        addButton.dataset.modalTitle ?? 'Add new tax rule',
        addButton.dataset.confirmButtonLabel ?? 'Save',
        addButton.dataset.cancelButtonLabel ?? 'Cancel',
      );
    });
  }

  private initEditButtons(): void {
    // Delegated listener — catches edit buttons added dynamically by the renderer
    $(this.listContainer).on('click', '.js-edit-tax-rule-btn', (event: ClickEvent) => {
      if (!(event.currentTarget instanceof HTMLElement)) {
        return;
      }

      const editButton = event.currentTarget;
      const {editUrl} = editButton.dataset;

      if (!editUrl) {
        return;
      }

      this.openModal(
        `${editUrl}${editUrl.includes('?') ? '&' : '?'}liteDisplaying=1`,
        editButton.dataset.modalTitle ?? 'Edit tax rule',
        editButton.dataset.confirmButtonLabel ?? 'Save',
        editButton.dataset.cancelButtonLabel ?? 'Cancel',
      );
    });
  }

  private openModal(
    formUrl: string,
    modalTitle: string,
    confirmButtonLabel: string,
    closeButtonLabel: string,
  ): void {
    const iframeModal = new FormIframeModal({
      id: MODAL_ID,
      formSelector: FORM_SELECTOR,
      formUrl,
      closable: true,
      modalTitle,
      confirmButtonLabel,
      closeButtonLabel,
      closeOnConfirm: false,
      onFormLoaded: (_form: HTMLFormElement, _formData: FormData, dataAttributes: DOMStringMap | null): void => {
        if (dataAttributes && dataAttributes.alertsSuccess === '1') {
          iframeModal.hide();
          this.paginator.paginate(1);
        }
      },
      formConfirmCallback: (form: HTMLFormElement): void => {
        form.submit();
      },
    });
    iframeModal.show();
  }
}
