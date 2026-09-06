/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ConfirmModal from '@components/modal';

const componentMap = {
  panel: '.js-grid-views',
  list: '.js-grid-views-list',
  configurationForm: '.js-grid-views-configuration form',
  filterForm: '.js-grid-views-form',
  filterLink: '.js-grid-view-link',
  filterTotal: '.js-grid-view-total',
  editButton: '.js-grid-view-edit',
  duplicateButton: '.js-grid-view-duplicate',
  deleteButton: '.js-grid-view-delete',
  editModal: '.js-grid-views-edit-modal',
  dateRuleRow: '.js-grid-views-date-rule',
};

interface AjaxActionResponse {
  success: boolean;
  message?: string;
  content?: string;
}

function errorMessage(element: HTMLElement | null): string {
  return element?.closest<HTMLElement>(componentMap.panel)?.dataset.errorMessage
    ?? document.querySelector<HTMLElement>(componentMap.panel)?.dataset.errorMessage
    ?? 'Unexpected error';
}

function parseJsonResponse(response: Response): Promise<AjaxActionResponse> {
  return <Promise<AjaxActionResponse>> response.json().catch(() => ({success: false}));
}

function showMessage(success: boolean, message?: string): void {
  if (!message) {
    return;
  }

  const notify = success ? (<any>window).showSuccessMessage : (<any>window).showErrorMessage;

  if (typeof notify === 'function') {
    notify(message);
  }
}

function getPanels(): HTMLElement[] {
  return Array.from(document.querySelectorAll<HTMLElement>(componentMap.panel));
}

function loadFilters(panel: HTMLElement): void {
  const {listUrl} = panel.dataset;
  const list = panel.querySelector<HTMLElement>(componentMap.list);

  if (!listUrl || !list) {
    return;
  }

  fetch(listUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
    .then((response) => {
      if (!response.ok) {
        throw new Error(response.statusText);
      }

      return response.text();
    })
    .then((content) => {
      if (/<html/i.test(content)) {
        throw new Error('Unexpected full page response');
      }

      list.innerHTML = content;

      if (panel.dataset.displayTotals === '1') {
        loadTotals(panel);
      }
    })
    .catch(() => {
      list.innerHTML = '';
    });
}

function loadAllFilters(): void {
  getPanels().forEach((panel) => loadFilters(panel));
}

function setAllTotals(panel: HTMLElement, content: string): void {
  panel.querySelectorAll<HTMLSpanElement>(componentMap.filterTotal).forEach((totalSpan) => {
    // eslint-disable-next-line no-param-reassign
    totalSpan.textContent = content;
  });
}

function loadTotals(panel: HTMLElement): void {
  const {countsUrl} = panel.dataset;

  if (!countsUrl) {
    return;
  }

  setAllTotals(panel, '(…)');

  fetch(countsUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
    .then((response) => {
      if (!response.ok) {
        throw new Error(response.statusText);
      }

      return response.json();
    })
    .then((data: {counts?: Record<string, number | null>}) => {
      const counts = data.counts ?? {};

      panel.querySelectorAll<HTMLAnchorElement>(componentMap.filterLink).forEach((link) => {
        const totalSpan = link.querySelector<HTMLSpanElement>(componentMap.filterTotal);
        const {viewId} = link.dataset;
        const total = viewId !== undefined ? counts[viewId] : undefined;

        if (totalSpan) {
          totalSpan.textContent = typeof total === 'number' ? `(${total})` : '';
        }
      });
    })
    .catch(() => setAllTotals(panel, ''));
}

function loadAllTotals(): void {
  getPanels().forEach((panel) => {
    if (panel.dataset.displayTotals === '1') {
      loadTotals(panel);
    }
  });
}

function initDateRuleInputs(container: HTMLElement | Document): void {
  container.querySelectorAll<HTMLElement>(componentMap.dateRuleRow).forEach((row) => {
    const select = row.querySelector<HTMLSelectElement>('select');
    const daysInput = row.querySelector<HTMLInputElement>('input[type="number"]');

    if (!select || !daysInput) {
      return;
    }

    const toggleDaysInput = (): void => {
      const isCustom = select.value === 'last_days';
      daysInput.disabled = !isCustom;

      if (!isCustom) {
        daysInput.value = '';
      }
    };

    toggleDaysInput();
    select.addEventListener('change', toggleDaysInput);
  });
}

function initConfigurationForms(): void {
  document.querySelectorAll<HTMLFormElement>(componentMap.configurationForm).forEach((form) => {
    form.addEventListener('click', (event) => event.stopPropagation());

    form.querySelectorAll<HTMLInputElement>('input').forEach((input) => {
      input.addEventListener('change', () => {
        fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: {'X-Requested-With': 'XMLHttpRequest'},
        })
          .then(parseJsonResponse)
          .then((data: AjaxActionResponse) => {
            showMessage(data.success, data.message ?? (data.success ? undefined : errorMessage(form)));

            const panel = form.closest<HTMLElement>(componentMap.panel);

            if (panel) {
              const totalsInput = form.querySelector<HTMLInputElement>('input[name$="[display_totals]"]');

              if (totalsInput) {
                panel.dataset.displayTotals = totalsInput.checked ? '1' : '0';
              }

              loadFilters(panel);
            } else {
              loadAllFilters();
            }
          })
          .catch(() => showMessage(false, errorMessage(form)));
      });
    });
  });
}

function submitFilterForm(form: HTMLFormElement): void {
  const submitButtons = Array.from(form.querySelectorAll<HTMLButtonElement>('button[type="submit"]'));

  if (submitButtons.some((button) => button.disabled)) {
    return;
  }
  submitButtons.forEach((button) => button.setAttribute('disabled', 'disabled'));

  fetch(form.action, {
    method: 'POST',
    body: new FormData(form),
    headers: {'X-Requested-With': 'XMLHttpRequest'},
  })
    .then(parseJsonResponse)
    .then((data: AjaxActionResponse) => {
      showMessage(data.success, data.message ?? (data.success ? undefined : errorMessage(form)));

      if (data.success) {
        const modal = form.closest<HTMLElement>('.modal');

        if (modal && (<any>window).$) {
          (<any>window).$(modal).modal('hide');
        }

        loadAllFilters();
      }
    })
    .catch(() => showMessage(false, errorMessage(form)))
    .finally(() => {
      submitButtons.forEach((button) => button.removeAttribute('disabled'));
    });
}

function openEditModal(panel: HTMLElement, editUrl: string): void {
  const modalContent = panel.querySelector<HTMLElement>(`${componentMap.editModal} .modal-content`);
  const modal = panel.querySelector<HTMLElement>(componentMap.editModal);

  if (!modalContent || !modal) {
    return;
  }

  fetch(editUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
    .then(parseJsonResponse)
    .then((data: AjaxActionResponse) => {
      if (!data.success || !data.content) {
        showMessage(false, data.message ?? errorMessage(panel));

        return;
      }

      modalContent.innerHTML = data.content;
      initDateRuleInputs(modalContent);

      if ((<any>window).$) {
        (<any>window).$(modal).modal('show');
      }
    })
    .catch(() => showMessage(false, errorMessage(panel)));
}

function postAction(url: string, contextElement: HTMLElement | null = null): void {
  fetch(url, {
    method: 'POST',
    headers: {'X-Requested-With': 'XMLHttpRequest'},
  })
    .then(parseJsonResponse)
    .then((data: AjaxActionResponse) => {
      showMessage(data.success, data.message ?? (data.success ? undefined : errorMessage(contextElement)));
      loadAllFilters();
    })
    .catch(() => showMessage(false, errorMessage(contextElement)));
}

function confirmDeletion(deleteButton: HTMLElement): void {
  const {ajaxUrl} = deleteButton.dataset;

  if (!ajaxUrl) {
    return;
  }

  const modal = new ConfirmModal(
    {
      id: 'grid-views-delete-confirm-modal',
      confirmTitle: deleteButton.dataset.confirmTitle,
      confirmMessage: deleteButton.dataset.confirmMessage ?? '',
      confirmButtonLabel: deleteButton.dataset.confirmButtonLabel ?? '',
      closeButtonLabel: deleteButton.dataset.closeButtonLabel ?? '',
      confirmButtonClass: 'btn-danger',
    },
    () => postAction(ajaxUrl, deleteButton),
  );

  modal.show();
}

function initDelegatedEvents(): void {
  document.addEventListener('submit', (event) => {
    const form = (<HTMLElement>event.target).closest<HTMLFormElement>(componentMap.filterForm);

    if (form) {
      event.preventDefault();
      submitFilterForm(form);
    }
  });

  document.addEventListener('click', (event) => {
    const target = <HTMLElement>event.target;

    const editButton = target.closest<HTMLElement>(componentMap.editButton);
    const panel = target.closest<HTMLElement>(componentMap.panel);

    if (editButton && panel && editButton.dataset.ajaxUrl) {
      event.preventDefault();
      openEditModal(panel, editButton.dataset.ajaxUrl);

      return;
    }

    const duplicateButton = target.closest<HTMLElement>(componentMap.duplicateButton);

    if (duplicateButton && duplicateButton.dataset.ajaxUrl) {
      event.preventDefault();
      postAction(duplicateButton.dataset.ajaxUrl, duplicateButton);

      return;
    }

    const deleteButton = target.closest<HTMLElement>(componentMap.deleteButton);

    if (deleteButton && deleteButton.dataset.ajaxUrl) {
      event.preventDefault();
      confirmDeletion(deleteButton);
    }
  });
}

function init(): void {
  if ((<any>window).psGridViewsInitialized) {
    return;
  }
  (<any>window).psGridViewsInitialized = true;

  initDelegatedEvents();
  initConfigurationForms();
  initDateRuleInputs(document);
  loadAllTotals();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

export default init;
