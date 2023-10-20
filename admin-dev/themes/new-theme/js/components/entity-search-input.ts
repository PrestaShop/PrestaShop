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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import AutoCompleteSearch, {InputAutoCompleteSearchConfig} from '@components/auto-complete-search';
import ComponentsMap from '@components/components-map';
import ConfirmModal from '@components/modal';
// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';
import {isUndefined} from '@PSTypes/typeguard';

const EntitySearchInputMap = ComponentsMap.entitySearchInput;

type RemoveFunction = (item: any) => void;
type SelectFunction = ($node: JQuery, item: any) => void;
type SuggestionFunction = (entity: any) => string;
export interface EntitySearchInputOptions extends OptionsObject {
  prototypeTemplate: string,
  prototypeIndex: string,
  prototypeMapping: OptionsObject,
  identifierField: string;
  allowDelete: boolean,
  dataLimit: number,
  minLength: number,
  remoteUrl: string,
  filterSelected: boolean,
  filteredIdentities: Array<string>,
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
  suggestionTemplate: SuggestionFunction | undefined,
  extraQueryParams?: () => Record<string, string>,
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
  private readonly $entitySearchInputContainer: JQuery;

  private readonly $entitySearchInput: JQuery;

  private readonly $entitiesContainer: JQuery;

  private $listContainer: JQuery;

  private $emptyState: JQuery;

  private readonly options!: EntitySearchInputOptions;

  private entityRemoteSource!: Bloodhound;

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
      suggestionField: 'name',
      prototypeTemplate: undefined,
      prototypeIndex: '__index__',
      prototypeMapping: {
        id: '__id__',
        name: '__name__',
        image: '__image__',
      },
      identifierField: 'id',
      allowDelete: true,
      dataLimit: 0,
      minLength: 2,
      remoteUrl: undefined,
      filterSelected: true,
      filteredIdentities: [],

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
      responseTransformer: (response: any) => response || [],

      // Template function
      suggestionTemplate: undefined,
      extraQueryParams: undefined,
    };

    Object.keys(defaultOptions).forEach((optionName) => {
      // This gets the proper value for each option, respecting the priority: input > data-attribute > default
      this.initOption(optionName, inputOptions, defaultOptions[optionName]);
    });

    // Cast all IDs into string to avoid not matching because of different types
    this.options.filteredIdentities = this.options.filteredIdentities.map(String);
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
    //'!!' converts option to bool (because if its 1 or 0, jquery toggle works differently than with true/false)
    $entityDelete.toggle(!!this.options.allowDelete);
  }

  /**
   * Build the AutoCompleteSearch component
   */
  private buildAutoCompleteSearch(): void {
    const autoSearchConfig: InputAutoCompleteSearchConfig = {
      source: this.entityRemoteSource,
      dataLimit: this.options.dataLimit,
      value: this.options.identifierField,
      minLength: this.options.minLength,
      templates: {
        suggestion: (entity: any) => this.showSuggestion(entity),
      },
      onSelect: (selectedItem: any) => {
        // When limit is one we cannot select additional elements so we replace them instead
        if (this.options.dataLimit === 1) {
          return this.replaceSelectedItem(selectedItem);
        }
        return this.appendSelectedItem(selectedItem);
      },
    };

    // The search feature may be disabled so the search input won't be present
    if (this.$entitySearchInput.length) {
      this.autoSearch = new AutoCompleteSearch(
        this.$entitySearchInput,
        autoSearchConfig,
      );
    }
  }

  private showSuggestion(entity: any): string {
    if (!isUndefined(this.options.suggestionTemplate)) {
      return this.options.suggestionTemplate(entity);
    }

    let entityImage = '';

    if (Object.prototype.hasOwnProperty.call(entity, 'image')) {
      entityImage = `<img src="${entity.image}" /> `;
    }

    return `<div class="search-suggestion">${entityImage}${entity[this.options.suggestionField]}</div>`;
  }

  /**
   * Build the Bloodhound remote source which will call the API. The placeholder to
   * inject the query search parameter is __QUERY__
   *
   * @returns {Bloodhound}
   */
  private buildRemoteSource(): void {
    this.entityRemoteSource = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      identify(obj: any) {
        return obj[this.options.identifierField];
      },
      remote: {
        url: this.options.remoteUrl,
        replace: (query: string, searchPhrase: string) => {
          // need to replace wildcard manually, because here we are overriding the default replace function
          const url = query.replace(this.options.queryWildcard, searchPhrase);

          if (!isUndefined(this.options.extraQueryParams)) {
            // this allows appending extra parameters to the query, such as shopId
            const extraParams = this.options.extraQueryParams();
            const encodedExtraParams = Object
              .keys(extraParams)
              .map((key: string) => `${key}=${encodeURIComponent(extraParams[key])}`)
              .join('&');

            return `${url}&${encodedExtraParams}`;
          }

          return url;
        },
        cache: false,
        transform: (response: any) => {
          if (!response) {
            return [];
          }
          const transformedResponse = this.options.responseTransformer(response);
          const selectedIds: string[] = this.getSelectedIds();
          const suggestedItems: any[] = [];
          transformedResponse.forEach((responseItem: any) => {
            // Force casting to string to avoid inequality with number IDs because of type
            const responseIdentifier: string = String(responseItem[this.options.identifierField]);
            const isIdContained = this.options.filterSelected && selectedIds.includes(responseIdentifier);
            const isFiltered = this.options.filteredIdentities.includes(responseIdentifier);

            if (!isIdContained && !isFiltered) {
              suggestedItems.push(responseItem);
            }
          });

          return suggestedItems;
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
    const $entityItems = $(this.options.entityItemSelector, this.$entitiesContainer);
    const newIndex = $entityItems.length ? this.getIndexFromItem($entityItems.last()) + 1 : 0;
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
   * Try and find the index of an element in the collection by parsing its inputs names which should look like:
   *
   * form[collection][0][name], form[collection][1][id] => we aim to extract the 1
   *
   * We search for the name matching the configured identifier, and extract its index. This is important because
   * when you edit a collection, you can add then remove then add an element again, the indexes are not gonna follow
   * so you cannot rely on just the index from element order. Which is why it is more accurate to parse the index that
   * was used when the element has been rendered for the first time.
   *
   * If we can't find anything we use the order index as fallback though.
   *
   * @param {JQuery} $item
   *
   * @return number
   */
  private getIndexFromItem($item: JQuery): number {
    // By default use the position index
    let index = $item.index();

    // Try to find an input which names contains [1][id] (where 1 is the index, and id the identifier)
    const identifierNameRegexp: string = `\\[(\\d+)\\]\\[${this.options.identifierField}\\]`;
    const inputs = $item.find('input');
    inputs.each((inputIndex: number, input: HTMLInputElement): void => {
      const matches = input.name.match(identifierNameRegexp);

      // Extract the index from the input name, if it is found and is a number use it as the index
      if (matches && matches.length > 0) {
        const foundIndex = parseInt(matches[1], 10);

        if (!Number.isNaN(foundIndex)) {
          index = foundIndex;
        }
      }
    });

    return index;
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

  /**
   * Parses the selection container and extract the IDs this allows to filter the already selected items.
   *
   * @private
   */
  private getSelectedIds(): string[] {
    const selectedIds: string[] = [];
    const selectedChildren = $(this.options.entityItemSelector, this.$entitiesContainer);
    selectedChildren.each((index: number, selectedChild: HTMLElement) => {
      const identifierNameRegexp: string = `\\[${this.options.identifierField}\\]`;
      const inputs = $(selectedChild).find('input');
      inputs.each((inputIndex: number, input: HTMLInputElement): void => {
        if (input.name.match(identifierNameRegexp)) {
          selectedIds.push(input.value);
        }
      });
    });

    return selectedIds;
  }
}
