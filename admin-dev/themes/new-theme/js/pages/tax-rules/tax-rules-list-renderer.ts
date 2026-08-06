/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import RendererType from '@PSTypes/renderers';
import ConfirmModal from '@components/modal/confirm-modal';

const {$} = window;

const LIST_CONTAINER_ID = '#tax-rules-list-container';
const LOADING_SPINNER_ID = '#tax-rules-loading';
const LIST_TABLE_ID = '#tax-rules-list-table';
const ROW_TEMPLATE_ID = '#tax-rule-tr-template';

interface TaxRuleForList {
  id: number;
  countryName: string;
  stateName: string;
  zipcode: string;
  behavior: string;
  taxName: string;
  taxRate: string;
  description: string;
  editUrl: string;
  deleteUrl: string;
}

export default class TaxRulesListRenderer implements RendererType {
  private listContainer: HTMLElement;

  private $loadingSpinner: JQuery;

  private $listTable: JQuery;

  private onListUpdated: () => void;

  constructor(onListUpdated: () => void) {
    this.listContainer = document.querySelector<HTMLElement>(LIST_CONTAINER_ID)!;
    this.$loadingSpinner = $(LOADING_SPINNER_ID);
    this.$listTable = $(LIST_TABLE_ID);
    this.onListUpdated = onListUpdated;
  }

  public setLoading(loading: boolean): void {
    this.$loadingSpinner.toggle(loading);
    this.$listTable.toggle(!loading);
  }

  public render(data: Record<string, any>): void {
    const tbody = this.listContainer.querySelector('tbody') as HTMLElement;
    const trTemplateEl = this.listContainer.querySelector(ROW_TEMPLATE_ID) as HTMLScriptElement;
    const trTemplate = trTemplateEl.innerHTML;
    tbody.innerHTML = '';

    const taxRules = data.taxRules as Array<TaxRuleForList>;
    this.toggleListVisibility(taxRules.length > 0);

    taxRules.forEach((taxRule: TaxRuleForList) => {
      const temporaryContainer = document.createElement('tbody');
      temporaryContainer.innerHTML = trTemplate.trim();
      const trClone = temporaryContainer.firstChild as HTMLElement;

      this.setField(trClone, '.tax-rule-country', taxRule.countryName);
      this.setField(trClone, '.tax-rule-state', taxRule.stateName);
      this.setField(trClone, '.tax-rule-zipcode', taxRule.zipcode);
      this.setField(trClone, '.tax-rule-behavior', taxRule.behavior);
      this.setField(trClone, '.tax-rule-tax-name', taxRule.taxName);
      this.setField(trClone, '.tax-rule-tax-rate', taxRule.taxRate);
      this.setField(trClone, '.tax-rule-description', taxRule.description);

      const editBtn = trClone.querySelector<HTMLElement>('.js-edit-tax-rule-btn')!;
      editBtn.dataset.editUrl = taxRule.editUrl;

      const deleteBtn = trClone.querySelector<HTMLElement>('.js-delete-tax-rule-btn')!;
      deleteBtn.dataset.deleteUrl = taxRule.deleteUrl;
      deleteBtn.addEventListener('click', () => this.handleDeleteClick(deleteBtn.dataset));

      tbody.append(trClone);
    });
  }

  private toggleListVisibility(show: boolean): void {
    this.listContainer.classList.toggle('d-none', !show);
  }

  private setField(container: HTMLElement, selector: string, value: string): void {
    const el = container.querySelector<HTMLElement>(selector);

    if (el) {
      el.textContent = value;
    }
  }

  private handleDeleteClick(dataset: DOMStringMap): void {
    const modal = new ConfirmModal(
      {
        id: 'modal-delete-tax-rule',
        confirmTitle: dataset.confirmTitle,
        confirmMessage: dataset.confirmMessage,
        confirmButtonLabel: dataset.confirmBtnLabel,
        closeButtonLabel: dataset.cancelBtnLabel,
        confirmButtonClass: dataset.confirmBtnClass,
        closable: true,
      },
      async () => {
        if (!dataset.deleteUrl) {
          return;
        }
        try {
          const response = await window.fetch(dataset.deleteUrl, {
            method: 'DELETE',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
          });
          const json = await response.json();

          if (json.message) {
            $.growl({message: json.message});
          }
          this.onListUpdated();
        } catch (e) {
          console.error('Failed to delete tax rule', e);
        }
      },
    );
    modal.show();
  }
}
