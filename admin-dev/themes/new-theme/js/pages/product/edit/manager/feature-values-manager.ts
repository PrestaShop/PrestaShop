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

const {$} = window;

export default class FeatureValuesManager {
  router: Router;

  eventEmitter: typeof EventEmitter;

  $controlsContainer: JQuery;

  $featureSelector: JQuery;

  $featureValueSelector: JQuery;

  $addFeatureValueButton: JQuery;

  $collectionContainer: JQuery;

  $collectionRowsContainer: JQuery;

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
    this.$addFeatureValueButton = $(ProductMap.featureValues.addFeatureValue, this.$controlsContainer);

    this.$collectionContainer = $(ProductMap.featureValues.collectionContainer);
    this.$collectionRowsContainer = $(ProductMap.featureValues.collectionRowsContainer);

    this.watchFeatureSelectors();
    this.watchDeleteButtons();
    this.watchAddButton();
  }

  private watchAddButton(): void {
    this.$addFeatureValueButton.on('click', () => {
      const prototype = this.$collectionContainer.data('prototype');
      const prototypeName = this.$collectionContainer.data('prototypeName');
      const newIndex = $(ProductMap.featureValues.collectionRow, this.$collectionRowsContainer).length;

      const $newRow = $(prototype.replace(new RegExp(prototypeName, 'g'), newIndex));
      this.$collectionRowsContainer.append($newRow);

      const $selectedFeature = $('option:selected', this.$featureSelector);
      const featureId = <string> $selectedFeature.val();
      const featureName = <string> $selectedFeature.text();
      $(ProductMap.featureValues.featureIdInput, $newRow).val(featureId);
      $(ProductMap.featureValues.featureNameInput, $newRow).val(featureName);
      $(ProductMap.featureValues.featureNamePreview, $newRow).text(featureName);

      const $selectedFeatureValue = $('option:selected', this.$featureValueSelector);
      const featureValueId = <string> $selectedFeatureValue.val();
      const featureValueName = <string> $selectedFeatureValue.text();

      if (featureValueId !== '-1') {
        $(ProductMap.featureValues.featureValueIdInput, $newRow).val(featureValueId);
        $(ProductMap.featureValues.featureValueNameInput, $newRow).val(featureValueName);
        $(ProductMap.featureValues.featureValueNamePreview, $newRow).text(featureValueName);
        $(ProductMap.featureValues.isCustomInput, $newRow).val(0);
        $(ProductMap.featureValues.customValuesContainer, $newRow).hide();
      } else {
        $(ProductMap.featureValues.featureValueIdInput, $newRow).val('');
        $(ProductMap.featureValues.featureValueNameInput, $newRow).val('');
        $(ProductMap.featureValues.featureValueNamePreview, $newRow).text('');
        $(ProductMap.featureValues.isCustomInput, $newRow).val(1);
        $(ProductMap.featureValues.customValuesContainer, $newRow).show();
      }

      // Display list that can't be empty anymore
      this.$collectionContainer.removeClass('d-none');
      this.resetControls();
    });
  }

  private resetControls(): void {
    this.$featureSelector.val(0).trigger('change');
    this.$featureValueSelector.empty();
    this.$featureValueSelector.val('').trigger('change');
    this.$featureValueSelector.prop('disabled', true);
  }

  private watchDeleteButtons(): void {
    $(this.$collectionRowsContainer).on('click', ProductMap.featureValues.deleteFeatureValue, (event) => {
      const $deleteButton = $(event.currentTarget);
      const $collectionRow = $deleteButton.closest(ProductMap.featureValues.collectionRow);
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
          $collectionRow.remove();
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
      const idFeature = Number(this.$featureSelector.val());
      this.renderFeatureValueChoices(idFeature);
    });

    this.$featureValueSelector.on('change', () => {
      const idFeature = Number(this.$featureSelector.val());
      const idFeatureValue = Number(this.$featureValueSelector.val());
      this.$addFeatureValueButton.prop('disabled', idFeature === 0 || idFeatureValue === 0);
    });
  }

  private renderFeatureValueChoices(idFeature: number): void {
    this.$featureValueSelector.val('');
    this.$featureValueSelector.trigger('change');
    this.$featureValueSelector.prop('disabled', true);

    if (!idFeature) {
      return;
    }

    $.get(this.router.generate('admin_feature_get_feature_values', {idFeature}))
      .then((featureValuesData) => {
        this.$featureValueSelector.empty();
        if (featureValuesData.length) {
          const selectedFeatureValues = this.getFeatureValueIds();
          this.addFeatureValue(this.$featureValueSelector.data('customValueLabel'), -1);
          $.each(featureValuesData, (index, featureValue) => {
            if (featureValue.id !== 0 && !selectedFeatureValues.includes(featureValue.id)) {
              this.addFeatureValue(featureValue.value, featureValue.id);
            }
          });
        }

        this.$featureValueSelector.prop('disabled', featureValuesData.length === 0);
        this.$featureValueSelector.val(-1).trigger('change');
        this.$featureValueSelector.select2();
      });
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
