/**
 * 2007-2019 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * In Add/Edit page of Webservice key there is permissions table input (permissons as columns / resources as rows).
 * There is "All" column and once resource is checked under this column
 * every other permission column should be auto-selected for that resource.
 */
export default class PermissionsRowSelector {
  constructor() {
    // when checkbox in "All" column is checked
    $('input[id^="webservice_key_permissions_all"]').on('change', (event) => {
      const $checkedBox = $(event.currentTarget);

      const isChecked = $checkedBox.is(':checked');

      // for each input in same row we need to toggle its value
      $checkedBox.closest('tr').find(`input:not(input[id="${$checkedBox.attr('id')}"])`).each((i, input) => {
        $(input).prop('checked', isChecked);
      });
    });

    return {};
  }
}
