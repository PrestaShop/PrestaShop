/**
* Copyright since 2007 PrestaShop SA and Contributors
* PrestaShop is an International Registered Trademark & Property of PrestaShop SA
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.md.
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
* needs please refer to https://devdocs.prestashop.com/ for more information.
*
* @author    PrestaShop SA and Contributors <contact@prestashop.com>
* @copyright Since 2007 PrestaShop SA and Contributors
* @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
*/

import {EventEmitter} from 'events';
import CarrierFormMap from '@pages/carrier/form/carrier-form-map';
import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';
import ConfirmModal from '@js/components/modal/confirm-modal';
import {Range} from '@pages/carrier/form/types';

const {$} = window;

/**
 * This component is used in carrier form page to manage the behavior of the form:
 * - Selections of zones, ranges and ranges prices
 * - Update form when the carrier shipping method change
 */
export default class CarrierFormManager {
  eventEmitter: EventEmitter;

  currentShippingSymbol: string;

  $zonesInput: JQuery;

  $rangesInput: JQuery;

  $shippingMethodInput: JQuery;

  $freeShippingInput: JQuery;

  /**
   * @param {EventEmitter} eventEmitter
   */
  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.currentShippingSymbol = '';

    // Initialize dom elements
    this.$zonesInput = $(CarrierFormMap.zonesInput);
    this.$rangesInput = $(CarrierFormMap.rangesInput);
    this.$shippingMethodInput = $(CarrierFormMap.shippingMethodInput);
    this.$freeShippingInput = $(CarrierFormMap.freeShippingInput);

    // Initialize form
    this.initForm();

    // Initialize listeners
    this.initListeners();
  }

  private initForm() {
    // First toggle shipping related controls
    this.refreshFreeShipping();
    // Then, we need to refresh the shipping method symbol
    this.refreshCurrentShippingSymbol();

    this.onChangeZones();
  }

  private initListeners() {
    this.$zonesInput.on('change', () => this.onChangeZones());
    this.$freeShippingInput.on('change', () => {
      this.refreshFreeShipping();
      this.onChangeZones();
    });
    this.$shippingMethodInput.on('change', () => this.refreshCurrentShippingSymbol());
    $(CarrierFormMap.zonesContainer).on('click', CarrierFormMap.deleteZoneButton, (e:Event) => this.onDeleteZone(e));
    this.eventEmitter.on(CarrierFormEventMap.rangesUpdated, (ranges: Range[]) => this.onChangeRanges(ranges));
  }

  private refreshFreeShipping(): void {
    const isFreeShipping = $(`${CarrierFormMap.freeShippingInput}:checked`).val() === '1';

    CarrierFormMap.shippingControls.forEach((inputId: string) => {
      const $inputGroup = $(inputId).closest('.form-group');
      $inputGroup.toggleClass('d-none', isFreeShipping);
      $(inputId).prop('required', !isFreeShipping);
    });
  }

  private refreshCurrentShippingSymbol() {
    // First, we need to get the units of the selected shipping method
    const shippingMethodUnits = $(CarrierFormMap.shippingMethodRow).data('units');
    const shippingMethodValue = <number> this.$shippingMethodInput.filter(':checked').first().val() || -1;
    this.currentShippingSymbol = shippingMethodUnits[shippingMethodValue] || '?';

    // Then, we need to emit an event to update this symbol to other components
    this.eventEmitter.emit(CarrierFormEventMap.shippingMethodChange, this.currentShippingSymbol);

    // Finally, we need to update the ranges names with the new symbol
    $(CarrierFormMap.rangeRow).each((_, rangeRow: HTMLElement) => {
      const $rangeRow = $(rangeRow);
      const $rangeName = $rangeRow.find(CarrierFormMap.rangeNamePreview);
      const $rangeNameHidden = $rangeRow.find(CarrierFormMap.rangeNameInput);
      const from = $rangeRow.find(CarrierFormMap.rangeFromInput).val();
      const to = $rangeRow.find(CarrierFormMap.rangeToInput).val();
      const rangeName = `${from}${this.currentShippingSymbol} - ${to}${this.currentShippingSymbol}`;
      $rangeName.text(rangeName);
      $rangeNameHidden.val(rangeName);
    });
  }

  private onChangeZones() {
    // First, we retrieve the zones actually displayed and selected
    const $zonesContainer = $(CarrierFormMap.zonesContainer);
    const $zonesRows = $(CarrierFormMap.zoneRow);
    const zones = <string[]> this.$zonesInput.val() ?? [];

    // First, we need to delete the zones that are not selected and already displayed
    // (and we keep the zones that are already displayed)
    const zonesAlreadyDisplayed = <string[]>[];
    $zonesRows.each((_, zoneRow: HTMLElement) => {
      const $zoneRow = $(zoneRow);
      const zoneId = $zoneRow.find(CarrierFormMap.zoneIdInput).val()?.toString();

      if (zoneId !== undefined) {
        if (!zones.includes(zoneId)) {
          $zoneRow.remove();
        } else {
          zonesAlreadyDisplayed.push(zoneId);
        }
      }
    });

    // Then, we need to add the zones that are selected but not displayed
    const zonePrototype = $zonesContainer.data('prototype');
    zones.forEach((zoneId: string) => {
      if (!zonesAlreadyDisplayed.includes(zoneId)) {
        // We create new zone row by duplicating the prototype and replacing the zone index
        const prototype = zonePrototype.replace(/__zone__/g, $(CarrierFormMap.zoneRow).length);

        // We need to update the zone id and the zone name
        const $prototype = $(prototype);
        $prototype.attr('data-zone-id', zoneId);
        $prototype.find(CarrierFormMap.zoneIdInput).val(zoneId);
        $prototype.find(CarrierFormMap.zoneNamePreview).text(this.$zonesInput.find(CarrierFormMap.zoneIdOption(zoneId)).text());

        // We append the new zone row into the zones container
        $zonesContainer.append($prototype);

        // Next, we need to prepare the ranges for this zone
        const $rangeContainer = $prototype.find(CarrierFormMap.rangesContainer);
        const $rangeContainerBody = $prototype.find(CarrierFormMap.rangesContainerBody);
        const rangePrototype = $rangeContainer.data('prototype');
        // @ts-ignore
        const ranges = <Range[]>JSON.parse(this.$rangesInput.val() || '[]');

        // For each range selected, we need to create a new range row with the range prototype
        ranges.forEach((range: Range, index) => {
          // Then, we append the new range row into the range container
          const $rPrototype = this.prepareRangePrototype(rangePrototype, index, range);
          $rangeContainerBody.append($rPrototype);
        });
      }
    });
  }

  private onDeleteZone(e: Event) {
    e.preventDefault();

    // We need to get the zone id to delete
    const $currentTarget = $(e.currentTarget as HTMLElement);
    const $currentZoneRow = $currentTarget.parents(CarrierFormMap.zoneRow);
    const idZoneToDelete = $currentZoneRow.children(CarrierFormMap.zoneIdInput).val();

    // We need to display a confirmation modal before deleting the zone
    const modal = new ConfirmModal(
      {
        id: 'modal-confirm-submit-feature-flag',
        confirmButtonClass: 'btn-danger',
        confirmTitle: $currentTarget.data('modal-title'),
        confirmMessage: '',
        confirmButtonLabel: $currentTarget.data('modal-confirm'),
        closeButtonLabel: $currentTarget.data('modal-cancel'),
      },
      () => {
        // If, the user confirms the deletion, we need to remove the zone
        // First, we need to remove this zone from the zones
        let zones = <string[]> this.$zonesInput.val() || [];
        zones = zones.filter((zoneId: string) => zoneId !== idZoneToDelete);

        // And update the zones selected values and trigger the zones selector change event
        this.$zonesInput.val(zones);
        this.$zonesInput.change();
      },
    );
    modal.show();
  }

  private onChangeRanges(ranges: Range[]) {
    // We retrieve all ranges containers in the page
    const $rangesContainerBodies = $(CarrierFormMap.rangesContainerBody);

    // For each range container, we need to update the ranges
    $rangesContainerBodies.each((_, zoneRangesContainer: HTMLElement) => {
      // First, we need to save all values for this range.
      const $zoneRangesContainerBody = $(zoneRangesContainer);
      const pricesRanges = $(zoneRangesContainer).find(CarrierFormMap.rangeRow).map((__, rangeRow: HTMLElement) => {
        const $rangeRow = $(rangeRow);
        const from = parseFloat($rangeRow.find(CarrierFormMap.rangeFromInput).val()?.toString() || '0');
        const to = parseFloat($rangeRow.find(CarrierFormMap.rangeToInput).val()?.toString() || '0');
        const price = $rangeRow.find(CarrierFormMap.rangePriceInput).val() || '';

        return {from, to, price};
      });

      // Then, we reset the ranges container
      $zoneRangesContainerBody.html('');

      // and, we need to add all the ranges selected
      const rangePrototype = $zoneRangesContainerBody.closest(CarrierFormMap.rangesContainer).data('prototype');
      ranges.forEach((range: Range, index) => {
        // First, we need to prepare the range prototype
        const $rPrototype = this.prepareRangePrototype(rangePrototype, index, range);

        // Then, we need to search the previous price if exist (oldFrom = newFrom OR oldTo = newTo)
        let price = '';

        for (let i = 0; i < pricesRanges.length; i += 1) {
          if (pricesRanges[i].from === range.from || pricesRanges[i].to === range.to) {
            price = pricesRanges[i].price.toString();
            break;
          }
        }

        // We set the previous value for this range if it exists
        // @ts-ignore
        $rPrototype.find(CarrierFormMap.rangePriceInput)
          .attr('data-from', range.from || '0')
          .attr('data-to', range.to || '0')
          .val(price);
        // Then, we append the new range row into the range container
        $zoneRangesContainerBody.append($rPrototype);
      });
    });
  }

  private prepareRangePrototype(rangePrototype: string, index: number, range: Range): JQuery {
    // We prepare the range prototype by replacing the range index, and setting the range values
    const $rPrototype = $(rangePrototype.replace(/__range__/g, index.toString()));
    $rPrototype.find(CarrierFormMap.rangeFromInput).val(range.from || '0');
    $rPrototype.find(CarrierFormMap.rangeToInput).val(range.to || '0');
    $rPrototype.find(CarrierFormMap.rangeNamePreview)
      .text(`${range.from}${this.currentShippingSymbol} - ${range.to}${this.currentShippingSymbol}`);

    // We return the prototype well formed
    return $rPrototype;
  }
}
