/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import $ from 'jquery';

/**
 * Handles the category reductions CollectionType:
 * - Initialises the PS ChoiceTree component on the category picker
 * - "Add" button clones the Symfony prototype and fills category name + ID from the picker selection
 * - "Remove" button removes an existing entry
 */
class CategoryReductionCollection {
  private $collection: JQuery;

  private prototype: string = '';

  private index: number = 0;

  constructor() {
    this.$collection = $('.js-category-reductions-collection');

    if (!this.$collection.length) {
      return;
    }

    this.prototype = this.$collection.data('prototype') as string;
    this.index = parseInt(String(this.$collection.data('index')), 10) || 0;

    new window.prestashop.component.ChoiceTree('#customer_group_category_picker');

    this.$collection.find('.category-reduction-entry').each((_, el) => {
      this.appendDeleteButton($(el));
    });

    this.bindEvents();
  }

  private bindEvents(): void {
    $(document).on('click', '.js-add-category-reduction', () => {
      this.addEntry();
    });

    $(document).on('click', '.js-remove-category-reduction', (e) => {
      $(e.currentTarget).closest('.category-reduction-entry').remove();
    });
  }

  private addEntry(): void {
    const $checked = $('#customer_group_category_picker').find('input[type="radio"].category:checked');

    if (!$checked.length) {
      return;
    }

    const categoryId = parseInt($checked.val() as string, 10);
    const categoryName = $checked.closest('label').text().trim();

    const newHtml = this.prototype.replace(/__name__/g, String(this.index));
    this.index += 1;

    const $entry = $(`<div class="category-reduction-entry">${newHtml}</div>`);
    $entry.find('input[name*="[id_category]"]').val(categoryId);
    $entry.find('input[name*="[name]"]').val(categoryName);

    this.appendDeleteButton($entry);
    this.$collection.append($entry);

    $checked.prop('checked', false);
  }

  private appendDeleteButton($entry: JQuery): void {
    $entry.append(
      $('<button>', {
        type: 'button',
        class: 'btn btn-sm btn-outline-danger js-remove-category-reduction ml-2',
      }).append($('<i>', {class: 'material-icons', text: 'delete'})),
    );
  }
}

$(() => {
  window.prestashop.component.initComponents(['TranslatableInput']);
  new CategoryReductionCollection();
});
