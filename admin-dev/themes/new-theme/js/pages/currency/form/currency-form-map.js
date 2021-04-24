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

/**
 * Defines all selectors that are used in currency add/edit form.
 */
export default {
  currencyForm: '#currency_form',
  currencyFormFooter: '#currency_form .card .card-footer',
  currencySelector: '#currency_selected_iso_code',
  isUnofficialCheckbox: '#currency_unofficial',
  namesInput: (langId) => `#currency_names_${langId}`,
  symbolsInput: (langId) => `#currency_symbols_${langId}`,
  transformationsInput: (langId) => `#currency_transformations_${langId}`,
  isoCodeInput: '#currency_iso_code',
  exchangeRateInput: '#currency_exchange_rate',
  resetDefaultSettingsInput: '#currency_reset_default_settings',
  loadingDataModal: '#currency_loading_data_modal',
  precisionInput: '#currency_precision',
  shopAssociationTree: '#currency_shop_association',
  currencyFormatter: '#currency_formatter',
};
