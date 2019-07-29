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

export default class AddRangeHandler {
  constructor() {
    $(document).on('click', '.js-add-range', () => {
      const rows = $('.js-range-row, .js-zone-row');
      const formTemplate = $('#js-ranges-input-template').html();

      this.addColumn(rows, formTemplate);
    });

    return {};
  }

  /**
   * Add new form prototype to collection
   *
   * @param rows
   * @param formTemplate
   */
  addColumn(rows, formTemplate) {
    const rangesCount = $('table').data('culumn-count') + 1;

    for (let i = 0; i < Object.keys(rows).length; i++) {
      const zoneId = $(rows[i]).data('zone-id');

      let name = `range_${rangesCount}`;
      if (typeof $(rows[i]).data('zone-id') !== 'undefined') {
        name = `zone_${zoneId}_range_${rangesCount}`;
      }

      const form = formTemplate.replace('/__name__/', name);
      $(rows[i]).append(form);
    }
    $('table').data('range-count', rangesCount);
  }
}

