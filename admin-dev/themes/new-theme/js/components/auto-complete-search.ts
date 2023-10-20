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

// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';

/**
 * This component is an overlay of typeahead it allows to have a single config input (since
 * typeahead weirdly uses two different configs). It also provides some default rendering
 * functions which are, of course, overridable.
 */

type DisplayFunction = (item: any) => string;

export interface TypeaheadJQueryDataset extends Twitter.Typeahead.Dataset<any> {
  display: string | DisplayFunction;
  value: string;
  limit: number;
  dataLimit: number;
  templates: any;
}

export interface TypeaheadJQueryOptions extends Twitter.Typeahead.Options {
  minLength: number,
  highlight: boolean,
  hint: boolean,
  onSelect: (
    selectedItem: any,
    event: JQueryEventObject,
    searchInput: JQuery
  ) => boolean;
  onClose: (event: JQueryEventObject, searchInput: JQuery) => void;
}

export type AutoCompleteSearchConfig = {
  minLength: number;
  highlight: boolean;
  hint: boolean;
  source: Bloodhound<Record<string, any>> | (
    (query: string, syncResults: (result: any[]) => void, asyncResults?: (result: any[]) => void
  ) => void);
  onSelect: (
    selectedItem: any,
    event: JQueryEventObject,
    searchInput: JQuery
  ) => boolean;
  onClose: (event: JQueryEventObject, searchInput: JQuery) => void;
  suggestionLimit: number;
  dataLimit: number;
  display: string | DisplayFunction;
  value: string;
  templates: any;
}
export type InputAutoCompleteSearchConfig = Partial<AutoCompleteSearchConfig> & {
  source: Bloodhound<Record<string, any>> | (
    (query: string, syncResults: (result: any[]) => void, asyncResults?: (result: any[]) => void
  ) => void); // source is mandatory option
};

export default class AutoCompleteSearch {
  private $searchInput: JQuery;

  private searchInputId: string;

  private config: AutoCompleteSearchConfig;

  constructor($searchInput: JQuery, inputConfig: Partial<InputAutoCompleteSearchConfig>) {
    this.$searchInput = $searchInput;
    this.searchInputId = this.$searchInput.prop('id');

    // Merging object works fine on one level, but on two it erases sub elements even if not present, so
    // we handle templates separately, these are the default rendering functions which can be overridden
    const defaultTemplates = {
      // Be careful that your rendering function must return HTML node not pure text so always include the
      // content in a div at least
      suggestion: (item: Record<string, string>) => {
        let displaySuggestion: Record<string, string> | string = item;

        if (typeof this.config.display === 'function') {
          displaySuggestion = this.config.display(item);
        } else if (
          Object.prototype.hasOwnProperty.call(
            item,
            <string> this.config.display,
          )
        ) {
          displaySuggestion = item[<string> this.config.display];
        }

        return `<div class="px-2">${displaySuggestion}</div>`;
      },
      pending(query: Record<string, string>) {
        return `<div class="px-2">Searching for "${query.query}"</div>`;
      },
      notFound(query: Record<string, string>) {
        return `<div class="px-2">No results found for "${query.query}"</div>`;
      },
    };

    // Merge default and input config
    this.config = <AutoCompleteSearchConfig>{
      minLength: 2,
      highlight: true,
      hint: false,
      onSelect: (
        selectedItem: any,
        event: JQueryEventObject,
        searchInput: JQuery,
      ): boolean => {
        searchInput.typeahead('val', selectedItem[this.config.value]);
        return true;
      },
      onClose(
        event: Event,
        searchInput: JQuery,
      ) {
        searchInput.typeahead('val', '');
        return true;
      },
      suggestionLimit: 30,
      dataLimit: 0,
      display: 'name',
      value: 'id',
      templates: defaultTemplates,
      ...inputConfig,
    };

    // If input has templates override me merge them with default ones
    if (Object.prototype.hasOwnProperty.call(inputConfig, 'templates')) {
      this.config.templates = {
        ...defaultTemplates,
        ...(<Record<string, unknown>>inputConfig.templates),
      };
    }

    this.buildTypeahead();
  }

  /**
   * Build the typeahead component based on provided configuration.
   */
  private buildTypeahead(): void {
    // Create the two config object for typeahead based on the full config
    const typeaheadOptions = {
      minLength: this.config.minLength,
      highlight: this.config.highlight,
      hint: this.config.hint,
      onSelect: this.config.onSelect,
      onClose: this.config.onClose,
    };

    const dataSetConfig = {
      source: this.config.source,
      display: this.config.display,
      value: this.config.value,
      limit: this.config.suggestionLimit,
      dataLimit: this.config.dataLimit,
      templates: this.config.templates,
    };

    /* eslint-disable */
    this.$searchInput
      .typeahead(<TypeaheadJQueryOptions>typeaheadOptions, <TypeaheadJQueryDataset>dataSetConfig)
      .bind('typeahead:select', (e: any, selectedItem: any) =>
        this.config.onSelect(selectedItem, e, this.$searchInput)
      )
      .bind('typeahead:close', (e: any) => {
        this.config.onClose(e, this.$searchInput);
      });
    /* eslint-enable */
  }
}
