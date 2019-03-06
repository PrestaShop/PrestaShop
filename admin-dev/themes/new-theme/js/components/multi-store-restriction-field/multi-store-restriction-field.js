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

const $ = window.$;

import multiStoreRestrictionFieldMap from 'multi-store-restriction-field-map';

/**
 * Enables multi store functionality for the page. It includes switch functionality and checkboxes
 */
export default class MultiStoreRestrictionField {
  constructor() {
    $(document).on('change', multiStoreRestrictionFieldMap.multiStoreRestrictionCheckbox, (e) => {
      this._multiStoreRestrictionCheckboxFieldChangeEvent(e)
    })
  }

  /**
   * Toggles the checkbox field and enables or disables its related field.
   *
   * @param e
   * @private
   */
  _multiStoreRestrictionCheckboxFieldChangeEvent(e) {
    const $currentItem = $(e.currentTarget);
    const targetValue = $currentItem.data('shopRestrictionTarget');

    $(`[data-shop-restriction-source="${targetValue}"]`).attr('disabled', !$currentItem.is(':checked'));
  }
}
