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
import VueResource from 'vue-resource';
import {showGrowl} from '@app/utils/growl';
import ConfirmModal from '@components/modal';
import ReplaceFormatter from '@vue/plugins/vue-i18n/replace-formatter';
import CurrencyFormatter from './components/CurrencyFormatter.vue';

Vue.use(VueResource);
Vue.use(VueI18n);

export default class CurrencyForm {
  /**
   * @param {object} currencyFormMap - Page map
   */
  constructor(currencyFormMap) {
    this.map = currencyFormMap;
    this.$currencyForm = $(this.map.currencyForm);
    this.$currencyFormFooter = $(this.map.currencyFormFooter);
    this.apiReferenceUrl = this.$currencyForm.data('reference-url');
    this.referenceCurrencyResource = Vue.resource(this.apiReferenceUrl);
    this.originalLanguages = this.$currencyForm.data('languages');
    this.translations = this.$currencyForm.data('translations');
    this.$currencySelector = $(this.map.currencySelector);
    this.$isUnofficialCheckbox = $(this.map.isUnofficialCheckbox);
    this.$isoCodeInput = $(this.map.isoCodeInput);
    this.$exchangeRateInput = $(this.map.exchangeRateInput);
    this.$precisionInput = $(this.map.precisionInput);
    this.$resetDefaultSettingsButton = $(this.map.resetDefaultSettingsInput);
    this.$loadingDataModal = $(this.map.loadingDataModal);
    this.currencyFormatterId = this.map.currencyFormatter.replace('#', '');
    this.hideModal = true;
    this.$loadingDataModal.on('shown.bs.modal', () => {
      if (this.hideModal) {
        this.$loadingDataModal.modal('hide');
      }
    });
  }

  init() {
    this.initListeners();
    this.initFields();
    this.initState();
    this.initCurrencyFormatter();
  }

  initState() {
    this.state = {
      currencyData: this.getCurrencyDataFromForm(),
      languages: [...this.originalLanguages],
    };
  }

  initCurrencyFormatter() {
    // Customizer only present when languages data are present (for installed currencies only)
    if (!this.originalLanguages.length) {
      return;
    }

    const i18n = new VueI18n({
      locale: 'en',
      formatter: new ReplaceFormatter(),
      messages: {en: this.translations},
    });

    $(`<div id="${this.currencyFormatterId}"></div>`).insertBefore(this.$currencyFormFooter);
    this.currencyFormatter = new Vue({
      el: this.map.currencyFormatter,
      i18n,
      components: {CurrencyFormatter},
      data: this.state,
      template: `<currency-formatter
        id="${this.currencyFormatterId}"
        :languages="languages"
        :currencyData="currencyData">
      </currency-formatter>`,
    });

    this.currencyFormatter.$watch('currencyData', () => {
      // We use the state value directly since the object is shared with the Vue component and already updated
      this.fillCurrencyCustomData(this.state.currencyData);
    }, {deep: true, immediate: true});
  }

  initListeners() {
    this.$currencySelector.change(this.onCurrencySelectorChange.bind(this));
    this.$isUnofficialCheckbox.change(this.onIsUnofficialCheckboxChange.bind(this));
    this.$resetDefaultSettingsButton.click(this.onResetDefaultSettingsClick.bind(this));
  }

  initFields() {
    if (!this.isUnofficialCurrency()) {
      this.$isUnofficialCheckbox.prop('checked', false);
      this.$isoCodeInput.prop('readonly', true);
    } else {
      this.$currencySelector.val('');
      this.$isoCodeInput.prop('readonly', false);
    }
  }

  onCurrencySelectorChange() {
    const selectedISOCode = this.$currencySelector.val();

    if (selectedISOCode !== '') {
      this.$isUnofficialCheckbox.prop('checked', false);
      this.$isoCodeInput.prop('readonly', true);
      this.resetCurrencyData(selectedISOCode);
    } else {
      this.$isUnofficialCheckbox.prop('checked', true);
      this.$isoCodeInput.prop('readonly', false);
    }
  }

  isUnofficialCurrency() {
    if (this.$isUnofficialCheckbox.prop('type') === 'hidden') {
      return this.$isUnofficialCheckbox.attr('value') === '1';
    }

    return this.$isUnofficialCheckbox.prop('checked');
  }

  onIsUnofficialCheckboxChange() {
    if (this.isUnofficialCurrency()) {
      this.$currencySelector.val('');
      this.$isoCodeInput.prop('readonly', false);
    } else {
      this.$isoCodeInput.prop('readonly', true);
    }
  }

  async onResetDefaultSettingsClick() {
    await this.resetCurrencyData(this.$isoCodeInput.val());
  }

  showResetDefaultSettingsConfirmModal() {
    const confirmTitle = this.translations['modal.restore.title'];
    const confirmMessage = this.translations['modal.restore.body'];
    const confirmButtonLabel = this.translations['modal.restore.apply'];
    const closeButtonLabel = this.translations['modal.restore.cancel'];

    const modal = new ConfirmModal({
      id: 'currency_restore_default_settings',
      confirmTitle,
      confirmMessage,
      confirmButtonLabel,
      closeButtonLabel,
    }, () => this.onResetDefaultSettingsClick());

    modal.show();
  }

  async resetCurrencyData(selectedISOCode) {
    this.$loadingDataModal.modal('show');
    this.$resetDefaultSettingsButton.addClass('spinner');

    this.state.currencyData = await this.fetchCurrency(selectedISOCode);
    this.fillCurrencyData(this.state.currencyData);

    // Reset languages
    this.originalLanguages.forEach((language) => {
      // Use language data (which contain the reference) to reset
      // price specification data (which contain the custom values)
      const patterns = language.currencyPattern.split(';');
      language.priceSpecification.positivePattern = patterns[0];
      language.priceSpecification.negativePattern = patterns.length > 1 ? patterns[1] : `-${patterns[0]}`;
      language.priceSpecification.currencySymbol = language.currencySymbol;
    });
    this.state.languages = [...this.originalLanguages];

    this.hideModal = true;
    this.$loadingDataModal.modal('hide');
    this.$resetDefaultSettingsButton.removeClass('spinner');
  }

  async fetchCurrency(currencyIsoCode) {
    let currencyData = null;

    if (currencyIsoCode) {
      await this.referenceCurrencyResource.get({id: currencyIsoCode}).then((response) => {
        currencyData = response.body;
      }, (errorResponse) => {
        if (errorResponse.body && errorResponse.body.error) {
          showGrowl('error', errorResponse.body.error, 3000);
        } else {
          showGrowl('error', `Can not find CLDR data for currency ${currencyIsoCode}`, 3000);
        }
      });
    }

    if (currencyData && currencyData.transformations === undefined) {
      currencyData.transformations = {};
      Object.keys(currencyData.symbols).forEach((langId) => {
        currencyData.transformations[langId] = '';
      });
    }

    return currencyData;
  }

  fillCurrencyData(currencyData) {
    if (!currencyData) {
      return;
    }

    Object.keys(currencyData.symbols).forEach((langId) => {
      const langNameSelector = this.map.namesInput(langId);
      $(langNameSelector).val(currencyData.names[langId]);
    });

    this.fillCurrencyCustomData(currencyData);
    this.$isoCodeInput.val(currencyData.isoCode);
    this.$exchangeRateInput.val(currencyData.exchangeRate);
    this.$precisionInput.val(currencyData.precision);
  }

  fillCurrencyCustomData(currencyData) {
    Object.keys(currencyData.symbols).forEach((langId) => {
      const langSymbolSelector = this.map.symbolsInput(langId);
      $(langSymbolSelector).val(currencyData.symbols[langId]);
    });

    Object.keys(currencyData.transformations).forEach((langId) => {
      const langTransformationSelector = this.map.transformationsInput(langId);
      $(langTransformationSelector).val(currencyData.transformations[langId]);
    });
  }

  getCurrencyDataFromForm() {
    const currencyData = {
      names: {},
      symbols: {},
      transformations: {},
      isoCode: this.$isoCodeInput.val(),
      exchangeRate: this.$exchangeRateInput.val(),
      precision: this.$precisionInput.val(),
    };

    this.originalLanguages.forEach((lang) => {
      currencyData.names[lang.id] = $(this.map.namesInput(lang.id)).val();
      currencyData.symbols[lang.id] = $(this.map.symbolsInput(lang.id)).val();
      currencyData.transformations[lang.id] = $(this.map.transformationsInput(lang.id)).val();
    });

    return currencyData;
  }
}
