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
  constructor(
    rangesTable,
    rangePriceTemplate,
    rangeFromTemplate,
    rangeToTemplate,
    addRangeBtn,
    removeRangeBtn,
    rangeRemovingBtnRow,
    zoneCheckbox
  ) {
    this.rangeIndex = 1;
    this.rangesTable = rangesTable;
    this.rangeRemovingBtnRow = rangeRemovingBtnRow;
    this.removeRangeBtn = removeRangeBtn;
    this.zoneCheckbox = zoneCheckbox;

    this.$addRangeBtn = $(addRangeBtn);
    this.$rows = $(`${rangesTable} tr:not(${rangeRemovingBtnRow})`);
    this.$rangePriceTemplate = $(rangePriceTemplate);
    this.$rangeFromTemplate = $(rangeFromTemplate);
    this.$rangeToTemplate = $(rangeToTemplate);

    this.handle();

    return {};
  }

  /**
   * Initiates the handler
   */
  handle() {
    this.$addRangeBtn.on('click', () => {
      this.addRangeColumn();
    });

    $(document).on('click', this.removeRangeBtn, (event) => {
      this.removeRangeColumn(event);
    });
  }

  /**
   * Add new column of inputs to table
   */
  addRangeColumn() {

    for (let i = 0; i < Object.keys(this.$rows).length; i++) {
      const $row = $(this.$rows[i]);

      if ($row.hasClass('js-range-from')) {

        // replace range-from index placeholder with actual value and enable input
        const inputFrom = (this.$rangeFromTemplate.get(0).outerHTML)
          .replace(/__RANGE_INDEX__/, this.rangeIndex)
          .replace(/disabled=""/, '');

        // append table data with input from template
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputFrom}</td>`);
      } else if ($row.hasClass('js-range-to')) {

        // replace range-to index placeholder with actual value and enable input
        const inputTo = (this.$rangeToTemplate.get(0).outerHTML)
          .replace(/__RANGE_INDEX__/, this.rangeIndex)
          .replace(/disabled=""/, '');

        // append table data with input from template
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputTo}</td>`);
      } else {


        // replace price index and zone id placeholders with actual values and enable input
        let inputPrice = (this.$rangePriceTemplate.get(0).outerHTML)
          .replace(/__RANGE_INDEX__/, this.rangeIndex)
          .replace(/disabled=""/, '')
          .replace(/__ZONE_ID__/g, $row.data('zone-id'));


        // check if corresponding zone is checked to remove readonly attr
        if ($row.find(this.zoneCheckbox).is(':checked')) {
          inputPrice = inputPrice.replace(/readonly=""/, '');
        }

        // append table data with input from template
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputPrice}</td>`);
      }
    }

    this.appendRangeRemovalButton(this.rangeIndex);

    // increment rangeIndex value by one to keep range identification unique
    this.rangeIndex += 1;
  }

  // append button which removes corresponding range column
  appendRangeRemovalButton(rangeIndex) {
    $(this.rangeRemovingBtnRow).removeClass('d-none');
    $(this.rangeRemovingBtnRow).append(
      `<td data-range-index="${rangeIndex}">${$(this.removeRangeBtn).get(0).outerHTML}</td>`,
    );
  }

  // remove range column
  removeRangeColumn(event) {
    $(this.rangesTable).find(`*[data-range-index="${$(event.currentTarget.parentElement).data('range-index')}"]`)
      .remove();
  }
}

