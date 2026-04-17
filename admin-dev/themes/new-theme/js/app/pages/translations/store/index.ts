/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createStore} from 'vuex';
import _ from 'lodash';
import * as actions from './actions';
import mutations from './mutations';

// root state object.

const state = {
  pageIndex: 1,
  totalPages: 0,
  translationsPerPage: 20,
  currentDomain: '',
  translations: {
    data: {},
    info: {},
  },
  catalog: {
    data: {},
    info: {},
  },
  domainsTree: [],
  totalMissingTranslations: 0,
  totalTranslations: 0,
  currentDomainTotalTranslations: 0,
  currentDomainTotalMissingTranslations: 0,
  isReady: false,
  sidebarLoading: true,
  principalLoading: true,
  searchTags: [],
  modifiedTranslations: [],
};

// getters are functions
const getters = {
  totalPages(rootState: Record<string, any>) {
    return rootState.totalPages;
  },
  pageIndex(rootState: Record<string, any>) {
    return rootState.pageIndex;
  },
  currentDomain(rootState: Record<string, any>) {
    return rootState.currentDomain;
  },
  translations(rootState: Record<string, any>) {
    return rootState.translations;
  },
  catalog(rootState: Record<string, any>) {
    return rootState.catalog;
  },
  domainsTree(rootState: Record<string, any>): Array<Record<string, any>> {
    function convert(domains: Array<Record<string, any>>): Array<Record<string, any>> {
      domains.forEach((domain: Record<string, any>) => {
        /* eslint-disable */
        domain.children = _.values(domain.children);
        domain.extraLabel = domain.total_missing_translations;
        domain.dataValue = domain.domain_catalog_link;
        domain.warning = Boolean(domain.total_missing_translations);
        domain.disable = !domain.total_translations;
        domain.id = domain.full_name;
        /* eslint-enable */
        convert(domain.children);
      });
      return domains;
    }

    return convert(rootState.domainsTree);
  },
  isReady(rootState: Record<string, any>): boolean {
    return rootState.isReady;
  },
  searchTags(rootState: Record<string, any>): Record<string, any> {
    return rootState.searchTags;
  },
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default createStore({
  state() {
    return state;
  },
  getters,
  actions,
  mutations,
});
