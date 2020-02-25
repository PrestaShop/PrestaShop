/**
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
 */

import {showGrowl} from '@app/utils/growl';

export default class CurrencyForm {
  /**
   * @param {object} currencyFormMap - Page map
   */
  constructor(currencyFormMap) {
    this.map = currencyFormMap;
    this.$currencyForm = $(this.map.currencyForm);
    this.getCLDRDataUrl = this.$currencyForm.data('get-cldr-data');
    this.$currencySelector = $(this.map.currencySelector);
    this.$isUnofficialCheckbox = $(this.map.isUnofficialCheckbox);
    this.$isoCodeInput = $(this.map.isoCodeInput);
    this.$exchangeRateInput = $(this.map.exchangeRateInput);
    this.$precisionInput = $(this.map.precisionInput);
    this.$resetDefaultSettingsButton = $(this.map.resetDefaultSettingsInput);
    this.$loadingDataModal = $(this.map.loadingDataModal);
  }

  init() {
    this._initListeners();
    this._initFields();
  }

  _initListeners() {
    this.$currencySelector.change(this._onCurrencySelectorChange.bind(this));
    this.$isUnofficialCheckbox.change(this._onIsUnofficialCheckboxChange.bind(this));
    this.$resetDefaultSettingsButton.click(this._onResetDefaultSettingsClick.bind(this));
  }

  _initFields() {
    if (!this._isUnofficialCurrency()) {
      this.$isUnofficialCheckbox.prop('checked', false);
      this.$isoCodeInput.prop('readonly', true);
    } else {
      this.$currencySelector.val('');
      this.$isoCodeInput.prop('readonly', false);
    }
  }

  _onCurrencySelectorChange() {
    const selectedISOCode = this.$currencySelector.val();
    if ('' !== selectedISOCode) {
      this.$isUnofficialCheckbox.prop('checked', false);
      this.$isoCodeInput.prop('readonly', true);
      this._resetCurrencyData(selectedISOCode);
    } else {
      this.$isUnofficialCheckbox.prop('checked', true);
      this.$isoCodeInput.prop('readonly', false);
    }
  }

  _isUnofficialCurrency() {
    if ('hidden' === this.$isUnofficialCheckbox.prop('type')) {
      return '1' === this.$isUnofficialCheckbox.attr('value');
    }

    return this.$isUnofficialCheckbox.prop('checked');
  }

  _onIsUnofficialCheckboxChange() {
    if (this._isUnofficialCurrency()) {
      this.$currencySelector.val('');
      this.$isoCodeInput.prop('readonly', false);
    } else {
      this.$isoCodeInput.prop('readonly', true);
    }
  }

  _onResetDefaultSettingsClick() {
    this._resetCurrencyData(this.$isoCodeInput.val());
  }

  _resetCurrencyData(selectedISOCode) {
    this.$loadingDataModal.modal('show');
    this.$resetDefaultSettingsButton.addClass('spinner');
    const getCurrencyData = this.getCLDRDataUrl.replace('CURRENCY_ISO_CODE', selectedISOCode);
    $.get(getCurrencyData)
      .then((currencyData) => {
        for (let langId in currencyData.names) {
          let langNameSelector = this.map.namesInput(langId);
          $(langNameSelector).val(currencyData.names[langId]);
        }
        for (let langId in currencyData.symbols) {
          let langSymbolSelector = this.map.symbolsInput(langId);
          $(langSymbolSelector).val(currencyData.symbols[langId]);
        }
        this.$isoCodeInput.val(currencyData.isoCode);
        if (currencyData.exchangeRate) {
          this.$exchangeRateInput.val(currencyData.exchangeRate);
        }
        this.$precisionInput.val(currencyData.precision);
      })
      .fail((currencyData) => {
        let errorMessage = 'Can not find CLDR data for currency ' + selectedISOCode;
        if (currencyData && currencyData.responseJSON && currencyData.responseJSON.error) {
          errorMessage = currencyData.responseJSON.error;
        }
        showGrowl('error', errorMessage, 3000);
      })
      .always(() => {
        this.$loadingDataModal.modal('hide');
        this.$resetDefaultSettingsButton.removeClass('spinner');
      })
  }
}
