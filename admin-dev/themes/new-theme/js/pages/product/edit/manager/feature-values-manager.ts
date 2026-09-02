/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import EventEmitter from '@components/event-emitter';
import Router from '@components/router';
import ConfirmModal from '@components/modal';
import ProductEventMap from '@pages/product/product-event-map';
import {isUndefined} from '@components/typeguard';

const {$} = window;

interface FeatureValue {
  id: number,
  value: string,
}

/**
 * Set of CSS selectors used by the widget. Both the product (`ProductMap.featureValues`) and the
 * combination (`ProductMap.combinationFeatureValues`) maps implement this shape, which is what
 * allows the very same manager to drive both widgets.
 */
export interface FeatureValuesMap {
  controlsContainer: string,
  collectionContainer: string,
  collectionRowsContainer: string,
  featureSelect: string,
  featureValueSelect: string,
  newCustomValuesContainers: string,
  newCustomValueInputs: string,
  featureRow: string,
  featureRowByFeatureId: (featureId: string) => string,
  featureValueRow: string,
  featureIdInput: string,
  featureNameInput: string,
  featureNameCell: string,
  featureValueRowByFeatureId: (featureId: string) => string,
  featureValueIdInput: string,
  featureValueNameInput: string,
  featureValueNamePreview: string,
  isCustomInput: string,
  customValuesContainer: string,
  customValueByLangId: (langId: number) => string,
  deleteFeatureValue: string,
  addFeatureValue: string,
  featureValueLoader: string,
}

export interface FeatureValuesManagerOptions {
  // Selector map to use, defaults to the product one
  map?: FeatureValuesMap,
  // Root container the widget is scoped to. When null (default) selectors are resolved globally
  // (product page); when provided the widget is scoped to it (combination form in a modal).
  container?: JQuery | null,
  // Id of the deletion confirmation modal, must be unique per widget instance on the page
  deleteModalId?: string,
  // Optional hook called whenever a value is added or removed, used by the combination form to flag
  // itself as updated (its hidden inputs/DOM rows do not trigger the modal's input listeners).
  onChange?: (() => void) | null,
}

export default class FeatureValuesManager {
  router: Router;

  eventEmitter: typeof EventEmitter;

  map: FeatureValuesMap;

  $container: JQuery | null;

  deleteModalId: string;

  onChange: (() => void) | null;

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
   * @param options {FeatureValuesManagerOptions}
   */
  constructor(eventEmitter: typeof EventEmitter, options: FeatureValuesManagerOptions = {}) {
    this.router = new Router();
    this.eventEmitter = eventEmitter;
    this.map = options.map ?? ProductMap.featureValues;
    this.$container = options.container ?? null;
    this.deleteModalId = options.deleteModalId ?? 'modal-confirm-delete-feature-value';
    this.onChange = options.onChange ?? null;

    this.$controlsContainer = this.find(this.map.controlsContainer);
    this.$featureSelector = $(this.map.featureSelect, this.$controlsContainer);
    this.$featureValueSelector = $(this.map.featureValueSelect, this.$controlsContainer);
    this.$newCustomValuesContainers = $(this.map.newCustomValuesContainers, this.$controlsContainer);
    this.$newCustomValueInputs = $(this.map.newCustomValueInputs, this.$newCustomValuesContainers);
    this.$addFeatureValueButton = $(this.map.addFeatureValue, this.$controlsContainer);
    this.$featureValueLoader = $(this.map.featureValueLoader, this.$controlsContainer);
    this.$collectionContainer = this.find(this.map.collectionContainer);
    this.$collectionRowsContainer = this.find(this.map.collectionRowsContainer);

    // Nothing to manage if the features widget is not present (e.g. feature disabled)
    if (!this.$controlsContainer.length) {
      return;
    }

    this.$featureSelector.select2();
    this.$featureValueSelector.select2();

    this.watchFeatureSelectors();
    this.watchDeleteButtons();
    this.watchAddButton();

    // Init select2
    $('select[data-toggle="select2"]', this.$collectionRowsContainer).select2();
  }

  /**
   * Resolve a selector, scoped to the container when one was provided.
   */
  private find(selector: string): JQuery {
    return this.$container ? $(selector, this.$container) : $(selector);
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
      const $featureRow = $(this.map.featureRowByFeatureId(featureId), this.$collectionRowsContainer);

      // Feature collection not present we must add it
      if (!$featureRow.length) {
        const featurePrototype = this.$collectionContainer.data('prototype');
        const featurePrototypeName = this.$collectionContainer.data('prototypeName');
        // The container keeps track of the next index to use, we increment it right away
        const rowIndex = this.$collectionContainer.data('rowIndex');
        this.$collectionContainer.data('rowIndex', rowIndex + 1);

        const $newFeatureRow = $(featurePrototype.replace(new RegExp(featurePrototypeName, 'g'), rowIndex)).first();
        $newFeatureRow.attr('feature-id', featureId);
        this.$collectionRowsContainer.append($newFeatureRow);
        $(this.map.featureIdInput, $newFeatureRow).val(featureId);
        $(this.map.featureNameInput, $newFeatureRow).val(featureName);
        this.addFeatureValueRow($newFeatureRow, featureId, featureName, featureValueId);
      } else {
        this.addFeatureValueRow($featureRow, featureId, featureName, featureValueId);
      }

      // Display list that can't be empty anymore
      this.$collectionContainer.removeClass('d-none');
      this.resetControls();
      this.notifyChange();
    });
  }

  /**
   * Notify the host form that the feature values changed.
   */
  private notifyChange(): void {
    if (this.onChange) {
      this.onChange();
    }
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
    // The feature row keeps track of the next index to use for its values, we increment it right away
    const rowIndex = $featureRow.data('rowIndex');
    $featureRow.data('rowIndex', rowIndex + 1);
    const $featureValueRows = $(this.map.featureValueRowByFeatureId(featureId), this.$collectionRowsContainer);
    const $newFeatureValueRow = $(rowValuePrototype.replace(new RegExp(rowValuePrototypeName, 'g'), rowIndex));
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
      $(this.map.featureValueIdInput, $newFeatureValueRow).val(featureValueId);
      $(this.map.featureValueNameInput, $newFeatureValueRow).val(featureValueName);
      $(this.map.featureValueNamePreview, $newFeatureValueRow).text(featureValueName);
      $(this.map.isCustomInput, $newFeatureValueRow).val(0);
      $(this.map.customValuesContainer, $newFeatureValueRow).hide();
    } else {
      $(this.map.featureValueIdInput, $newFeatureValueRow).val('');
      $(this.map.featureValueNameInput, $newFeatureValueRow).val('');
      $(this.map.featureValueNamePreview, $newFeatureValueRow).text('');
      $(this.map.isCustomInput, $newFeatureValueRow).val(1);
      $(this.map.customValuesContainer, $newFeatureValueRow).show();

      const newCustomValues = this.getNewCustomValues();
      newCustomValues.forEach((customValue: string, langId: number) => {
        const customValueInputSelector = this.map.customValueByLangId(langId);
        const $customValueInput = $(customValueInputSelector, $newFeatureValueRow);
        $customValueInput.val(customValue);
      });
    }
    $(this.map.featureNameCell, $newFeatureValueRow).text(featureName);
  }

  private resetControls(): void {
    this.$featureSelector.val(0).trigger('change');
    this.$featureValueSelector.empty();
    this.$featureValueSelector.val('').trigger('change');
    this.$featureValueSelector.prop('disabled', true);
    this.$newCustomValueInputs.val('');
  }

  private watchDeleteButtons(): void {
    $(this.$collectionRowsContainer).on('click', this.map.deleteFeatureValue, (event) => {
      const $deleteButton = $(event.currentTarget);
      const $collectionRow = $deleteButton.closest(this.map.featureValueRow);
      const modal = new (ConfirmModal as any)(
        {
          id: this.deleteModalId,
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
          const $valueRows = $(this.map.featureValueRowByFeatureId(featureId), this.$collectionRowsContainer);

          if ($valueRows.length === 0) {
            const $featureRow = $(this.map.featureRowByFeatureId(featureId), this.$collectionRowsContainer);
            $featureRow.remove();
          }
          this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
          this.$collectionContainer.toggleClass('d-none', this.$collectionRowsContainer.children().length === 0);
          this.notifyChange();
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

    const selectedFeatureValues = this.getFeatureValueIds();
    // Always add placeholder and custom value options, even when no predefined values exist.
    this.addFeatureValue(this.$featureValueSelector.data('placeholderLabel'), 0);
    this.addFeatureValue(this.$featureValueSelector.data('customValueLabel'), -1);

    // Then add the available predefined feature values.
    $.each(featureValuesData, (index, featureValue) => {
      if (featureValue.id !== 0 && !selectedFeatureValues.includes(featureValue.id)) {
        this.addFeatureValue(featureValue.value, featureValue.id);
      }
    });

    this.$featureValueSelector.prop('disabled', false);
    this.$featureValueSelector.val(0).trigger('change');
    this.$featureValueSelector.select2();
  }

  private getFeatureValueIds(): number[] {
    const featureValueIds: number[] = [];
    $(this.map.featureValueIdInput, this.$collectionRowsContainer).each((index, featureValueInput) => {
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
