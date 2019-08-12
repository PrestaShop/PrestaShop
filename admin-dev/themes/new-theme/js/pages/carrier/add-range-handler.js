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

/**
 * Responsible for adding new range column which consists of range and price inputs
 */
export default class AddRangeHandler {
  constructor(rowsSelector, templatesSelector) {
    $(document).on('click', '.js-add-range', () => {
      this.$rows = $(rowsSelector);
      this.$templates = $(templatesSelector);
      this.addColumn();
    });

    return {};
  }

  /**
   * Add new column of inputs to table
   */
  addColumn() {
    const currentRange = this.$rows.last().find('td:not(:first-child)').length;
    const $inputFrom = this.$templates.find('#js-range-from-template');
    const $inputTo = this.$templates.find('#js-range-to-template');
    const $inputPrice = this.$templates.find('#js-price-template');

    for (let i = 0; i < Object.keys(this.$rows).length; i++) {
      const $row = $(this.$rows[i]);

      if ($row.hasClass('js-range-from')) {
        const inputFrom = ($inputFrom.get(0).outerHTML).replace(/__RANGE_INDEX__/, currentRange).replace(/disabled/, '');
        $row.append(`<td>${inputFrom}</td>`);
      } else if ($row.hasClass('js-range-to')) {
        const inputTo = ($inputTo.get(0).outerHTML).replace(/__RANGE_INDEX__/, currentRange).replace(/disabled/, '');
        $row.append(`<td>${inputTo}</td>`);
      } else {
        const inputPrice = ($inputPrice.get(0).outerHTML).replace(/__RANGE_INDEX__/, currentRange).
          replace(/__ZONE_ID__/, $row.data('zone-id')).replace(/disabled/, '');
        $row.append(`<td>${inputPrice}</td>`);
      }
    }
  }
}

