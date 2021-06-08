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
  $entitySearchInputContainer: JQuery;

  $entitySearchInput: JQuery;

  $selectionContainer: JQuery;

  entitySearchInputId: string;

  searchInputFullName: string;

  options: OptionsObject;

  entityRemoteSource: any;

  autoSearch: any;

  constructor($entitySearchInputContainer: JQuery, options: OptionsObject) {
    this.$entitySearchInputContainer = $entitySearchInputContainer;
    this.$entitySearchInput = $('.entity-search-input', this.$entitySearchInputContainer);
    this.$selectionContainer = $('.entities-list', this.$entitySearchInputContainer);

    this.mappingValue = this.$entitySearchInputContainer.data('mappingValue');
    this.mappingDisplay = this.$entitySearchInputContainer.data('mappingDisplay');
    this.mappingImage = this.$entitySearchInputContainer.data('mappingImage');

    this.prototypeTemplate = this.$entitySearchInputContainer.data('prototype');
    this.prototypeName = this.$entitySearchInputContainer.data('prototypeName');
    this.prototypeImage = this.$entitySearchInputContainer.data('prototypeImage');
    this.prototypeValue = this.$entitySearchInputContainer.data('prototypeValue');
    this.prototypeDisplay = this.$entitySearchInputContainer.data('prototypeDisplay');

    this.searchInputFullName = this.$entitySearchInputContainer.data('fullName');
    this.dataLimit = this.$entitySearchInputContainer.data('limit');
    this.remoteUrl = this.$entitySearchInputContainer.data('remoteUrl');

    this.removeModalTitle = this.$entitySearchInputContainer.data('removeModalTitle');
    this.removeModalMessage = this.$entitySearchInputContainer.data('removeModalMessage');
    this.removeModalApply = this.$entitySearchInputContainer.data('removeModalApply');
    this.removeModalCancel = this.$entitySearchInputContainer.data('removeModalCancel');

    const inputOptions = options || {};
    this.options = {
      value: 'id',
      dataLimit: 1,
      ...inputOptions,
    };
    this.entityRemoteSource = {};
    this.buildRemoteSource();
    this.buildAutoCompleteSearch();
    this.buildActions();

    return {
      setRemoteUrl: (remoteUrl) => this.setRemoteUrl(remoteUrl),
      setValue: (values) => this.setValue(values),
    };
  }

  /**
   * Change the remote url of the endpoint that returns suggestions.
   *
   * @param remoteUrl {string}
   */
  setRemoteUrl(remoteUrl: string): void {
    this.remoteUrl = remoteUrl;
    this.entityRemoteSource.remote.url = this.remoteUrl;
  }

  /**
   * Force selected values, the input is an array of object that must match the format from
   * the API if you want the selected entities to be correctly displayed.
   *
   * @param values {array}
   */
  setValue(values: array): void {
    this.clearSelectedItems();
    if (!values || values.length <= 0) {
      return;
    }

    values.each((index: number, value: any) => {
      this.appendSelectedItem(value);
    });
  }

  buildActions() {
    $(this.$selectionContainer).on('click', '.entity-item-delete', (event) => {
      const $entity = $(event.target).closest('.entity-item');
      const modal = new ConfirmModal(
        {
          id: 'modal-confirm-remove-entity',
          confirmTitle: this.removeModalTitle,
          confirmMessage: this.removeModalMessage,
          confirmButtonLabel: this.removeModalApply,
          closeButtonLabel: this.removeModalCancel,
          confirmButtonClass: 'btn-danger',
          closable: true,
        },
        () => {
          const $hiddenInput = $('input[type="hidden"]', $entity);
          $entity.remove();
          $hiddenInput.trigger('change');
          this.$selectionContainer.trigger('change');
        },
      );
      modal.show();
    });
  }

  /**
   * Build the AutoCompleteSearch component
   */
  buildAutoCompleteSearch(): void {
    const autoSearchConfig = {
      source: this.entityRemoteSource,
      dataLimit: this.dataLimit,
      value: '',
      templates: {
        suggestion: (entity: OptionsObject) => {
          let entityImage;

          if (Object.prototype.hasOwnProperty.call(entity, 'image')) {
            entityImage = `<img src="${entity.image}" /> `;
          }

          return `<div class="search-suggestion">${entityImage}${entity.name}</div>`;
        },
      },
      /* eslint-disable-next-line no-unused-vars */
      onSelect: (selectedItem: OptionsObject, event: JQueryEventObject) => {
        // When limit is one we cannot select additional elements so we replace them instead
        if (this.dataLimit === 1) {
          return this.replaceSelectedItem(selectedItem);
        }
        return this.appendSelectedItem(selectedItem);
      },
    };

    // Can be used to format value depending on selected item
    if (this.mappingValue !== undefined) {
      autoSearchConfig.value = <string> this.mappingValue;
    }
    this.autoSearch = new AutoCompleteSearch(
      this.$entitySearchInput,
      autoSearchConfig,
    );
  }

  /**
   * Build the Bloodhound remote source which will call the API. The placeholder to
   * inject the query search parameter is __QUERY__ (@todo: could be configurable)
   *
   * @returns {Bloodhound}
   */
  buildRemoteSource(): void {
    const sourceConfig = {
      mappingValue: this.mappingValue,
      remoteUrl: this.remoteUrl,
    };

    this.entityRemoteSource = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      identify(obj: any) {
        return obj[sourceConfig.mappingValue];
      },
      remote: {
        url: sourceConfig.remoteUrl,
        cache: false,
        wildcard: '__QUERY__',
        transform(response: any) {
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
  clearSelectedItems(): void {
    this.$selectionContainer.empty();
  }

  /**
   * When the component is configured to have only one selected element on each selection
   * the previous selection is removed and then replaced.
   *
   * @param selectedItem {Object}
   * @returns {boolean}
   */
  replaceSelectedItem(selectedItem: unknown): boolean {
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
  appendSelectedItem(selectedItem: unknown): boolean {
    // If collection length is up to limit, return
    const formIdItem = $('li', this.$selectionContainer);

    if (this.dataLimit !== 0 && formIdItem.length >= this.dataLimit) {
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
  addSelectedContentToContainer(selectedItem: unknown) {
    const newIndex = this.$selectionContainer.children().length;
    const selectedHtml = this.renderSelected(selectedItem, newIndex);

    const $selectedNode = $(selectedHtml);
    const $hiddenInput = $('input[type="hidden"]', $selectedNode);
    this.$selectionContainer.append($selectedNode);

    // Trigger the change so that listeners detect the form data has been modified
    $hiddenInput.trigger('change');
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
  renderSelected(entity: unknown, index: int): string {
    const value = entity[this.mappingValue] || 0;
    const display = entity[this.mappingDisplay] || '';
    const image = entity[this.mappingImage] || '';

    return this.prototypeTemplate
      .replace(new RegExp(this.prototypeName, 'g'), index)
      .replace(new RegExp(this.prototypeValue, 'g'), value)
      .replace(new RegExp(this.prototypeImage, 'g'), image)
      .replace(new RegExp(this.prototypeDisplay, 'g'), display);
  }
}
