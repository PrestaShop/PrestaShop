/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default {
  priceList: '#js-specific-price-list',
  cancel: '#specific_price_form .js-cancel',
  priceForm: '#specific_price_form',
  save: '#specific_price_form .js-save',
  openCreate: '#js-open-create-specific-price-form',
  leavBPrice: (selectorPrefix: string): string => `${selectorPrefix}leave_bprice`,
  reductionType: (selectorPrefix: string): string => `${selectorPrefix}sp_reduction_type`,
  modalCancel: '#form_modal_cancel',
  modalClose: '#form_modal_cancel',
  modalSave: '#form_modal_save',
};
