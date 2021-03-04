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

import AutoCompleteSearch from '@components/auto-complete-search';
import Bloodhound from 'typeahead.js';

/**
 * This component is used to search and select an entity, it is uses the AutoSearchComplete
 * component which displays a list of suggestion based on an API returned response. Then when
 * an element is selected it is added to the selection container and hidden inputs are created to
 * send an array of entity IDs in the form request.
 *
 * This component is used with TypeaheadType forms, and is tightly linked to the content of this
 * twig file src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/typeahead.html.twig
 *
 * @todo: the component relies on this TypeaheadType because it was the historical type but it would be worth
 * creating a new clean form type with better templating (the tplcollection brings nearly no value as is)
 */
export default class EntitySearchInput {
  constructor($entitySearchInput, options) {
    this.$entitySearchInput = $entitySearchInput;
    this.entitySearchInputId = this.$entitySearchInput.prop('id');
    this.$autoCompleteSearchContainer = this.$entitySearchInput.closest('.autocomplete-search');
    this.$selectionContainer = $(`#${this.entitySearchInputId}-data`);
    this.searchInputFullName = this.$autoCompleteSearchContainer.data('fullname');

    const inputOptions = options || {};
    this.options = {
      value: 'id',
      dataLimit: 1,
      ...inputOptions,
    };
    this.buildRemoteSource();
    this.buildAutoCompleteSearch();
  }

  /**
   * Change the remote url of the endpoint that returns suggestions.
   *
   * @param remoteUrl {string}
   */
  setRemoteUrl(remoteUrl) {
    this.entityRemoteSource.remote.url = remoteUrl;
  }

  /**
   * Force selected values, the input is an array of object that must match the format from
   * the API if you want the selected entities to be correctly displayed.
   *
   * @param values {array}
   */
  setValue(values) {
    this.clearSelectedItems();
    if (!values || values.length <= 0) {
      return;
    }

    values.each((value) => {
      this.appendSelectedItem(value);
    });
  }

  /**
   * Build the AutoCompleteSearch component
   */
  buildAutoCompleteSearch() {
    const autoSearchConfig = {
      source: this.entityRemoteSource,
      dataLimit: this.options.dataLimit,
      templates: {
        suggestion: (entity) => {
          let entityImage;

          if (Object.prototype.hasOwnProperty.call(entity, 'image')) {
            entityImage = `<img src="${entity.image}" /> `;
          }

          return `<div class="search-suggestion">${entityImage}${entity.name}</div>`;
        },
      },
      onClose: (event) => {
        this.onSelectionClose(event);
      },
      /* eslint-disable-next-line no-unused-vars */
      onSelect: (selectedItem, event) => {
        // When limit is one we cannot select additional elements so we replace them instead
        if (this.options.dataLimit === 1) {
          return this.replaceSelectedItem(selectedItem);
        }
        return this.appendSelectedItem(selectedItem);
      },
    };

    // Can be used to format value depending on selected item
    if (this.options.value !== undefined) {
      autoSearchConfig.value = this.options.value;
    }
    this.autoSearch = new AutoCompleteSearch(this.$entitySearchInput, autoSearchConfig);
  }

  /**
   * Build the Bloodhound remote source which will call the API. The placeholder to
   * inject the query search parameter is __QUERY__ (@todo: could be configurable)
   *
   * @returns {Bloodhound}
   */
  buildRemoteSource() {
    const sourceConfig = {
      mappingValue: this.$autoCompleteSearchContainer.data('mappingvalue'),
      remoteUrl: this.$autoCompleteSearchContainer.data('remoteurl'),
    };

    this.entityRemoteSource = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      identify(obj) {
        return obj[sourceConfig.mappingValue];
      },
      remote: {
        url: sourceConfig.remoteUrl,
        cache: false,
        wildcard: '__QUERY__',
        transform(response) {
          if (!response) {
            return [];
          }
          return response;
        },
      },
    });
  }

  /**
   * When an item is selected we empty the input search, since the selected data is stored in hidden inputs anyway
   *
   * @param event
   */
  onSelectionClose(event) {
    $(event.target).val('');
  }

  /**
   * Removes selected items.
   */
  clearSelectedItems() {
    const formIdItem = $('li', this.$selectionContainer);
    formIdItem.remove();
  }

  /**
   * When the component is configured to have only one selected element on each selection
   * the previous selection is removed and then replaced.
   *
   * @param selectedItem {Object}
   * @returns {boolean}
   */
  replaceSelectedItem(selectedItem) {
    this.clearSelectedItems();
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

    if (this.options.dataLimit !== 0 && formIdItem.length >= this.options.dataLimit) {
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

    if (typeof this.options.value === 'function') {
      value = this.options.value(selectedItem);
    } else {
      value = selectedItem[this.options.value];
    }

    const selectedHtml = this.renderSelected(selectedItem);
    // Hidden input is added into the selected li
    const $selectedNode = $(selectedHtml);
    const $hiddenInput = $(`<input type="hidden" name="${this.searchInputFullName}[data][]" value="${value}" />`);
    $selectedNode.append($hiddenInput);

    // Then the li is added to the list
    this.$selectionContainer.append($selectedNode);

    // Trigger the change so that listeners detect the form data has been modified
    $hiddenInput.trigger('change');
  }

  /**
   * Render the selected element, this will be appended in the selection list (ul),
   * no need to include the hidden input as it is automatically handled in addSelectedContentToContainer
   *
   * @param entity {Object}
   *
   * @returns {string}
   */
  renderSelected(entity) {
    // @todo: the tplcollection idea is not bad but it only contains a span for now, to fo to the end of this idea
    // it should contain the whole div (with media-left media-body and all)
    const $templateContainer = $(`#tplcollection-${this.entitySearchInputId}`);
    const innerTemplateHtml = $templateContainer
      .html()
      .replace('%s', entity.name);

    return `<li class="media">
        <div class="media-left">
          <img class="media-object image" src="${entity.image}" />
        </div>
        <div class="media-body media-middle">
          ${innerTemplateHtml}
        </div>
      </li>`;
  }
}
