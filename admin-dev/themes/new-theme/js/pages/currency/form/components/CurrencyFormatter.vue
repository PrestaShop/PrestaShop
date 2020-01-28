<!--**
* 2007-2019 PrestaShop SA and Contributors
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
* needs please refer to https://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA and Contributors
* @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*-->
<template>
  <div :id="id" class="card-block row">
    <div class="col-sm">
      <language-list
        v-if="languagesCount"
        :languages="languages"
        @selectLanguage="selectLanguage"
        @resetLanguage="resetLanguage"
      >
      </language-list>

      <currency-modal
        :language="selectedLanguage"
        @close="closeModal"
        @applyCustomization="applyCustomization"
      >
      </currency-modal>

      <modal
        confirmation
        v-if="confirmationLanguage !== null"
        @confirm="applyResetLanguage"
        @close="closeConfirmationModal"
        :modalTitle="$t('modal.restore.title')"
        :cancelLabel="$t('modal.restore.cancel')"
        :confirmLabel="$t('modal.restore.apply')"
      >
        <template slot="body">{{ $t('modal.restore.body') }}</template>
      </modal>
    </div>
  </div>
</template>

<script>
  import Modal from '@vue/components/Modal';
  import LanguageList from './LanguageList';
  import CurrencyModal from './CurrencyModal';

  export default {
    name: 'currency-formatter',
    data: () => ({
      selectedLanguage: null,
      confirmationLanguage: null,
    }),
    props: {
      id: {
        type: String,
        required: true
      },
      languages: {
        type: Array,
        required: true
      },
      currencyData: {
        type: Object,
        required: true
      }
    },
    components: {LanguageList, CurrencyModal, Modal},
    computed: {
      languagesCount() {
        return this.languages.length;
      },
    }, methods: {
      closeModal() {
        this.selectedLanguage = null;
      },
      selectLanguage(language) {
        this.selectedLanguage = language;
      },
      closeConfirmationModal() {
        this.confirmationLanguage = null;
      },
      resetLanguage(language) {
        this.confirmationLanguage = language;
      },
      applyResetLanguage() {
        const patterns = this.confirmationLanguage.currencyPattern.split(';');
        this.confirmationLanguage.priceSpecification.positivePattern = patterns[0];
        this.confirmationLanguage.priceSpecification.negativePattern = patterns.length > 1 ? patterns[1] : '-' + patterns[0];
        this.confirmationLanguage.priceSpecification.currencySymbol = this.confirmationLanguage.currencySymbol;

        this.currencyData.transformations[this.confirmationLanguage.id] = '';
        this.currencyData.symbols[this.confirmationLanguage.id] = this.confirmationLanguage.currencySymbol;
        this.closeConfirmationModal();
      },
      applyCustomization(customData) {
        const selectedPattern = this.selectedLanguage.transformations[customData.transformation];
        const patterns = selectedPattern.split(';');

        this.selectedLanguage.priceSpecification.currencySymbol = customData.symbol;
        this.selectedLanguage.priceSpecification.positivePattern = patterns[0];
        this.selectedLanguage.priceSpecification.negativePattern = patterns.length > 1 ? patterns[1] : '-' + patterns[0];

        this.currencyData.transformations[this.selectedLanguage.id] = customData.transformation;
        this.currencyData.symbols[this.selectedLanguage.id] = customData.symbol;

        this.closeModal();
      }
    }
  }
</script>
