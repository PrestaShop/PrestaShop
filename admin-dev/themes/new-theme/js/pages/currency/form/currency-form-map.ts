/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Defines all selectors that are used in currency add/edit form.
 */
export default {
  currencyForm: '#currency_form',
  currencyFormFooter: '#currency_form .card .card-footer',
  currencySelector: '#currency_selected_iso_code',
  isUnofficialCheckbox: '#currency_unofficial',
  namesInput: (langId: string): string => `#currency_names_${langId}`,
  symbolsInput: (langId: string): string => `#currency_symbols_${langId}`,
  transformationsInput: (langId: string): string => `#currency_transformations_${langId}`,
  isoCodeInput: '#currency_iso_code',
  exchangeRateInput: '#currency_exchange_rate',
  resetDefaultSettingsInput: '#currency_reset_default_settings',
  loadingDataModal: '#currency_loading_data_modal',
  precisionInput: '#currency_precision',
  shopAssociationTree: '#currency_shop_association',
  currencyFormatter: '#currency_formatter',
};
