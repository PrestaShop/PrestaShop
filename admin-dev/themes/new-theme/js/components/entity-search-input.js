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

import AutoCompleteSearch from "@components/auto-complete-search";
import Bloodhound from "typeahead.js";

export default class EntitySearchInput {
  constructor($entitySearchInput, options) {
    this.$entitySearchInput = $entitySearchInput;
    this.entitySearchInputId = this.$entitySearchInput.prop('id');
    this.$autoCompleteSearchContainer = this.$entitySearchInput.closest('.autocomplete-search');

    options = options || {};
    this.buildRemoteSource();
    this.buildAutoCompleteSearch(options);
  }

  buildAutoCompleteSearch(options) {
    const dataSetConfig = {
      source: this.entityRemoteSource,
      dataLimit: 1,
      templates: {
        renderSelected: (entity) => this.renderSelected(entity),
      }
    };
    if (options.value !== undefined) {
      dataSetConfig.value = options;
    }
    this.autoSearch = new AutoCompleteSearch(this.$entitySearchInput, {}, dataSetConfig);
  }

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

  renderSelected(entity) {
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
