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
 */
export default class AutoCompleteSearch {
  constructor($searchInput, config, dataSetConfig) {
    this.$searchInput = $searchInput;
    this.$autoCompleteSearchContainer = this.$searchInput.closest('.autocomplete-search');
    this.searchInputId = this.$searchInput.prop('id');
    this.searchInputFullName = this.$autoCompleteSearchContainer.data('fullname');
    this.$selectionContainer = $(`#${this.searchInputId}-data`);
    this.$templateContainer = $(`#tplcollection-${this.searchInputId}`);

    // Merge default and input config
    config = config || {};
    const defaultConfig = {
      minLength: 2,
      highlight: true,
      cache: false,
      hint: false,
    };
    this.config = {...defaultConfig, ...config};

    // Merge default and input dataSetConfig
    dataSetConfig = dataSetConfig || {};
    const defaultDataSetConfig = {
      display: 'name', // Which field of the object from the list is used for display (can be a string or a callback)
      value: 'id', // Which field of the object from the list is used for value (can be a string or a callback)
      limit: 20, // Limit the number of displayed suggestion
      dataLimit: 0, // How many elements can be selected max
      templates: { // Default rendering functions which can be overridden
        suggestion(item) {
          return `<div><img src="${item.image}" style="width:50px" /> ${item.name}</div>`;
        },
        pending(query) {
          return `<div class="px-2">Searching for "${query.query}"</div>`;
        },
        notFound(query) {
          return `<div class="px-2">No results found for "${query.query}"</div>`;
        },
        renderSelected: (selectedItem, inputFullName) => {
          let value;
          if (typeof this.dataSetConfig.value === 'function') {
            value = this.dataSetConfig.value(selectedItem);
          } else {
            value = selectedItem[this.dataSetConfig.value];
          }

          return `<li>
            <div>
              <img src="${selectedItem.image}" style="width:50px" /> ${selectedItem.name}
            </div>
            <input type="hidden" name="${inputFullName}[data][]" value="${value}" />
          </li>`;
        }
      }
    };
    this.dataSetConfig = {...defaultDataSetConfig, ...dataSetConfig};

    this.buildTypeahead();
  }

  buildTypeahead() {
    this.$searchInput.typeahead(this.config, this.dataSetConfig)
      .bind('typeahead:select', (e, selectedItem) => {
        if (this.dataSetConfig.dataLimit === 1) {
          this.replaceSelectedItem(selectedItem);
        } else {
          this.appendSelectedItem(selectedItem);
        }
    }).bind('typeahead:close', (e) => {
      $(e.target).val('');
    });
  }

  replaceSelectedItem(selectedItem) {
    const formIdItem = $('li', this.$selectionContainer);
    formIdItem.remove();

    const selectedHtml = this.dataSetConfig.templates.renderSelected(selectedItem, this.searchInputFullName);
    this.$selectionContainer.append(selectedHtml);

    return true;
  }

  appendSelectedItem(selectedItem) {
    // If collection length is up to limit, return
    const formIdItem = $('li', this.$selectionContainer);
    if (this.dataSetConfig.dataLimit !== 0 && formIdItem.length >= this.dataSetConfig.dataLimit) {
      return false;
    }

    const selectedHtml = this.dataSetConfig.templates.renderSelected(selectedItem, this.searchInputFullName);
    this.$selectionContainer.append(selectedHtml);

    return true;
  }
}
