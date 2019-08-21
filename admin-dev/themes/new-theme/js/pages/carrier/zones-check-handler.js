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

export default class ZonesCheckHandler {
  constructor(zoneCheck) {
    this.$zoneCheck = $(zoneCheck);
    this.handle();


    return {};
  }

  handle() {
    this.$zoneCheck.change((e) => {
      if ($(e.target).val() === '0') {
        this.selectAll(e);
      }
      this.disableDependantInputs(e);
    });
  }

  selectAll(event) {
    const isSelectAllChecked = $(event.target).is(':checked');

    this.$zoneCheck.not(event.target).prop('checked', isSelectAllChecked);
  }

  disableDependantInputs(event) {
    $.each($(event.target), (i, input) => {
      const isChecked = $(event.target).is(':checked');
      const zoneId = $(input).val();
      $('#js-carrier-ranges').find(`div[data-zone-id='${zoneId}'] input`).prop('readonly', !isChecked);
    });
  }
}
