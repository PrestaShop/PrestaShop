/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PlacementList from '@pages/extra-property-definition/form/placement-list';
import SuggestionDropdown, {SuggestionItem} from '@pages/extra-property-definition/form/suggestion-dropdown';
import {ExtraPropertyCatalogs} from '@pages/extra-property-definition/form/types';
import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';

/**
 * Search-to-add affordance of the Admin API subsection — a SuggestionDropdown in search mode
 * (query-dependent items, nothing written back into the search input): typing filters the
 * endpoint catalog grouped by resource (first URI segment); picking a suggestion adds a row
 * matching all methods. The LAST item is always the escape hatch — "Use as a custom path" — so a
 * third-party endpoint outside the catalog can be added too (it simply gets the amber
 * not-in-catalog pill).
 */
export default class EndpointSearch {
  constructor(container: HTMLElement, catalogs: ExtraPropertyCatalogs, list: PlacementList) {
    const input = container.querySelector<HTMLInputElement>(ExtraPropertyFormMap.api.endpointSearch);

    if (!input) {
      throw new Error('Endpoint search input not found');
    }

    const wrapper = input.closest<HTMLElement>(ExtraPropertyFormMap.api.endpointSearchWrapper) ?? container;
    wrapper.classList.add(ExtraPropertyFormMap.dropdown.wrapClass);

    new SuggestionDropdown(wrapper, ExtraPropertyFormMap.api.endpointSearch, {
      dynamicItems: true,
      writeValue: false,
      enterSelectsFirst: true,
      labelClass: 'text-monospace',
      items: (searchInput) => {
        const query = searchInput.value.trim();
        const items: SuggestionItem[] = catalogs.apis
          .filter((entry) => query === '' || entry.uriTemplate.toLowerCase().includes(query.toLowerCase()))
          .map((entry) => ({
            value: entry.uriTemplate,
            label: entry.uriTemplate,
            detail: entry.methods.join(' · '),
            group: `/${entry.uriTemplate.split('/').filter((segment) => segment !== '')[0] ?? ''}`,
          }));

        if (query !== '') {
          items.push({
            value: query,
            label: `+ ${searchInput.dataset.customLabel ?? 'Use as a custom path'}: ${query}`,
            detail: '',
            custom: true,
          });
        }

        return items;
      },
      onSelect: (uri, searchInput) => {
        list.addRowWithValues({uri, methods: ''});
        /* eslint-disable no-param-reassign */
        searchInput.value = '';
        /* eslint-enable no-param-reassign */
      },
    });
  }
}
