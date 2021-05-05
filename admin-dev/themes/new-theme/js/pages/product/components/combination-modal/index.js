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
import ReplaceFormatter from '@vue/plugins/vue-i18n/replace-formatter';
import CombinationModal from '@pages/product/components/combination-modal/CombinationModal.vue';

Vue.use(VueI18n);

export default function initCombinationModal(combinationModalSelector, productId) {
  const container = document.querySelector(combinationModalSelector);

  if (!container) {
    return null;
  }

  const translations = JSON.parse(container.dataset.translations);
  const i18n = new VueI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  return new Vue({
    el: combinationModalSelector,
    template: '<combination-modal :productId=productId />',
    components: {CombinationModal},
    i18n,
    data: {
      productId,
    },
  });
}
