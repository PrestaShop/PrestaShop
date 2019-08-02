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
    const getCurrencyData = getCLDRDataUrl.replace('CURRENCY_ISO_CODE', $currencySelector.val());
    console.log(getCurrencyData);
    $.get(getCurrencyData).then((currencyData) => {
      for (let langId in currencyData.names) {
        let langNameSelector = currencyFormMap.nameSelector.replace('LANG_ID', langId);
        $(langNameSelector).val(currencyData.names[langId]);
      }
      for (let langId in currencyData.symbols) {
        let langSymbolSelector = currencyFormMap.symbolSelector.replace('LANG_ID', langId);
        $(langSymbolSelector).val(currencyData.symbols[langId]);
      }
      $(currencyFormMap.isoCodeSelector).val(currencyData.iso_code);
      $(currencyFormMap.numericIsoCodeSelector).val(currencyData.numeric_iso_code);
      $(currencyFormMap.exchangeRateSelector).val(currencyData.exchange_rate);
    })
  });
});
