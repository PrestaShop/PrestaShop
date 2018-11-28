/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import _ from 'lodash';

Vue.use(Vuex);

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
  totalPages(rootState) {
    return rootState.totalPages;
  },
  pageIndex(rootState) {
    return rootState.pageIndex;
  },
  currentDomain(rootState) {
    return rootState.currentDomain;
  },
  translations(rootState) {
    return rootState.translations;
  },
  catalog(rootState) {
    return rootState.catalog;
  },
  domainsTree() {
    function convert(domains) {
      domains.forEach((domain) => {
        domain.children = _.values(domain.children);
        domain.extraLabel = domain.total_missing_translations;
        domain.dataValue = domain.domain_catalog_link;
        domain.warning = Boolean(domain.total_missing_translations);
        domain.disable = !domain.total_translations;
        domain.id = domain.full_name;
        convert(domain.children);
      });
      return domains;
    }

    return convert(state.domainsTree);
  },
  isReady(rootState) {
    return rootState.isReady;
  },
  searchTags(rootState) {
    return rootState.searchTags;
  },
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations,
});
