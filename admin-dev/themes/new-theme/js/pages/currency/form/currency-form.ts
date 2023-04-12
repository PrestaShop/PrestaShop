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
import {createApp} from 'vue';
import {createI18n} from 'vue-i18n';
import {showGrowl} from '@app/utils/growl';
import ConfirmModal from '@components/modal';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import {EventEmitter} from '@components/event-emitter';
import CurrencyFormatter from './components/CurrencyFormatter.vue';
import CurrencyFormEventMap from './currency-form-event-map';

export default class CurrencyForm {
  map: Record<string, any>;

  $currencyForm: JQuery;

  $currencyFormFooter: JQuery;

  apiReferenceUrl: string;

  originalLanguages: any;

  translations: Record<string, any>;

  $currencySelector: JQuery;

  $isUnofficialCheckbox: JQuery;

  $isoCodeInput: JQuery;

  $exchangeRateInput: JQuery;

  $precisionInput: JQuery;

  $resetDefaultSettingsButton: JQuery;

  currencyFormatterId: string;

  $loadingDataModal: JQuery;

  hideModal: boolean;

  state: Record<string, any>;

  currencyFormatter: any;

  /**
   * @param {object} currencyFormMap - Page map
   */
  constructor(currencyFormMap: Record<string, any>) {
    this.map = currencyFormMap;
    this.$currencyForm = $(this.map.currencyForm);
    this.$currencyFormFooter = $(this.map.currencyFormFooter);
    this.apiReferenceUrl = this.$currencyForm.data('reference-url');
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
    this.currencyFormatter = null;
    this.$loadingDataModal.on('shown.bs.modal', () => {
      if (this.hideModal) {
        this.$loadingDataModal.modal('hide');
      }
    });
    this.state = {};
  }

  init(): void {
    this.initListeners();
    this.initFields();
    this.initState();
    this.initCurrencyFormatter();

    EventEmitter.on(CurrencyFormEventMap.refreshCurrencyApp, (currencyData) => {
      this.state.currencyData = currencyData;
      this.fillCurrencyCustomData(currencyData);
      this.initCurrencyFormatter();
    });
  }

  initState(): void {
    this.state = {
      currencyData: this.getCurrencyDataFromForm(),
      languages: [...this.originalLanguages],
    };
  }

  initCurrencyFormatter(): void {
    // Customizer only present when languages data are present (for installed currencies only)
    if (!this.originalLanguages.length) {
      return;
    }

    $(`<div id="${this.currencyFormatterId}"></div>`).insertBefore(
      this.$currencyFormFooter,
    );

    const i18n = createI18n({
      locale: 'en',
      formatter: new ReplaceFormatter(),
      messages: {en: this.translations},
    });

    this.currencyFormatter = createApp(CurrencyFormatter, {
      data: () => this.state,
      languages: this.state.languages,
      currencyData: this.state.currencyData,
      id: this.currencyFormatterId,
    }).use(i18n).mount(this.map.currencyFormatter);
  }

  initListeners(): void {
    this.$currencySelector.change(this.onCurrencySelectorChange.bind(this));
    this.$isUnofficialCheckbox.change(
      this.onIsUnofficialCheckboxChange.bind(this),
    );
    this.$resetDefaultSettingsButton.click(
      this.onResetDefaultSettingsClick.bind(this),
    );
  }

  initFields(): void {
    if (!this.isUnofficialCurrency()) {
      this.$isUnofficialCheckbox.prop('checked', false);
      this.$isoCodeInput.prop('readonly', true);
    } else {
      this.$currencySelector.val('');
      this.$isoCodeInput.prop('readonly', false);
    }
  }

  onCurrencySelectorChange(): void {
    const selectedISOCode = this.$currencySelector.val();

    if (selectedISOCode !== '') {
      this.$isUnofficialCheckbox.prop('checked', false);
      this.$isoCodeInput.prop('readonly', true);
      this.resetCurrencyData(<string>selectedISOCode);
    } else {
      this.$isUnofficialCheckbox.prop('checked', true);
      this.$isoCodeInput.prop('readonly', false);
    }
  }

  isUnofficialCurrency(): boolean {
    if (this.$isUnofficialCheckbox.prop('type') === 'hidden') {
      return this.$isUnofficialCheckbox.attr('value') === '1';
    }

    return this.$isUnofficialCheckbox.prop('checked');
  }

  onIsUnofficialCheckboxChange(): void {
    if (this.isUnofficialCurrency()) {
      this.$currencySelector.val('');
      this.$isoCodeInput.prop('readonly', false);
    } else {
      this.$isoCodeInput.prop('readonly', true);
    }
  }

  async onResetDefaultSettingsClick(): Promise<void> {
    await this.resetCurrencyData(<string> this.$isoCodeInput.val());
  }

  showResetDefaultSettingsConfirmModal(): void {
    const confirmTitle = this.translations['modal.restore.title'];
    const confirmMessage = this.translations['modal.restore.body'];
    const confirmButtonLabel = this.translations['modal.restore.apply'];
    const closeButtonLabel = this.translations['modal.restore.cancel'];

    const modal = new ConfirmModal(
      {
        id: 'currency_restore_default_settings',
        confirmTitle,
        confirmMessage,
        confirmButtonLabel,
        closeButtonLabel,
      },
      () => this.onResetDefaultSettingsClick(),
    );

    modal.show();
  }

  async resetCurrencyData(selectedISOCode: string): Promise<void> {
    this.$loadingDataModal.modal('show');
    this.$resetDefaultSettingsButton.addClass('spinner');

    this.state.currencyData = await this.fetchCurrency(selectedISOCode);
    this.fillCurrencyData(this.state.currencyData);

    // Reset languages
    this.originalLanguages.forEach((language: Record<string, any>) => {
      // Use language data (which contain the reference) to reset
      // price specification data (which contain the custom values)
      const patterns = language.currencyPattern.split(';');
      /* eslint-disable */

      language.priceSpecification.positivePattern = patterns[0];
      language.priceSpecification.negativePattern =
        patterns.length > 1 ? patterns[1] : `-${patterns[0]}`;
      language.priceSpecification.currencySymbol = language.currencySymbol;
      /* eslint-enable */
    });
    this.state.languages = [...this.originalLanguages];
    EventEmitter.emit(CurrencyFormEventMap.refreshCurrencyApp, this.state.currencyData);

    this.hideModal = true;
    this.$loadingDataModal.modal('hide');
    this.$resetDefaultSettingsButton.removeClass('spinner');
  }

  async fetchCurrency(currencyIsoCode: string): Promise<Record<string, any>> {
    let currencyData: Record<string, any> = {};

    if (currencyIsoCode) {
      try {
        const response = await fetch(`${this.apiReferenceUrl.replace('{/id}', `/${currencyIsoCode}`)}`);
        currencyData = await response.json();

        if (currencyData && currencyData.transformations === undefined) {
          currencyData.transformations = {};
          Object.keys(currencyData.symbols).forEach((langId) => {
            currencyData.transformations[langId] = '';
          });
        }
      } catch (errorResponse: any) {
        if (errorResponse.body && errorResponse.body.error) {
          showGrowl('error', errorResponse.body.error, 3000);
        } else {
          showGrowl(
            'error',
            `Can not find CLDR data for currency ${currencyIsoCode}`,
            3000,
          );
        }
      }
    }

    return currencyData;
  }

  fillCurrencyData(
    currencyData: Record<string, any>,
  ): void | Record<string, any> {
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

  fillCurrencyCustomData(currencyData: Record<string, any>): void {
    Object.keys(currencyData.symbols).forEach((langId) => {
      const langSymbolSelector = this.map.symbolsInput(langId);
      $(langSymbolSelector).val(currencyData.symbols[langId]);
    });

    Object.keys(currencyData.transformations).forEach((langId) => {
      const langTransformationSelector = this.map.transformationsInput(langId);
      $(langTransformationSelector).val(currencyData.transformations[langId]);
    });
  }

  getCurrencyDataFromForm(): Record<string, any> {
    const currencyData: Record<string, any> = {
      names: {},
      symbols: {},
      transformations: {},
      isoCode: this.$isoCodeInput.val(),
      exchangeRate: this.$exchangeRateInput.val(),
      precision: this.$precisionInput.val(),
    };

    this.originalLanguages.forEach((lang: Record<string, any>) => {
      currencyData.names[<string>lang.id] = $(
        this.map.namesInput(lang.id),
      ).val();
      currencyData.symbols[lang.id] = $(this.map.symbolsInput(lang.id)).val();
      currencyData.transformations[lang.id] = $(
        this.map.transformationsInput(lang.id),
      ).val();
    });

    return currencyData;
  }
}
