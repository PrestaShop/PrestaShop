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
import ConfirmModal from '@components/modal';
import Bloodhound from 'typeahead.js';

/**
 * This component is used to search and select one or several entities, it uses the AutoSearchComplete
 * component which displays a list of suggestion based on an API returned response. Then when
 * an element is selected it is added to the selection container and hidden inputs are created to
 * send an array of entity IDs in the form request.
 *
 * This component is used with EntitySearchInputType forms, and is tightly linked to the content of this
 * twig file src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/entity_search_input.html.twig
 *
 * The default content of the collection is an EntityItemType with a simple default template but you can
 * either override it in a theme or create your own entity type if you need to customize the behaviour.
 */
export default class EntitySearchInput {
  constructor($entitySearchInputContainer, options) {
    this.options = {};
    this.$entitySearchInputContainer = $entitySearchInputContainer;
    this.buildOptions(options);

    this.$entitySearchInput = $(this.options.searchInputSelector, this.$entitySearchInputContainer);
    this.$selectionContainer = $(this.options.listSelector, this.$entitySearchInputContainer);

    this.buildRemoteSource();
    this.buildAutoCompleteSearch();
    this.buildActions();

    return {
      setRemoteUrl: (remoteUrl) => this.setRemoteUrl(remoteUrl),
      setValue: (values) => this.setValue(values),
      setOption: (optionName, value) => this.setOption(optionName, value),
    };
  }

  /**
   * Change the remote url of the endpoint that returns suggestions.
   *
   * @param remoteUrl {string}
   */
  setRemoteUrl(remoteUrl) {
    this.options.remoteUrl = remoteUrl;
    this.entityRemoteSource.remote.url = this.options.remoteUrl;
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
   * @param {string} optionName
   * @param {*} value
   */
  setOption(optionName, value) {
    this.options[optionName] = value;
  }

  /**
   * @param {Object} options
   */
  buildOptions(options) {
    const inputOptions = options || {};
    this.initOption('mappingValue', inputOptions, 'id');
    this.initOption('mappingDisplay', inputOptions, 'name');
    this.initOption('mappingImage', inputOptions, 'image');

    this.initOption('prototypeTemplate', inputOptions);
    this.initOption('prototypeName', inputOptions, '__name__');
    this.initOption('prototypeImage', inputOptions, '__image__');
    this.initOption('prototypeValue', inputOptions, '__value__');
    this.initOption('prototypeDisplay', inputOptions, '__display__');

    this.initOption('allowDelete', inputOptions, true);
    this.initOption('dataLimit', inputOptions, 0);
    this.initOption('remoteUrl', inputOptions);

    this.initOption('removeModalTitle', inputOptions);
    this.initOption('removeModalMessage', inputOptions);
    this.initOption('removeModalApply', inputOptions);
    this.initOption('removeModalCancel', inputOptions);

    // Most of the previous config are configurable via the EntitySearchInputForm options, the following ones are only
    // overridable via js config (as long as you use the default template)
    this.initOption('searchInputSelector', inputOptions, '.entity-search-input');
    this.initOption('listSelector', inputOptions, '.entities-list');
    this.initOption('removeModalId', inputOptions, 'modal-confirm-remove-entity');
    this.initOption('confirmButtonClass', inputOptions, 'btn-danger');
    this.initOption('queryWildcard', inputOptions, '__QUERY__');
    this.initOption('entityItemSelector', inputOptions, 'li.entity-item');
    this.initOption('entityDeleteSelector', inputOptions, '.entity-item-delete');
  }

  /**
   * Init the option value, the input config has the more priority. It overrides the data attribute option
   * (if present), finally a default value is used (if defined).
   *
   * @param {string} optionName
   * @param {Object} inputOptions
   * @param {*|undefined} defaultOption
   */
  initOption(optionName, inputOptions, defaultOption = undefined) {
    if (Object.prototype.hasOwnProperty.call(inputOptions, optionName)) {
      this.options[optionName] = inputOptions[optionName];
    } else if (typeof this.$entitySearchInputContainer.data(optionName) !== 'undefined') {
      this.options[optionName] = this.$entitySearchInputContainer.data(optionName);
    } else {
      this.options[optionName] = defaultOption;
    }
  }

  buildActions() {
    $(this.$selectionContainer).on('click', this.options.entityDeleteSelector, (event) => {
      if (!this.options.allowDelete) {
        return;
      }

      const $entity = $(event.target).closest(this.options.entityItemSelector);
      const modal = new ConfirmModal(
        {
          id: this.options.removeModalId,
          confirmTitle: this.options.removeModalTitle,
          confirmMessage: this.options.removeModalMessage,
          confirmButtonLabel: this.options.removeModalApply,
          closeButtonLabel: this.options.removeModalCancel,
          confirmButtonClass: this.options.confirmButtonClass,
          closable: true,
        },
        () => {
          const $hiddenInput = $('input[type="hidden"]', $entity);
          $entity.remove();
          $hiddenInput.trigger('change');
          this.$selectionContainer.trigger('change');

          if (typeof this.options.onRemovedContent !== 'undefined') {
            this.options.onRemovedContent($entity);
          }
        },
      );
      modal.show();
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
    if (this.options.mappingValue !== undefined) {
      autoSearchConfig.value = this.options.mappingValue;
    }
    this.autoSearch = new AutoCompleteSearch(this.$entitySearchInput, autoSearchConfig);
  }

  /**
   * Build the Bloodhound remote source which will call the API. The placeholder to
   * inject the query search parameter is __QUERY__
   *
   * @returns {Bloodhound}
   */
  buildRemoteSource() {
    const sourceConfig = {
      mappingValue: this.options.mappingValue,
      remoteUrl: this.options.remoteUrl,
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
        wildcard: this.options.queryWildcard,
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
   * Removes selected items.
   */
  clearSelectedItems() {
    this.$selectionContainer.empty();
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
    const $entityItems = $(this.options.entityItemSelector, this.$selectionContainer);

    if (this.options.dataLimit !== 0 && $entityItems.length >= this.options.dataLimit) {
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
    const newIndex = this.$selectionContainer.children().length;
    const selectedHtml = this.renderSelected(selectedItem, newIndex);

    const $selectedNode = $(selectedHtml);
    const $hiddenInput = $('input[type="hidden"]', $selectedNode);

    const $entityDelete = $(this.options.entityDeleteSelector, $selectedNode);
    $entityDelete.toggle(this.options.allowDelete);

    this.$selectionContainer.append($selectedNode);

    // Trigger the change so that listeners detect the form data has been modified
    $hiddenInput.trigger('change');

    if (typeof this.options.onSelectedContent !== 'undefined') {
      this.options.onSelectedContent($selectedNode, selectedItem);
    }
  }

  /**
   * Render the selected element, this will be appended in the selection list (ul),
   * no need to include the hidden input as it is automatically handled in addSelectedContentToContainer
   *
   * @param {Object} entity
   * @param {int} index
   *
   * @returns {string}
   */
  renderSelected(entity, index) {
    const value = entity[this.options.mappingValue] || 0;
    const display = entity[this.options.mappingDisplay] || '';
    const image = entity[this.options.mappingImage] || '';

    return this.options.prototypeTemplate
      .replace(new RegExp(this.options.prototypeName, 'g'), index)
      .replace(new RegExp(this.options.prototypeValue, 'g'), value)
      .replace(new RegExp(this.options.prototypeImage, 'g'), image)
      .replace(new RegExp(this.options.prototypeDisplay, 'g'), display);
  }
}
