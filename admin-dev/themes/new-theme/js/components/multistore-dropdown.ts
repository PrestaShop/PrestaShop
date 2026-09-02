/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

// @ts-ignore-next-line
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
    /* eslint-disable-next-line @typescript-eslint/no-unused-vars */
    onSelect(selectedItem: any, event: Event) {
      const contextUrlLetter = typeof selectedItem.groupName !== 'undefined' ? 's' : 'g';
      window.location.href = ComponentsMap.multistoreHeader.setContextUrl(
        window.location.href,
        contextUrlLetter,
        selectedItem.id,
      );

      return true;
    },
  };

  new AutoCompleteSearch($searchInput, dataSetConfig);
};

$(() => {
  initMultistoreDropdown();
});
