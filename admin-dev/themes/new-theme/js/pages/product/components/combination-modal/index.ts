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

import Vue from 'vue';
import VueI18n from 'vue-i18n';
import EventEmitter from '@components/event-emitter';
import ReplaceFormatter from '@vue/plugins/vue-i18n/replace-formatter';
import CombinationModal from '@pages/product/components/combination-modal/CombinationModal.vue';
import PaginatedCombinationsService from '@pages/product/services/paginated-combinations-service';

Vue.use(VueI18n);

/**
 * @param {string} combinationModalSelector
 * @param {PaginatedCombinationsService} paginatedCombinationsService
 * @param {int} productId
 * @param {Object} eventEmitter
 *
 * @returns {Vue|CombinedVueInstance<Vue, {eventEmitter, productId}, object, object, Record<never, any>>|null}
 */
export default function initCombinationModal(
  combinationModalSelector: string,
  paginatedCombinationsService: PaginatedCombinationsService,
  productId: number,
  eventEmitter: typeof EventEmitter,
): Vue {
  const container = <HTMLElement> document.querySelector(combinationModalSelector);
  const {emptyImage} = container.dataset;

  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = new VueI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  return new Vue({
    el: combinationModalSelector,
    template:
    // eslint-disable-next-line max-len
      '<combination-modal :emptyImageUrl="emptyImage" :eventEmitter=eventEmitter :paginated-combinations-service="paginatedCombinationsService"/>',
    components: {CombinationModal},
    i18n,
    data: {
      paginatedCombinationsService,
      eventEmitter,
      emptyImage,
    },
  });
}
