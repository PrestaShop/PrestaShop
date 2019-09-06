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
 * Responsible for manipulating ranges table
 */
export default class RangesTable {
  constructor(
    rangesTable,
    rangeRow,
    rangePriceTemplate,
    rangeFromTemplate,
    rangeToTemplate,
    addRangeBtn,
    removeRangeBtn,
    rangeRemovingBtnRow,
    zoneCheckbox,
  ) {
    this.rangeIndex = 1;
    this.rangeRemovingBtnRow = rangeRemovingBtnRow;
    this.removeRangeBtn = removeRangeBtn;
    this.$zoneCheckbox = $(zoneCheckbox);
    this.rangeRow = rangeRow;

    this.$addRangeBtn = $(addRangeBtn);
    this.$rangesTable = $(rangesTable);
    this.$rows = $(`${rangesTable} tr:not(${rangeRemovingBtnRow})`);
    this.$rangePriceTemplate = $(rangePriceTemplate);
    this.$rangeFromTemplate = $(rangeFromTemplate);
    this.$rangeToTemplate = $(rangeToTemplate);
  }

  addRange() {
    for (let i = 0; i < Object.keys(this.$rows).length; i++) {
      const $row = $(this.$rows[i]);

      if ($row.hasClass('js-range-from')) {

        // replace range-from index placeholder with actual value and enable input
        const inputFrom = (this.$rangeFromTemplate.get(0).outerHTML)
          .replace(/__RANGE_INDEX__/g, this.rangeIndex)
          .replace(/disabled=""/, '');

        // append table data with input from template
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputFrom}</td>`);
      } else if ($row.hasClass('js-range-to')) {

        // replace range-to index placeholder with actual value and enable input
        const inputTo = (this.$rangeToTemplate.get(0).outerHTML)
          .replace(/__RANGE_INDEX__/g, this.rangeIndex)
          .replace(/disabled=""/, '');

        // append table data with input from template
        $row.append(`<td data-range-index="${this.rangeIndex}">${inputTo}</td>`);
      } else {
        // replace price index and zone id placeholders with actual values and enable input
        let inputPrice = (this.$rangePriceTemplate.get(0).outerHTML)
          .replace(/__RANGE_INDEX__/g, this.rangeIndex)
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

    this._appendRangeRemovalButton(this.rangeIndex);

    // increment rangeIndex value by one to keep range identification unique
    this.rangeIndex += 1;
  }

  removeRange(event) {
    this.$rangesTable.find(`*[data-range-index="${$(event.currentTarget.parentElement).data('range-index')}"]`)
      .remove();
  }

  showZonesOnly() {
    this.$rangesTable.find('td').fadeOut();
    this.$rangesTable.find(this.rangeRow).fadeOut();
    this.$addRangeBtn.fadeOut();
  }

  showWholeTable() {
    this.$rangesTable.find('td').fadeIn();
    this.$rangesTable.find(this.rangeRow).fadeIn();
    this.$addRangeBtn.fadeIn();
  }

  selectAllZones(event) {
    const isSelectAllChecked = $(event.target).is(':checked');

    this.$zoneCheckbox.not(event.target).prop('checked', isSelectAllChecked);
  }

  /**
   * Fills column inputs with provided value
   *
   * @param columnIndex
   * @param value
   */
  fillAllZonesPrice(columnIndex, value) {
    const inputsToFill = this.$rangesTable.find(`input[data-range-index="${columnIndex}"]`);
    inputsToFill.val(value);
  }

  /**
   * Disables inputs that depends from checked value
   *
   * @param event
   */
  disableZoneInputs(event) {
    $.each($(event.target), (i, input) => {
      const isChecked = $(event.target).is(':checked');
      const zoneId = $(input).val();
      $('#js-carrier-ranges').find(`div[data-zone-id='${zoneId}'] input`).prop('readonly', !isChecked);
    });
  }

  /**
   * Append button which removes corresponding range column
   *
   * @param rangeIndex
   */
  _appendRangeRemovalButton(rangeIndex) {
    $(this.rangeRemovingBtnRow).removeClass('d-none');
    $(this.rangeRemovingBtnRow).append(
      `<td data-range-index="${rangeIndex}">${$(this.removeRangeBtn).get(0).outerHTML}</td>`,
    );
  }
}

