<!--**
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div
    :id="id"
    class="card-block row"
  >
    <div class="col-sm">
      <language-list
        v-if="languagesCount"
        :languages="languages"
        @selectLanguage="selectLanguage"
        @resetLanguage="resetLanguage"
      />

      <currency-modal
        :language="selectedLanguage"
        @close="closeModal"
        @applyCustomization="applyCustomization"
      />
    </div>
  </div>
</template>

<script>
  import {showGrowl} from '@app/utils/growl';
  import {EventEmitter} from '@components/event-emitter';
  import CurrencyFormEventMap from '@pages/currency/form/currency-form-event-map';
  import LanguageList from './LanguageList';
  import CurrencyModal from './CurrencyModal';

  export default {
    name: 'CurrencyFormatter',
    data: () => ({selectedLanguage: null}),
    props: {
      id: {
        type: String,
        required: true,
      },
      languages: {
        type: Array,
        required: true,
      },
      currencyData: {
        type: Object,
        required: true,
      },
    },
    components: {LanguageList, CurrencyModal},
    computed: {
      languagesCount() {
        return this.languages.length;
      },
    },
    methods: {
      closeModal() {
        this.selectedLanguage = null;
      },
      selectLanguage(language) {
        this.selectedLanguage = language;
      },
      resetLanguage(language) {
        const patterns = language.currencyPattern.split(';');
        language.priceSpecification.positivePattern = patterns[0];
        language.priceSpecification.negativePattern = patterns.length > 1 ? patterns[1] : `-${patterns[0]}`;
        language.priceSpecification.currencySymbol = language.currencySymbol;

        this.currencyData.transformations[language.id] = '';
        this.currencyData.symbols[language.id] = language.currencySymbol;

        showGrowl('success', this.$t('list.reset.success'));

        EventEmitter.emit(CurrencyFormEventMap.refreshCurrencyApp, this.currencyData);
      },
      applyCustomization(customData) {
        const selectedPattern = this.selectedLanguage.transformations[
          customData.transformation
        ];
        const patterns = selectedPattern.split(';');

        this.selectedLanguage.priceSpecification.currencySymbol = customData.symbol;
        this.selectedLanguage.priceSpecification.positivePattern = patterns[0];
        // eslint-disable-next-line
        this.selectedLanguage.priceSpecification.negativePattern =
          patterns.length > 1 ? patterns[1] : `-${patterns[0]}`;

        this.currencyData.transformations[this.selectedLanguage.id] = customData.transformation;
        this.currencyData.symbols[this.selectedLanguage.id] = customData.symbol;

        EventEmitter.emit(CurrencyFormEventMap.refreshCurrencyApp, this.currencyData);

        this.closeModal();
      },
    },
  };
</script>
