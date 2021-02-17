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
import Router from '@components/router';

const {$} = window;

const initMultistoreHeader = () => {
  const headerButton = document.querySelector('.js-header-multishop-open-modal');
  const modalMultishop = document.querySelector('.js-multishop-modal');
  const $searchInput = $('.js-multishop-modal-search');
  const router = new Router();
  const route = router.generate('admin_shops_search', {searchTerm: '__QUERY__'});

  const config = {
    minLength: 2,
    highlight: true,
    cache: false,
    hint: false,
  };

  const source = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: route,
      wildcard: '__QUERY__',
    },
  });

  const dataSetConfig = {
    display: 'name',
    value: 'id',
    source,
    /* eslint-disable-next-line no-unused-vars */
    onSelect(selectedItem, event) {
      window.location.href = selectedItem.url;

      return true;
    },
    /* eslint-disable-next-line no-unused-vars */
    onClose(event) {},
  };

  const defaultTemplates = {
    // Be careful that your rendering function must return HTML node not pure text so always include the
    // content in a div at least
    suggestion: (item) => {
      let displaySuggestion = item;

      if (typeof dataSetConfig.display === 'function') {
        dataSetConfig.display(item);
      } else if (Object.prototype.hasOwnProperty.call(item, dataSetConfig.display)) {
        displaySuggestion = item[dataSetConfig.display];
      }

      return `<div class="px-2">${displaySuggestion}</div>`;
    },
    pending(query) {
      return `<div class="px-2">${$searchInput.data('searching')} "${query.query}"</div>`;
    },
    notFound(query) {
      return `<div class="px-2">${$searchInput.data('no-results')} "${query.query}"</div>`;
    },
  };

  if (Object.prototype.hasOwnProperty.call(config, 'templates')) {
    dataSetConfig.templates = {...defaultTemplates, ...config.templates};
  } else {
    dataSetConfig.templates = defaultTemplates;
  }

  $searchInput
    .typeahead(config, dataSetConfig)
    .bind('typeahead:select', (e, selectedItem) => dataSetConfig.onSelect(selectedItem, e))
    .bind('typeahead:close', (e) => {
      dataSetConfig.onClose(e);
    });

  headerButton.addEventListener('click', () => {
    modalMultishop.classList.toggle('multishop-modal-hidden');
    headerButton.classList.toggle('active');
  });
};

$(() => {
  initMultistoreHeader();
});
