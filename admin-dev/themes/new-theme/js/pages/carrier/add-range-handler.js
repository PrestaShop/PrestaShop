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
 * Responsible for adding and removing range columns
 */
export default class AddRangeHandler {
  constructor(rangesTable, templatesSelector, appendBtnSelector) {
    this.rangeIndex = 1;

    $(document).on('click', '.js-add-range', () => {
      this.rangesTableSelector = rangesTable;
      this.appendButtonsSelector = appendBtnSelector;

      this.$rows = $(`${rangesTable} tr:not(${appendBtnSelector})`);
      this.$templates = $(templatesSelector);
      this.addRangeColumn();
    });
    $(document).on('click', '.js-remove-range', (event) => {
      this.removeRangeColumn(event);
    });

    return {};
  }

  /**
   * Add new column of inputs to table
   */
  addRangeColumn() {
    const $inputFrom = this.$templates.find('#js-range-from-template > div');
    const $inputTo = this.$templates.find('#js-range-to-template > div');
    const $inputPrice = this.$templates.find('#js-price-template > div');

    for (let i = 0; i < Object.keys(this.$rows).length; i++) {
      const $row = $(this.$rows[i]);

      if ($row.hasClass('js-range-from')) {
        const inputFrom = ($inputFrom.get(0).outerHTML).replace(/__RANGE_INDEX__/, this.rangeIndex).replace(/disabled=""/, '');
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputFrom}</td>`);
      } else if ($row.hasClass('js-range-to')) {
        const inputTo = ($inputTo.get(0).outerHTML).replace(/__RANGE_INDEX__/, this.rangeIndex).replace(/disabled=""/, '');
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputTo}</td>`);
      } else {
        const inputPrice = ($inputPrice.get(0).outerHTML).replace(/__RANGE_INDEX__/, this.rangeIndex).replace(/disabled=""/, '')
          .replace(/__ZONE_ID__/g, $row.data('zone-id'));
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputPrice}</td>`);
      }
    }
    this.appendRangeRemovalButton(this.rangeIndex);
    this.rangeIndex += 1;
  }

  appendRangeRemovalButton(rangeIndex) {
    $(this.appendButtonsSelector).removeClass('d-none');
    $(this.appendButtonsSelector).append(
      `<td data-range-index="${rangeIndex}">${this.$templates.find('.js-remove-range').get(0).outerHTML}</td>`,
    );
  }

  removeRangeColumn(event) {
    $(this.rangesTableSelector).find(`*[data-range-index="${$(event.currentTarget.parentElement).data('range-index')}"]`)
      .remove();
  }
}

