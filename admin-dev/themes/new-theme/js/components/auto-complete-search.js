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

/**
 * This component is used with TypeaheadType forms, and is tightly linked to the content of this
 * twig file src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/typeahead.html.twig
 *
 * @todo: the component relies on this TypeaheadType because it was the historic type but it would be worth
 * creating a new clean form type
 */
export default class AutoCompleteSearch {
  constructor($searchInput, config, dataSetConfig) {
    this.$searchInput = $searchInput;
    this.$autoCompleteSearchContainer = this.$searchInput.closest('.autocomplete-search');
    this.searchInputId = this.$searchInput.prop('id');
    this.searchInputFullName = this.$autoCompleteSearchContainer.data('fullname');
    this.$selectionContainer = $(`#${this.searchInputId}-data`);

    // Merge default and input config
    const inputConfig = config || {};
    const defaultConfig = {
      minLength: 2,
      highlight: true,
      cache: false,
      hint: false,
    };
    this.config = {...defaultConfig, ...inputConfig};

    // Merge default and input dataSetConfig
    const inputDataSetConfig = dataSetConfig || {};
    const defaultDataSetConfig = {
      display: 'name', // Which field of the object from the list is used for display (can be a string or a callback)
      value: 'id', // Which field of the object from the list is used for value (can be a string or a callback)
      limit: 20, // Limit the number of displayed suggestion
      dataLimit: 0, // How many elements can be selected max
    };
    this.dataSetConfig = {...defaultDataSetConfig, ...inputDataSetConfig};

    // Merging object works fine on one level, but on two it erases sub elements even if not present, so
    // we handle templates separately
    const defaultTemplates = { // Default rendering functions which can be overridden
      suggestion(item) {
        return `<div><img src="${item.image}" style="width:50px" /> ${item.name}</div>`;
      },
      pending(query) {
        return `<div class="px-2">Searching for "${query.query}"</div>`;
      },
      notFound(query) {
        return `<div class="px-2">No results found for "${query.query}"</div>`;
      },
      renderSelected: (selectedItem) => `<li>
            <div>
              <img src="${selectedItem.image}" style="width:50px" /> ${selectedItem.name}
            </div>
          </li>`,
    };
    this.dataSetConfig.templates = {...defaultTemplates, ...inputDataSetConfig.templates};

    this.buildTypeahead();
  }

  /**
   * Force set values, the selected items are removed Then the component loops through the inputs
   * and render each object in the list as if it came from the API so the format must match.
   *
   * @param values {array}
   */
  setValue(values) {
    this.clearValue();
    if (!values || values.length <= 0) {
      return;
    }

    values.each((value) => {
      this.appendSelectedItem(value);
    });
  }

  /**
   * Removes selected items.
   */
  clearValue() {
    const formIdItem = $('li', this.$selectionContainer);
    formIdItem.remove();
  }

  /**
   * Build the typeahead component based on provided configuration.
   */
  buildTypeahead() {
    this.$searchInput.typeahead(this.config, this.dataSetConfig)
      .bind('typeahead:select', (e, selectedItem) => {
        // When limit is one we cannot select additional elements so we replace them instead
        // @todo: maybe this behaviour should be defined by an option
        if (this.dataSetConfig.dataLimit === 1) {
          this.replaceSelectedItem(selectedItem);
        } else {
          this.appendSelectedItem(selectedItem);
        }
      }).bind('typeahead:close', (e) => {
        $(e.target).val('');
      });
  }

  /**
   * When the component is configured to have only one selected element on each selection
   * the previous selection is removed and then replaced.
   *
   * @param selectedItem {Object}
   * @returns {boolean}
   */
  replaceSelectedItem(selectedItem) {
    this.clearValue();
    this.addSelectedContentToContainer(selectedItem);

    return true;
  }

  /**
   * When the component is configured to have more than one selected item on each selection
   * the item is added to the list.
   *
   * @param selectedItem {Object}
   * @returns {boolean}
   */
  appendSelectedItem(selectedItem) {
    // If collection length is up to limit, return
    const formIdItem = $('li', this.$selectionContainer);

    if (this.dataSetConfig.dataLimit !== 0 && formIdItem.length >= this.dataSetConfig.dataLimit) {
      return false;
    }

    this.addSelectedContentToContainer(selectedItem);

    return true;
  }

  /**
   * Add the selected content to the selection container, the HTML is generated based on the render function
   * then a hidden input is automatically added inside it, and finally the rendered selection is added to the list.
   *
   * @param selectedItem {Object}
   */
  addSelectedContentToContainer(selectedItem) {
    let value;

    if (typeof this.dataSetConfig.value === 'function') {
      value = this.dataSetConfig.value(selectedItem);
    } else {
      value = selectedItem[this.dataSetConfig.value];
    }

    const selectedHtml = this.dataSetConfig.templates.renderSelected(selectedItem);
    // Hidden input is added into the selected li
    const $selectedNode = $(selectedHtml);
    const $hiddenInput = $(`<input type="hidden" name="${this.searchInputFullName}[data][]" value="${value}" />`);
    $selectedNode.append($hiddenInput);

    // Then the li is added to the list
    this.$selectionContainer.append($selectedNode);

    // Trigger the change so that listeners detect the form data has been modified
    $hiddenInput.trigger('change');
  }
}
