<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
