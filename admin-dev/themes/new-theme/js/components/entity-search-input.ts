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
import ComponentsMap from '@components/components-map';
import ConfirmModal from '@components/modal';
// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';

const EntitySearchInputMap = ComponentsMap.entitySearchInput;

type RemoveFunction = (item: any) => void;
type SelectFunction = ($node: JQuery, item: any) => void;
export interface EntitySearchInputOptions extends OptionsObject {
  prototypeTemplate: string,
  prototypeIndex: string,
  prototypeMapping: OptionsObject,

  allowDelete: boolean,
  dataLimit: number,
  remoteUrl: string,

  removeModal: ModalOptions,

  searchInputSelector: string,
  entitiesContainerSelector: string,
  listContainerSelector: string,
  entityItemSelector: string,
  entityDeleteSelector: string,
  emptyStateSelector: string,
  queryWildcard: string,

  onRemovedContent: RemoveFunction | undefined,
  onSelectedContent: SelectFunction | undefined,
}
export interface ModalOptions extends OptionsObject {
  id: string;
  title: string;
  message: string;
  apply: string;
  cancel: string;
  buttonClass: string;
}

/**
 * This component is used to search and select one or several entities, it uses the AutoSearchComplete
 * component which displays a list of suggestion based on an API returned response. Then when
 * an element is selected it is added to the selection container relying on the prototype template provided.
 *
 * This component is used with EntitySearchInputType forms, and is tightly linked to the content of this
 * twig file src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/entity_search_input.html.twig
 *
 * The default content of the collection is an EntityItemType with a simple default template but you can
 * either override it in a theme or create your own entity type if you need to customize the behaviour.
 */
export default class EntitySearchInput {
  private $entitySearchInputContainer: JQuery;

  private $entitySearchInput: JQuery;

  private $entitiesContainer: JQuery;

  private $listContainer: JQuery;

  private $emptyState: JQuery;

  private options!: EntitySearchInputOptions;

  private entityRemoteSource?: Bloodhound<Record<string, any>>;

  private autoSearch!: AutoCompleteSearch;

  constructor($entitySearchInputContainer: JQuery, options: OptionsObject) {
    this.$entitySearchInputContainer = $entitySearchInputContainer;
    this.options = <EntitySearchInputOptions>{};
    this.buildOptions(options);

    this.$entitySearchInput = $(this.options.searchInputSelector, this.$entitySearchInputContainer);
    this.$entitiesContainer = $(this.options.entitiesContainerSelector, this.$entitySearchInputContainer);
    this.$listContainer = $(this.options.listContainerSelector, this.$entitySearchInputContainer);
    this.$emptyState = $(this.options.emptyStateSelector, this.$entitySearchInputContainer);

    this.buildRemoteSource();
    this.buildAutoCompleteSearch();
    this.buildActions();
    this.updateEmptyState();
  }

  /**
   * Force selected values, the input is an array of object that must match the format from
   * the API if you want the selected entities to be correctly displayed.
   *
   * @param values {Array<any>}
   */
  setValues(values: any[]): void {
    this.clearSelectedItems();
    if (!values || values.length <= 0) {
      return;
    }

    values.forEach((index: number, value: any) => {
      this.appendSelectedItem(value);
    });
  }

  /**
   * Append the item to the selection, respecting the configured limit so if limit is already reached the item is not
   * added.
   *
   * @param newItem
   *
   * @return boolean
   */
  addItem(newItem: any): boolean {
    return this.appendSelectedItem(newItem);
  }

  /**
   * @param optionName
   */
  getOption(optionName: string): any {
    return this.options[optionName];
  }

  /**
   * @param {string} optionName
   * @param {unknown} value
   */
  setOption(optionName: string, value: unknown): void {
    this.options[optionName] = value;

    // Apply special options to components when needed
    if (optionName === 'remoteUrl' && this.entityRemoteSource) {
      (<Record<string, any>> this.entityRemoteSource).remote.url = this.options.remoteUrl;
    }
  }

  /**
   * @param {Object} options
   */
  private buildOptions(options: OptionsObject): void {
    const inputOptions = options || {};
    const defaultOptions: OptionsObject = {
      prototypeTemplate: undefined,
      prototypeIndex: '__index__',
      prototypeMapping: {
        id: '__id__',
        name: '__name__',
        image: '__image__',
      },

      allowDelete: true,
      dataLimit: 0,
      remoteUrl: undefined,

      removeModal: {
        id: 'modal-confirm-remove-entity',
        title: 'Delete item',
        message: 'Are you sure you want to delete this item?',
        apply: 'Delete',
        cancel: 'Cancel',
        buttonClass: 'btn-danger',
      },

      // Most of the previous config are configurable via the EntitySearchInputForm options, the following ones are only
      // overridable via js config (as long as you use the default template)
      searchInputSelector: EntitySearchInputMap.searchInputSelector,
      entitiesContainerSelector: EntitySearchInputMap.entitiesContainerSelector,
      listContainerSelector: EntitySearchInputMap.listContainerSelector,
      entityItemSelector: EntitySearchInputMap.entityItemSelector,
      entityDeleteSelector: EntitySearchInputMap.entityDeleteSelector,
      emptyStateSelector: EntitySearchInputMap.emptyStateSelector,
      queryWildcard: '__QUERY__',

      // These are configurable callbacks
      onRemovedContent: undefined,
      onSelectedContent: undefined,
    };

    Object.keys(defaultOptions).forEach((optionName) => {
      // This gets the proper value for each option, respecting the priority: input > data-attribute > default
      this.initOption(optionName, inputOptions, defaultOptions[optionName]);
    });
  }

  /**
   * Init the option value, the input config has the more priority. It overrides the data attribute option
   * (if present), finally a default value is used (if defined).
   *
   * @param {string} optionName
   * @param {Object} inputOptions
   * @param {*|undefined} defaultOption
   */
  private initOption(optionName: string, inputOptions: OptionsObject, defaultOption: any = undefined): void {
    if (Object.prototype.hasOwnProperty.call(inputOptions, optionName)) {
      this.options[optionName] = inputOptions[optionName];
    } else if (typeof this.$entitySearchInputContainer.data(optionName) !== 'undefined') {
      this.options[optionName] = this.$entitySearchInputContainer.data(optionName);
    } else {
      this.options[optionName] = defaultOption;
    }
  }

  private buildActions(): void {
    // Always check for click even if it is useless when allowDelete options is false, it can be changed dynamically
    $(this.$entitiesContainer).on('click', this.options.entityDeleteSelector, (event) => {
      if (!this.options.allowDelete) {
        return;
      }

      const $entity = $(event.target).closest(this.options.entityItemSelector);

      const modal = new (ConfirmModal as any)(
        {
          id: this.options.removeModal.id,
          confirmTitle: this.options.removeModal.title,
          confirmMessage: this.options.removeModal.message,
          closeButtonLabel: this.options.removeModal.cancel,
          confirmButtonLabel: this.options.removeModal.apply,
          confirmButtonClass: this.options.removeModal.buttonClass,
          closable: true,
        },
        () => {
          $entity.remove();
          this.updateEmptyState();
          if (typeof this.options.onRemovedContent !== 'undefined') {
            this.options.onRemovedContent($entity);
          }
        },
      );
      modal.show();
    });

    // For now adapt the display based on the allowDelete option
    const $entityDelete = $(this.options.entityDeleteSelector, this.$entitiesContainer);
    $entityDelete.toggle(!!this.options.allowDelete);
  }

  /**
   * Build the AutoCompleteSearch component
   */
  private buildAutoCompleteSearch(): void {
    const autoSearchConfig = {
      source: this.entityRemoteSource,
      dataLimit: this.options.dataLimit,
      value: '',
      templates: {
        suggestion: (entity: any) => {
          let entityImage = '';

          if (Object.prototype.hasOwnProperty.call(entity, 'image')) {
            entityImage = `<img src="${entity.image}" /> `;
          }

          return `<div class="search-suggestion">${entityImage}${entity.name}</div>`;
        },
      },
      onSelect: (selectedItem: any) => {
        // When limit is one we cannot select additional elements so we replace them instead
        if (this.options.dataLimit === 1) {
          return this.replaceSelectedItem(selectedItem);
        }
        return this.appendSelectedItem(selectedItem);
      },
    };

    // Can be used to format value depending on selected item
    if (this.options.mappingValue !== undefined) {
      autoSearchConfig.value = <string> this.options.mappingValue;
    }

    // The search feature may be disabled so the search input won't be present
    if (this.$entitySearchInput.length) {
      this.autoSearch = new AutoCompleteSearch(
        this.$entitySearchInput,
        autoSearchConfig,
      );
    }
  }

  /**
   * Build the Bloodhound remote source which will call the API. The placeholder to
   * inject the query search parameter is __QUERY__
   *
   * @returns {Bloodhound}
   */
  private buildRemoteSource(): void {
    const sourceConfig = {
      mappingValue: this.options.mappingValue,
      remoteUrl: this.options.remoteUrl,
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
        wildcard: this.options.queryWildcard,
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
  private clearSelectedItems(): void {
    this.$entitiesContainer.empty();
    this.updateEmptyState();
  }

  /**
   * When the component is configured to have only one selected element on each selection
   * the previous selection is removed and then replaced.
   *
   * @param selectedItem {Object}
   * @returns {boolean}
   */
  private replaceSelectedItem(selectedItem: any): boolean {
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
  private appendSelectedItem(selectedItem: any): boolean {
    // If collection length is up to limit, return
    const $entityItems = $(this.options.entityItemSelector, this.$entitiesContainer);

    if (this.options.dataLimit !== 0 && $entityItems.length >= this.options.dataLimit) {
      return false;
    }

    this.addSelectedContentToContainer(selectedItem);

    return true;
  }

  private updateEmptyState(): void {
    const $entityItems = $(this.options.entityItemSelector, this.$entitiesContainer);
    this.$emptyState.toggle($entityItems.length === 0);
    this.$listContainer.toggle($entityItems.length !== 0);
  }

  /**
   * Add the selected content to the selection container, the HTML is generated based on the render that relies on the
   * prototype template and mapping, and finally the rendered selection is added to the list.
   *
   * @param {Object} selectedItem
   */
  private addSelectedContentToContainer(selectedItem: any): void {
    const newIndex = this.$entitiesContainer.children().length;
    const selectedHtml = this.renderSelected(selectedItem, newIndex);

    const $selectedNode = $(selectedHtml);
    const $entityDelete = $(this.options.entityDeleteSelector, $selectedNode);
    $entityDelete.toggle(!!this.options.allowDelete);

    this.$entitiesContainer.append($selectedNode);

    if (typeof this.options.onSelectedContent !== 'undefined') {
      this.options.onSelectedContent($selectedNode, selectedItem);
    }
    this.updateEmptyState();
  }

  /**
   * Render the selected element, this will be appended in the selection list (ul), prototypeTemplate is used as the
   * base the we rely on prototypeMapping to replace every placeholders in the template by their mapping value in the
   * provided entity.
   *
   * @param {Object} entity
   * @param {number} index
   *
   * @returns {string}
   */
  private renderSelected(entity: any, index: number): string {
    let template = this.options.prototypeTemplate.replace(new RegExp(this.options.prototypeIndex, 'g'), String(index));

    Object.keys(this.options.prototypeMapping).forEach((fieldName) => {
      const fieldValue = entity[fieldName] || '';
      template = template.replace(new RegExp(this.options.prototypeMapping[fieldName], 'g'), fieldValue);
    });

    return template;
  }
}
