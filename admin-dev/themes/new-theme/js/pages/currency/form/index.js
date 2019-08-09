/**
 * 2007-2019 PrestaShop and Contributors
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

import ChoiceTree from '../../../components/form/choice-tree';
import TranslatableInput from '../../../components/translatable-input';
import currencyFormMap from "./currency-form-map";

const $ = window.$;

$(() => {
  new TranslatableInput();
  const choiceTree = new ChoiceTree('#currency_shop_association');
  choiceTree.enableAutoCheckChildren();

  const $currencyForm = $(currencyFormMap.currencyForm);
  const getCLDRDataUrl = $currencyForm.data('get-cldr-data');
  const $currencySelector = $(currencyFormMap.currencySelector);
  $currencySelector.change(() => {
    const selectedISOCode = $currencySelector.val();
    if ('' !== selectedISOCode) {
      $(currencyFormMap.isCustomCheckbox).prop('checked', false);
      $(currencyFormMap.isoCodeSelector).prop('readonly', true);
      $(currencyFormMap.numericIsoCodeSelector).prop('readonly', true);
      resetCurrencyData(selectedISOCode);
    } else {
      $(currencyFormMap.isCustomCheckbox).prop('checked', true);
      $(currencyFormMap.isoCodeSelector).prop('readonly', false);
      $(currencyFormMap.numericIsoCodeSelector).prop('readonly', false);
    }
  });

  $(currencyFormMap.isCustomCheckbox).change(() => {
    if ($(currencyFormMap.isCustomCheckbox).prop('checked')) {
      $currencySelector.val('');
      $(currencyFormMap.isoCodeSelector).prop('readonly', false);
      $(currencyFormMap.numericIsoCodeSelector).prop('readonly', false);
    }
  });

  $(currencyFormMap.resetDefaultSettingsSelector).click(() => {
    resetCurrencyData($(currencyFormMap.isoCodeSelector).val());

    return false;
  });

  function resetCurrencyData(selectedISOCode) {
    $(currencyFormMap.loadingDataModalSelector).modal('show');
    $(currencyFormMap.resetDefaultSettingsSelector).addClass('spinner');
    const getCurrencyData = getCLDRDataUrl.replace('CURRENCY_ISO_CODE', selectedISOCode);
    $.get(getCurrencyData)
      .then((currencyData) => {
        for (let langId in currencyData.names) {
          let langNameSelector = currencyFormMap.namesSelector.replace('LANG_ID', langId);
          $(langNameSelector).val(currencyData.names[langId]);
        }
        for (let langId in currencyData.symbols) {
          let langSymbolSelector = currencyFormMap.symbolsSelector.replace('LANG_ID', langId);
          $(langSymbolSelector).val(currencyData.symbols[langId]);
        }
        $(currencyFormMap.isoCodeSelector).val(currencyData.iso_code);
        $(currencyFormMap.numericIsoCodeSelector).val(currencyData.numeric_iso_code);
        if (currencyData.exchange_rate) {
          $(currencyFormMap.exchangeRateSelector).val(currencyData.exchange_rate);
        }
        $(currencyFormMap.loadingDataModalSelector).modal('hide');
        $(currencyFormMap.resetDefaultSettingsSelector).removeClass('spinner');
      })
      .fail(() => {
        $(currencyFormMap.loadingDataModalSelector).modal('hide');
        $(currencyFormMap.resetDefaultSettingsSelector).removeClass('spinner');
      })
  }

  function initFields() {
    const selectedISOCode = $currencySelector.val();
    const isCustom = parseInt($(currencyFormMap.isCustomCheckbox).val());
    if ('' !== selectedISOCode && !isCustom) {
      $(currencyFormMap.isCustomCheckbox).prop('checked', false);
      $(currencyFormMap.isoCodeSelector).prop('readonly', true);
      $(currencyFormMap.numericIsoCodeSelector).prop('readonly', true);
    } else {
      $currencySelector.val('');
      $(currencyFormMap.isoCodeSelector).prop('readonly', false);
      $(currencyFormMap.numericIsoCodeSelector).prop('readonly', false);
    }
  }
  initFields();
});
