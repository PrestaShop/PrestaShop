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

import ProductMap from '@pages/product/product-map';
import EventEmitter from '@components/event-emitter';
import Router from '@components/router';
import ConfirmModal from '@components/modal';
import ProductEventMap from '@pages/product/product-event-map';
import {isUndefined} from '@PSTypes/typeguard';

const {$} = window;

interface FeatureValue {
  id: number,
  value: string,
}

export default class FeatureValuesManager {
  router: Router;

  eventEmitter: typeof EventEmitter;

  $controlsContainer: JQuery;

  $featureSelector: JQuery;

  $featureValueSelector: JQuery;

  $newCustomValuesContainers: JQuery;

  $newCustomValueInputs: JQuery;

  $featureValueLoader: JQuery;

  $addFeatureValueButton: JQuery;

  $collectionContainer: JQuery;

  $collectionRowsContainer: JQuery;

  featureValues: Array<FeatureValue[]> = [];

  /**
   * @param eventEmitter {EventEmitter}
   */
  constructor(eventEmitter: typeof EventEmitter) {
    this.router = new Router();
    this.eventEmitter = eventEmitter;
    this.$controlsContainer = $(ProductMap.featureValues.controlsContainer);
    this.$featureSelector = $(ProductMap.featureValues.featureSelect, this.$controlsContainer);
    this.$featureSelector.select2();
    this.$featureValueSelector = $(ProductMap.featureValues.featureValueSelect, this.$controlsContainer);
    this.$featureValueSelector.select2();
    this.$newCustomValuesContainers = $(ProductMap.featureValues.newCustomValuesContainers, this.$controlsContainer);
    this.$newCustomValueInputs = $(ProductMap.featureValues.newCustomValueInputs, this.$newCustomValuesContainers);
    this.$addFeatureValueButton = $(ProductMap.featureValues.addFeatureValue, this.$controlsContainer);
    this.$featureValueLoader = $(ProductMap.featureValues.featureValueLoader, this.$controlsContainer);

    this.$collectionContainer = $(ProductMap.featureValues.collectionContainer);
    this.$collectionRowsContainer = $(ProductMap.featureValues.collectionRowsContainer);

    this.watchFeatureSelectors();
    this.watchDeleteButtons();
    this.watchAddButton();
  }

  private watchAddButton(): void {
    this.$addFeatureValueButton.on('click', () => {
      // Check feature value first, placeholder can not be added
      const $selectedFeatureValue = $('option:selected', this.$featureValueSelector);
      const featureValueId = <string> $selectedFeatureValue.val();

      // Placeholder selected nothing to do
      if (featureValueId === '0') {
        return;
      }

      // Custom value selected but no value in inputs
      if (featureValueId === '-1') {
        const newCustomValues = this.getNewCustomValues();

        if (newCustomValues.length === 0) {
          return;
        }
      }

      // Get selected values first
      const $selectedFeature = $('option:selected', this.$featureSelector);
      const featureId = <string> $selectedFeature.val();
      const featureName = <string> $selectedFeature.text();

      // Check if feature collection is already present for the selected feature
      const $featureRow = $(ProductMap.featureValues.featureRowByFeatureId(featureId), this.$collectionRowsContainer);

      // Feature collection not present we must add it
      if (!$featureRow.length) {
        const featurePrototype = this.$collectionContainer.data('prototype');
        const featurePrototypeName = this.$collectionContainer.data('prototypeName');
        const newIndex = $(ProductMap.featureValues.featureRow, this.$collectionRowsContainer).length;

        const $newFeatureRow = $(featurePrototype.replace(new RegExp(featurePrototypeName, 'g'), newIndex)).first();
        $newFeatureRow.attr('feature-id', featureId);
        this.$collectionRowsContainer.append($newFeatureRow);
        $(ProductMap.featureValues.featureIdInput, $newFeatureRow).val(featureId);
        $(ProductMap.featureValues.featureNameInput, $newFeatureRow).val(featureName);
        this.addFeatureValueRow($newFeatureRow, featureId, featureName, featureValueId);
      } else {
        this.addFeatureValueRow($featureRow, featureId, featureName, featureValueId);
      }

      // Display list that can't be empty anymore
      this.$collectionContainer.removeClass('d-none');
      this.resetControls();
    });
  }

  private getNewCustomValues(): string[] {
    const newCustomValues: string[] = [];
    $('.js-locale-input', this.$newCustomValuesContainers).each((index: number, localeInputContainer: HTMLElement) => {
      const localeInput = localeInputContainer.querySelector<HTMLInputElement>('input.form-control');

      if (!isUndefined(localeInputContainer.dataset.langId) && localeInput && localeInput.value !== '') {
        const langId = parseInt(localeInputContainer.dataset.langId, 10);
        newCustomValues[langId] = localeInput.value;
      }
    });

    return newCustomValues;
  }

  private addFeatureValueRow($featureRow: JQuery, featureId: string, featureName: string, featureValueId: string): void {
    const rowValuePrototype = $featureRow.data('prototype');
    const rowValuePrototypeName = $featureRow.data('prototypeName');
    const $featureValueRows = $(ProductMap.featureValues.featureValueRowByFeatureId(featureId), this.$collectionRowsContainer);

    const $newFeatureValueRow = $(rowValuePrototype.replace(new RegExp(rowValuePrototypeName, 'g'), $featureValueRows.length));
    $newFeatureValueRow.attr('feature-id', featureId);

    if ($featureValueRows.length === 0) {
      // If no previous feature values the new one is added after the feature row (which is invisible)
      $featureRow.after($newFeatureValueRow);
    } else {
      // If some previous values were present the new one is added after the last value from the feature
      $featureValueRows.last().after($newFeatureValueRow);
    }

    const $selectedFeatureValue = $('option:selected', this.$featureValueSelector);
    const featureValueName = <string> $selectedFeatureValue.text();

    if (featureValueId !== '-1') {
      $(ProductMap.featureValues.featureValueIdInput, $newFeatureValueRow).val(featureValueId);
      $(ProductMap.featureValues.featureValueNameInput, $newFeatureValueRow).val(featureValueName);
      $(ProductMap.featureValues.featureValueNamePreview, $newFeatureValueRow).text(featureValueName);
      $(ProductMap.featureValues.isCustomInput, $newFeatureValueRow).val(0);
      $(ProductMap.featureValues.customValuesContainer, $newFeatureValueRow).hide();
    } else {
      $(ProductMap.featureValues.featureValueIdInput, $newFeatureValueRow).val('');
      $(ProductMap.featureValues.featureValueNameInput, $newFeatureValueRow).val('');
      $(ProductMap.featureValues.featureValueNamePreview, $newFeatureValueRow).text('');
      $(ProductMap.featureValues.isCustomInput, $newFeatureValueRow).val(1);
      $(ProductMap.featureValues.customValuesContainer, $newFeatureValueRow).show();

      const newCustomValues = this.getNewCustomValues();
      newCustomValues.forEach((customValue: string, langId: number) => {
        const customValueInputSelector = ProductMap.featureValues.customValueByLangId(langId);
        const $customValueInput = $(customValueInputSelector, $newFeatureValueRow);
        $customValueInput.val(customValue);
      });
    }
    $(ProductMap.featureValues.featureNameCell, $newFeatureValueRow).text(featureName);
  }

  private resetControls(): void {
    this.$featureSelector.val(0).trigger('change');
    this.$featureValueSelector.empty();
    this.$featureValueSelector.val('').trigger('change');
    this.$featureValueSelector.prop('disabled', true);
    this.$newCustomValueInputs.val('');
  }

  private watchDeleteButtons(): void {
    $(this.$collectionRowsContainer).on('click', ProductMap.featureValues.deleteFeatureValue, (event) => {
      const $deleteButton = $(event.currentTarget);
      const $collectionRow = $deleteButton.closest(ProductMap.featureValues.featureValueRow);
      const modal = new (ConfirmModal as any)(
        {
          id: 'modal-confirm-delete-feature-value',
          confirmTitle: $deleteButton.data('modal-title'),
          confirmMessage: $deleteButton.data('modal-message'),
          confirmButtonLabel: $deleteButton.data('modal-apply'),
          closeButtonLabel: $deleteButton.data('modal-cancel'),
          confirmButtonClass: 'btn-danger',
          closable: true,
        },
        () => {
          const featureId = <string> $collectionRow.attr('feature-id');
          $collectionRow.remove();

          // Check if the collection has some values left
          const $valueRows = $(ProductMap.featureValues.featureValueRowByFeatureId(featureId), this.$collectionRowsContainer);

          if ($valueRows.length === 0) {
            const $featureRow = $(ProductMap.featureValues.featureRowByFeatureId(featureId), this.$collectionRowsContainer);
            $featureRow.remove();
          }
          this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
          this.$collectionContainer.toggleClass('d-none', this.$collectionRowsContainer.children().length === 0);
        },
      );
      modal.show();
    });
  }

  private watchFeatureSelectors(): void {
    this.$featureSelector.on('change', () => {
      this.$addFeatureValueButton.prop('disabled', true);
      const featureId = Number(this.$featureSelector.val());
      this.renderFeatureValueChoices(featureId);
    });

    this.$featureValueSelector.on('change', () => this.updateAddButtonState());
    this.$newCustomValueInputs.on('change keyup', () => this.updateAddButtonState());
  }

  private updateAddButtonState(): void {
    const featureId = Number(this.$featureSelector.val());
    const featureValueId = Number(this.$featureValueSelector.val());
    const newCustomValues = this.getNewCustomValues();

    this.$newCustomValuesContainers.toggleClass('d-none', featureId === 0 || featureValueId !== -1);

    if (featureValueId !== -1) {
      this.$newCustomValueInputs.val('');
    }
    this.$addFeatureValueButton.prop('disabled',
      featureId === 0
      || featureValueId === 0
      || (featureValueId === -1 && newCustomValues.length === 0),
    );
  }

  private renderFeatureValueChoices(featureId: number): void {
    this.$featureValueSelector.val('');
    this.$featureValueSelector.trigger('change');
    this.$featureValueSelector.prop('disabled', true);

    if (!featureId) {
      return;
    }

    if (this.featureValues[featureId]) {
      this.doRenderFeatureValueChoices(this.featureValues[featureId]);
    } else {
      // Hide select2 and display loader
      const $featureSelect2Container = $(`#select2-${this.$featureValueSelector.prop('id')}-container`);
      const $featureSelect2 = $featureSelect2Container.parents('.select2-container');
      this.$featureValueLoader.removeClass('d-none');
      $featureSelect2.addClass('d-none');

      $.get(this.router.generate('admin_feature_get_feature_values', {featureId}))
        .then((featureValuesData: FeatureValue[]) => {
          this.featureValues[featureId] = featureValuesData;
          this.doRenderFeatureValueChoices(this.featureValues[featureId]);
          this.$featureValueLoader.addClass('d-none');
          $featureSelect2.removeClass('d-none');
        });
    }
  }

  private doRenderFeatureValueChoices(featureValuesData: FeatureValue[]): void {
    this.$featureValueSelector.empty();
    if (featureValuesData.length) {
      const selectedFeatureValues = this.getFeatureValueIds();
      // First add placeholder and custom value options
      this.addFeatureValue(this.$featureValueSelector.data('placeholderLabel'), 0);
      this.addFeatureValue(this.$featureValueSelector.data('customValueLabel'), -1);

      // Then loop through the pre-defined feature values
      $.each(featureValuesData, (index, featureValue) => {
        if (featureValue.id !== 0 && !selectedFeatureValues.includes(featureValue.id)) {
          this.addFeatureValue(featureValue.value, featureValue.id);
        }
      });
    }

    this.$featureValueSelector.prop('disabled', featureValuesData.length === 0);
    this.$featureValueSelector.val(0).trigger('change');
    this.$featureValueSelector.select2();
  }

  private getFeatureValueIds(): number[] {
    const featureValueIds: number[] = [];
    $(ProductMap.featureValues.featureValueIdInput, this.$collectionRowsContainer).each((index, featureValueInput) => {
      if (featureValueInput instanceof HTMLInputElement) {
        featureValueIds.push(parseInt(<string> featureValueInput.value, 10));
      }
    });

    return featureValueIds;
  }

  private addFeatureValue(valueLabel: string, value: number): void {
    this.$featureValueSelector.append(
      $('<option></option>')
        .attr('value', value)
        .text(valueLabel),
    );
  }
}
