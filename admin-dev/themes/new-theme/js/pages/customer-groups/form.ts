/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';

const {$} = window;
const router = new Router();

interface TreeCategory {
  id: number;
  name: string;
  displayName: string;
  active: boolean;
  children: TreeCategory[];
}

/**
 * Handles the category reductions CollectionType:
 * - "Add" button clones the Symfony prototype and appends a new entry
 * - "Remove" button removes an existing entry
 * - Category picker modal populates id_category + name in the new entry
 */
class CategoryReductionCollection {
  private container: JQuery;

  private prototype: string;

  private index: number;

  private pendingEntry: JQuery | null = null;

  private categoriesCache: TreeCategory[] | null = null;

  constructor() {
    this.container = $('.js-category-reductions-collection');

    if (!this.container.length) {
      return;
    }

    this.prototype = this.container.data('prototype') as string;
    this.index = parseInt(String(this.container.data('index')), 10) || 0;

    this.bindEvents();
  }

  private bindEvents(): void {
    $(document).on('click', '.js-add-category-reduction', () => {
      this.openCategoryModal();
    });

    $(document).on('click', '.js-remove-category-reduction', (e) => {
      $(e.currentTarget).closest('.category-reduction-entry').remove();
    });

    $(document).on('click', '#category-tree-confirm', () => {
      this.confirmSelection();
    });

    $('#categoryTreeModal').on('hidden.bs.modal', () => {
      this.pendingEntry = null;
      $('#category-reduction-input').val('');
      $('#category-tree-confirm').prop('disabled', true);
    });
  }

  private openCategoryModal(): void {
    const newHtml = this.prototype.replace(/__name__/g, String(this.index));
    this.index += 1;
    this.pendingEntry = $(
      `<div class="category-reduction-entry row mb-2">${newHtml}
        <div class="col-md-2">
          <button type="button" class="btn btn-sm btn-outline-danger js-remove-category-reduction">
            <i class="material-icons">delete</i>
          </button>
        </div>
      </div>`,
    );

    ($('#categoryTreeModal') as any).modal('show');

    if (this.categoriesCache) {
      this.renderTree(this.categoriesCache);
    } else {
      this.loadCategories();
    }
  }

  private loadCategories(): void {
    $('#category-tree-loader').show();
    $('#category-tree-list').hide();

    $.get(router.generate('admin_categories_get_categories_tree'))
      .done((data: TreeCategory[]) => {
        this.categoriesCache = data;
        this.renderTree(data);
      })
      .fail(() => {
        $('#category-tree-loader').html('<p class="text-danger">Failed to load categories.</p>');
      });
  }

  private renderTree(categories: TreeCategory[]): void {
    $('#category-tree-loader').hide();
    const container = $('#category-tree-list');
    container.empty().append(this.buildTreeHtml(categories, 0)).show();

    container.off('click', '.js-category-node').on('click', '.js-category-node', (e) => {
      e.stopPropagation();
      const $node = $(e.currentTarget);
      container.find('.js-category-node').removeClass('bg-primary text-white');
      $node.addClass('bg-primary text-white');
      $('#category-tree-confirm').data('id', $node.data('id')).data('name', $node.data('name')).prop('disabled', false);
    });
  }

  private buildTreeHtml(categories: TreeCategory[], depth: number): string {
    let html = '<ul class="list-unstyled mb-0">';
    categories.forEach((cat) => {
      const indent = depth * 16 + 8;
      const children = cat.children?.length ? this.buildTreeHtml(cat.children, depth + 1) : '';
      html += `<li>
        <div class="d-flex align-items-center py-1 px-2 js-category-node"
             data-id="${cat.id}" data-name="${this.escape(cat.displayName)}"
             style="padding-left:${indent}px;cursor:pointer;border-radius:4px">
          ${this.escape(cat.displayName)}
        </div>${children}</li>`;
    });
    html += '</ul>';

    return html;
  }

  private confirmSelection(): void {
    const btn = $('#category-tree-confirm');
    const categoryId = parseInt(String(btn.data('id')), 10);
    const categoryName = btn.data('name') as string;

    if (!categoryId || !this.pendingEntry) {
      return;
    }

    this.pendingEntry.find('input[name$="[id_category]"]').val(categoryId);
    this.pendingEntry.find('input[name$="[name]"]').val(categoryName);
    this.pendingEntry.find('span.text-preview, input[type="hidden"][name$="[name]"]').closest('.form-group').find('.text-preview-value, .preview').text(categoryName);

    this.container.append(this.pendingEntry);
    this.pendingEntry = null;

    ($('#categoryTreeModal') as any).modal('hide');
  }

  private escape(str: string): string {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
}

$(() => {
  window.prestashop.component.initComponents(['TranslatableInput']);
  new CategoryReductionCollection();
});
