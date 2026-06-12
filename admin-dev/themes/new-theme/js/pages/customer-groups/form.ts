import $ from 'jquery';
import Router from '@components/router';

interface CategoryReduction {
  id_category: number;
  reduction: number;
  name: string;
}

interface InstalledModule {
  id_module: number;
  name: string;
  active: number;
}

interface TreeCategory {
  id: number;
  name: string;
  displayName: string;
  active: boolean;
  children: TreeCategory[];
}

declare const allModules: InstalledModule[];
declare const initialCategoryReductions: CategoryReduction[] | undefined;

const router = new Router();

class CustomerGroupForm {
  private categoryReductionsField: JQuery;
  private authorizedModulesField: JQuery;
  private categoryReductions: CategoryReduction[] = [];
  private authorizedModuleIds: number[] = [];
  private selectedCategoryId: number | null = null;
  private selectedCategoryName: string = '';
  private categoriesCache: TreeCategory[] | null = null;

  constructor() {
    this.categoryReductionsField = $('.js-category-reductions-data');
    this.authorizedModulesField = $('.js-authorized-modules-data');

    this.loadInitialData();
    this.bindEvents();
  }

  private loadInitialData(): void {
    if (typeof initialCategoryReductions !== 'undefined') {
      this.categoryReductions = initialCategoryReductions;
      this.syncCategoryReductionsField();
    } else {
      try {
        const catData = this.categoryReductionsField.val() as string;
        this.categoryReductions = catData ? JSON.parse(catData) : [];
      } catch {
        this.categoryReductions = [];
      }
    }

    try {
      const modData = this.authorizedModulesField.val() as string;
      this.authorizedModuleIds = modData ? JSON.parse(modData) : [];
    } catch {
      this.authorizedModuleIds = [];
    }

    this.renderCategoryReductions();
    this.renderModuleColumns();
  }

  private renderCategoryReductions(): void {
    const tbody = $('#category-reductions-body');
    tbody.empty();

    if (this.categoryReductions.length === 0) {
      tbody.append(
        '<tr class="js-no-reductions"><td colspan="3" class="text-center text-muted">No category reductions defined.</td></tr>',
      );
      return;
    }

    this.categoryReductions.forEach((item, index) => {
      tbody.append(`
        <tr data-index="${index}">
          <td>${this.escapeHtml(item.name || `Category #${item.id_category}`)}</td>
          <td>${parseFloat(String(item.reduction)).toFixed(2)}%</td>
          <td>
            <button type="button" class="btn btn-sm btn-outline-danger js-delete-reduction" data-index="${index}">
              <i class="material-icons">delete</i>
            </button>
          </td>
        </tr>
      `);
    });
  }

  private renderModuleColumns(): void {
    const authorizedList = $('#authorized-modules-list');
    const unauthorizedList = $('#unauthorized-modules-list');
    authorizedList.empty();
    unauthorizedList.empty();

    if (typeof allModules === 'undefined' || !Array.isArray(allModules)) {
      return;
    }

    allModules.forEach((mod: InstalledModule) => {
      const option = `<option value="${mod.id_module}">${this.escapeHtml(mod.name)}</option>`;
      if (this.authorizedModuleIds.includes(mod.id_module)) {
        authorizedList.append(option);
      } else {
        unauthorizedList.append(option);
      }
    });
  }

  private syncCategoryReductionsField(): void {
    this.categoryReductionsField.val(JSON.stringify(this.categoryReductions));
  }

  private syncAuthorizedModulesField(): void {
    this.authorizedModulesField.val(JSON.stringify(this.authorizedModuleIds));
  }

  private bindEvents(): void {
    $(document).on('click', '.js-delete-reduction', (e) => {
      const index = parseInt($(e.currentTarget).data('index'), 10);
      this.categoryReductions.splice(index, 1);
      this.renderCategoryReductions();
      this.syncCategoryReductionsField();
    });

    $(document).on('click', '.js-add-category-reduction', () => {
      this.openCategoryModal();
    });

    $(document).on('click', '#category-tree-confirm', () => {
      this.confirmCategorySelection();
    });

    $(document).on('input', '#category-tree-search-input', (e) => {
      this.filterTree($(e.currentTarget).val() as string);
    });

    $(document).on('click', '.js-remove-module-auth', () => {
      const selected = $('#authorized-modules-list option:selected');
      selected.each((_i, opt) => {
        const id = parseInt($(opt).val() as string, 10);
        this.authorizedModuleIds = this.authorizedModuleIds.filter((x) => x !== id);
      });
      this.renderModuleColumns();
      this.syncAuthorizedModulesField();
    });

    $(document).on('click', '.js-add-module-auth', () => {
      const selected = $('#unauthorized-modules-list option:selected');
      selected.each((_i, opt) => {
        const id = parseInt($(opt).val() as string, 10);
        if (!this.authorizedModuleIds.includes(id)) {
          this.authorizedModuleIds.push(id);
        }
      });
      this.renderModuleColumns();
      this.syncAuthorizedModulesField();
    });

    $('#categoryTreeModal').on('hidden.bs.modal', () => {
      this.selectedCategoryId = null;
      this.selectedCategoryName = '';
      $('#category-reduction-input').val('');
      $('#category-tree-confirm').prop('disabled', true);
    });
  }

  private openCategoryModal(): void {
    this.selectedCategoryId = null;
    this.selectedCategoryName = '';
    $('#category-reduction-input').val('');
    $('#category-tree-confirm').prop('disabled', true);
    ($('#categoryTreeModal') as any).modal('show');

    if (this.categoriesCache) {
      this.renderCategoryTree(this.categoriesCache);
      return;
    }

    $('#category-tree-loader').show();
    $('#category-tree-list').hide();
    $('#category-tree-search').hide();

    $.get(router.generate('admin_categories_get_categories_tree'))
      .done((data: TreeCategory[]) => {
        this.categoriesCache = data;
        this.renderCategoryTree(data);
      })
      .fail(() => {
        $('#category-tree-loader').html('<p class="text-danger">Failed to load categories.</p>');
      });
  }

  private renderCategoryTree(categories: TreeCategory[]): void {
    $('#category-tree-loader').hide();
    const container = $('#category-tree-list');
    container.empty();
    container.append(this.buildTreeHtml(categories, 0));
    container.show();
    $('#category-tree-search').show();

    container.on('click', '.js-category-node', (e) => {
      e.stopPropagation();
      const $node = $(e.currentTarget);
      container.find('.js-category-node').removeClass('bg-primary text-white');
      $node.addClass('bg-primary text-white');
      this.selectedCategoryId = parseInt($node.data('id'), 10);
      this.selectedCategoryName = $node.data('name') as string;
      $('#category-tree-confirm').prop('disabled', false);
    });
  }

  private buildTreeHtml(categories: TreeCategory[], depth: number): string {
    let html = '<ul class="list-unstyled mb-0">';

    for (const cat of categories) {
      const indent = depth * 16;
      const hasChildren = cat.children && cat.children.length > 0;
      html += `
        <li>
          <div class="d-flex align-items-center py-1 px-2 js-category-node"
               data-id="${cat.id}"
               data-name="${this.escapeHtml(cat.displayName)}"
               style="padding-left:${indent + 8}px;cursor:pointer;border-radius:4px"
               data-depth="${depth}">
            ${hasChildren ? '<i class="material-icons" style="font-size:16px;margin-right:4px">folder</i>' : '<i class="material-icons" style="font-size:16px;margin-right:4px;color:#ccc">label</i>'}
            ${this.escapeHtml(cat.displayName)}
          </div>
          ${hasChildren ? this.buildTreeHtml(cat.children, depth + 1) : ''}
        </li>`;
    }

    html += '</ul>';
    return html;
  }

  private filterTree(query: string): void {
    const q = query.toLowerCase().trim();

    if (!q) {
      $('#category-tree-list .js-category-node').closest('li').show();
      return;
    }

    $('#category-tree-list .js-category-node').each((_i, el) => {
      const name = ($(el).data('name') as string).toLowerCase();
      $(el).closest('li').toggle(name.includes(q));
    });
  }

  private confirmCategorySelection(): void {
    if (!this.selectedCategoryId) {
      return;
    }

    const reductionRaw = parseFloat($('#category-reduction-input').val() as string);
    const reduction = isNaN(reductionRaw) ? 0 : Math.min(100, Math.max(0, reductionRaw));

    const existing = this.categoryReductions.findIndex((r) => r.id_category === this.selectedCategoryId);
    if (existing !== -1) {
      this.categoryReductions[existing].reduction = reduction;
    } else {
      this.categoryReductions.push({
        id_category: this.selectedCategoryId,
        reduction,
        name: this.selectedCategoryName,
      });
    }

    this.renderCategoryReductions();
    this.syncCategoryReductionsField();
    ($('#categoryTreeModal') as any).modal('hide');
  }

  private escapeHtml(str: string): string {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
}

$(() => {
  window.prestashop.component.initComponents(['TranslatableInput']);
  new CustomerGroupForm();
});
