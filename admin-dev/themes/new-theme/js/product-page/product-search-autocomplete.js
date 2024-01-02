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
import Bloodhound from 'typeahead.js';

export default function () {
  $(() => {
    $('.autocomplete-search').each(function () {
      loadAutocomplete($(this), false);
    });

    $('.autocomplete-search').on('buildTypeahead', function () {
      loadAutocomplete($(this), true);
    });
  });

  function loadAutocomplete(object, reset) {
    const autocompleteObject = $(object);
    const autocompleteFormId = autocompleteObject.attr('data-formid');
    const formId = `#${autocompleteFormId}-data .delete`;
    const autocompleteSource = `${autocompleteFormId}_source`;

    if (reset === true) {
      $(`#${autocompleteFormId}`).typeahead('destroy');
    }

    $(document).on('click', formId, (e) => {
      e.preventDefault();

      window.modalConfirmation.create(
        window.translate_javascripts['Are you sure you want to delete this item?'],
        null,
        {
          onContinue: () => {
            $(e.target).parents('.media').remove();

            // Save current product after its related product has been removed
            $('#submit').click();
          },
        }).show();
    });

    document[autocompleteSource] = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      identify(obj) {
        return obj[autocompleteObject.attr('data-mappingvalue')];
      },
      remote: {
        url: autocompleteObject.attr('data-remoteurl'),
        cache: false,
        wildcard: '%QUERY',
        transform(response) {
          if (!response) {
            return [];
          }
          return response;
        },
      },
    });

    // define typeahead
    $(`#${autocompleteFormId}`).typeahead({
      limit: 20,
      minLength: 2,
      highlight: true,
      cache: false,
      hint: false,
    }, {
      display: autocompleteObject.attr('data-mappingname'),
      source: document[`${autocompleteFormId}_source`],
      limit: 30,
      templates: {
        suggestion(item) {
          return `<div><img src="${item.image}" style="width:50px" /> ${item.name}</div>`;
        },
      },
    }).on('typeahead:select', (e, suggestion) => {
      // if collection length is up to limit, return

      const formIdItem = $(`#${autocompleteFormId}-data li`);
      const autocompleteFormLimit = parseInt(autocompleteObject.attr('data-limit'), 10);

      if (autocompleteFormLimit !== 0 && formIdItem.length >= autocompleteFormLimit) {
        return false;
      }

      let value = suggestion[autocompleteObject.attr('data-mappingvalue')];

      if (suggestion.id_product_attribute) {
        value = `${value},${suggestion.id_product_attribute}`;
      }

      const tplcollection = $(`#tplcollection-${autocompleteFormId}`);
      const tplcollectionHtml = tplcollection
        .html()
        .replace('%s', suggestion[autocompleteObject.attr('data-mappingname')]);

      const html = `<li class="media">
        <div class="media-left">
          <img class="media-object image" src="${suggestion.image}" />
        </div>
        <div class="media-body media-middle">
          ${tplcollectionHtml}
        </div>
        <input type="hidden" name="${autocompleteObject.attr('data-fullname')}[data][]" value="${value}" />
      </li>`;

      $(`#${autocompleteFormId}-data`).append(html);

      return true;
    }).on('typeahead:close', (e) => {
      $(e.target).val('');
    });
  }
}
