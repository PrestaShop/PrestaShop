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
import AutoCompleteSearch from '@components/auto-complete-search';
import PerfectScrollbar from 'perfect-scrollbar';
import ComponentsMap from '@components/components-map';
import 'perfect-scrollbar/css/perfect-scrollbar.css';

const {$} = window;

const initMultistoreDropdown = () => {
  const MultistoreDropdownMap = ComponentsMap.multistoreDropdown;
  const $searchInput = $(MultistoreDropdownMap.searchInput);
  const router = new Router();
  const route = router.generate('admin_shops_search', {
    searchTerm: '__QUERY__',
  });

  if ($(MultistoreDropdownMap.scrollbar).length > 0) {
    new PerfectScrollbar(MultistoreDropdownMap.scrollbar);
  }

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
      const contextUrlLetter = typeof selectedItem.groupName !== 'undefined' ? 's' : 'g';
      window.location.href = ComponentsMap.multistoreHeader.setContextUrl(
        window.location.href,
        contextUrlLetter,
        selectedItem.id,
      );

      return true;
    },
    /* eslint-disable-next-line no-unused-vars */
    onClose(event) {},
  };

  new AutoCompleteSearch($searchInput, dataSetConfig);
};

$(() => {
  initMultistoreDropdown();
});
