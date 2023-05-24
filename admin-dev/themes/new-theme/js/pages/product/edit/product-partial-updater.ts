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

import _ from 'lodash';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import ProductMap from '@pages/product/product-map';

const {$} = window;

/**
 * When product is edited we want to send only partial updates
 * so this class compares the initial data from the form computes
 * the diff when form is submitted And dynamically build another
 * form to submit only updated data (along with required fields
 * token and such).
 *
 * It also disabled the submit button as long as no data has been
 * modified by the user.
 */
export default class ProductPartialUpdater {
  private eventEmitter: EventEmitter;

  private $productForm: JQuery;

  private $productFormSubmitButton: JQuery;

  private $productTypePreview: JQuery;

  private initialData: Record<string, any>;

  private listEditionMode: boolean = false;

  /**
   * @param eventEmitter {EventEmitter}
   * @param $productForm {JQuery}
   */
  constructor(
    eventEmitter: EventEmitter,
    $productForm: JQuery,
  ) {
    this.eventEmitter = eventEmitter;
    this.$productForm = $productForm;
    this.$productFormSubmitButton = $(ProductMap.productFormSubmitButton);
    this.$productTypePreview = $(ProductMap.productType.headerPreviewButton);
    this.initialData = {};

    this.watch();
  }

  /**
   * This the public method you need to use to start this component
   * ex: new ProductPartialUpdater($productForm, $productFormSubmitButton).watch();
   */
  private watch(): void {
    // Avoid submitting form when pressing Enter
    this.$productForm.keypress((e) => e.which !== 13);
    this.$productFormSubmitButton.prop('disabled', true);
    this.initialData = this.getFormDataAsObject();
    this.$productForm.submit(() => this.updatePartialForm());
    // 'dp.change' event allows tracking datepicker input changes
    this.$productForm.on('keyup change dp.change',
      // listen for all inputs except combination filters
      `:input[name!="${ProductMap.combinations.list.attributeFilterInputName}"]`,
      () => this.updateFooterButtonStates(),
    );
    this.eventEmitter.on(ProductEventMap.updateSubmitButtonState, () => this.updateFooterButtonStates());
    this.eventEmitter.on(ProductEventMap.combinations.listEditionMode, (editionMode) => {
      this.listEditionMode = editionMode;
      this.updateFooterButtonStates();
    });

    this.watchCustomizations();
    this.watchCategories();
    this.initFormattedTextarea();
  }

  /**
   * Watch events specifically related to customizations subform
   *
   * @private
   */
  private watchCustomizations(): void {
    this.eventEmitter.on(ProductEventMap.customizations.rowAdded, () => this.updateFooterButtonStates());
    this.eventEmitter.on(ProductEventMap.customizations.rowRemoved, () => this.updateFooterButtonStates());
  }

  /**
   * Watch events specifically related to categories subform
   *
   * @private
   */
  private watchCategories(): void {
    this.eventEmitter.on(ProductEventMap.categories.categoriesUpdated, () => this.updateFooterButtonStates());
  }

  /**
   * Rich editors apply a layer over initial textarea fields therefore they need to be watched differently.
   *
   * @private
   */
  private initFormattedTextarea(): void {
    this.eventEmitter.on('tinymceEditorSetup', (event) => {
      event.editor.on('change', () => this.updateFooterButtonStates());
    });
  }

  /**
   * This methods handles the form submit
   *
   * @returns {boolean}
   *
   * @private
   */
  private updatePartialForm(): boolean {
    const updatedData = this.getUpdatedFormData();

    if (updatedData !== null) {
      let formMethod = this.$productForm.prop('method');

      if (Object.prototype.hasOwnProperty.call(updatedData, '_method')) {
        // eslint-disable-next-line dot-notation
        formMethod = updatedData['_method'];
      }

      if (formMethod !== 'PATCH') {
        // Returning true will continue submitting form as usual
        return true;
      }
      // On patch method we extract changed values and submit only them
      this.submitUpdatedData(updatedData);
    } else {
      // @todo: This is temporary we should probably use a nice modal instead, that said since the submit button is
      //        disabled when no data has been modified it should never happen
      alert('no fields updated');
    }

    return false;
  }

  /**
   * Dynamically build a form with provided updated data and submit this "shadow" form
   *
   * @param updatedData {Object} Contains an object with all form fields to update indexed by query parameters name
   *
   * @private
   */
  private submitUpdatedData(updatedData: Record<string, any>): void {
    this.$productFormSubmitButton.prop('disabled', true);
    const $updatedForm = this.createShadowForm(updatedData);

    $updatedForm.appendTo('body');
    $updatedForm.submit();
  }

  /**
   * @param updatedData
   *
   * @private
   *
   * @returns {Object} Form clone (Jquery object)
   */
  private createShadowForm(updatedData: Record<string, any>): JQuery {
    const $updatedForm = this.$productForm.clone();
    $updatedForm.empty();
    $updatedForm.prop('class', '');
    Object.keys(updatedData).forEach((fieldName) => {
      if (Array.isArray(updatedData[fieldName])) {
        updatedData[fieldName].forEach((value: any) => {
          this.appendInputToForm($updatedForm, fieldName, value);
        });
      } else {
        this.appendInputToForm($updatedForm, fieldName, updatedData[fieldName]);
      }
    });

    return $updatedForm;
  }

  /**
   * Adapt the submit button state, as long as no data has been updated the button is disabled
   *
   * @private
   */
  private updateFooterButtonStates(): void {
    const updatedData = this.getUpdatedFormData();

    if (this.listEditionMode) {
      this.toggleButtonsState([
        ProductMap.productFormSubmitButton,
        ProductMap.footer.cancelButton,
        ProductMap.footer.goToCatalogButton,
        ProductMap.footer.previewUrlButton,
        ProductMap.footer.duplicateProductButton,
        ProductMap.footer.newProductButton,
      ], false);
      // Disable type button permanently
      this.$productTypePreview.off('click');
    } else if (updatedData === null) {
      // Initial mode no modification
      this.toggleButtonsState([
        ProductMap.productFormSubmitButton,
        ProductMap.footer.cancelButton,
      ], false);
      this.toggleButtonsState([
        ProductMap.footer.goToCatalogButton,
        ProductMap.footer.previewUrlButton,
        ProductMap.footer.duplicateProductButton,
        ProductMap.footer.newProductButton,
      ], true);
    } else {
      this.toggleButtonsState([
        ProductMap.productFormSubmitButton,
        ProductMap.footer.cancelButton,
      ], true);
      this.toggleButtonsState([
        ProductMap.footer.goToCatalogButton,
        ProductMap.footer.previewUrlButton,
        ProductMap.footer.duplicateProductButton,
        ProductMap.footer.newProductButton,
      ], false);
      // Disable type button permanently
      this.$productTypePreview.off('click');
    }
  }

  private toggleButtonsState(buttons: string[], enabled: boolean): void {
    buttons.forEach((buttonSelector: string) => {
      const $button = $(buttonSelector);
      $button.prop('disabled', !enabled);
      $button.toggleClass('disabled', !enabled);
    });
  }

  /**
   * Returns the updated data, only fields which are different from the initial page load
   * are returned (token and method are added since they are required for a valid request).
   *
   * If no fields have been modified this method returns null.
   *
   * @private
   *
   * @returns {{}|null}
   */
  private getUpdatedFormData(): Record<string, any> | null {
    const currentData: Record<string, any> = this.getFormDataAsObject();
    // Loop through current form data and remove the one that did not change
    // This way only updated AND new values remain
    Object.keys(this.initialData).forEach((fieldName) => {
      const fieldValue = this.initialData[fieldName];

      // Field is absent in the new data (it was not in the initial) we force it to empty string (not null
      // or it will be ignored)
      if (!Object.prototype.hasOwnProperty.call(currentData, fieldName)) {
        currentData[fieldName] = '';
      } else if (_.isEqual(currentData[fieldName], fieldValue)) {
        delete currentData[fieldName];
      }
    });
    // No need to loop through the field contained in currentData and not in the initial
    // they are new values so are, by fact, updated values

    if (Object.keys(currentData).length === 0) {
      return null;
    }

    // Some parameters are always needed
    const permanentParameters = [
      // We need the form CSRF token
      'product[_token]',
      // If method is not POST or GET a hidden type input is used to simulate it (like PATCH)
      '_method',
    ];
    permanentParameters.forEach((permanentParameter) => {
      if (Object.prototype.hasOwnProperty.call(this.initialData, permanentParameter)) {
        currentData[permanentParameter] = this.initialData[permanentParameter];
      }
    });

    return currentData;
  }

  /**
   * Returns the serialized form data as an Object indexed by field name
   *
   * @private
   *
   * @returns {{}}
   */
  private getFormDataAsObject(): Record<string, any> {
    const formArray = this.$productForm.serializeArray();
    const serializedForm: Record<string, any> = {};

    formArray.forEach((formField) => {
      let {value} = formField;

      // Input names can be identical when expressing array of values for same field (like multiselect checkboxes)
      // so we need to put these input values into single array indexed by that field name
      if (formField.name.endsWith('[]')) {
        let multiField = [];

        if (Object.prototype.hasOwnProperty.call(serializedForm, formField.name)) {
          multiField = serializedForm[formField.name];
        }

        multiField.push(formField.value);
        value = multiField;
      }

      serializedForm[formField.name] = value;
    });

    // File inputs must be handled manually
    $('input[type="file"]', this.$productForm).each((inputIndex: number, fileInput) => {
      const inputFile = <HTMLInputElement>fileInput;

      const {files} = <HTMLInputElement>$(fileInput)[0];
      $.each(files, (fileIndex, file) => {
        serializedForm[inputFile.name] = file;
      });
    });

    return serializedForm;
  }

  /**
   * @param $form
   * @param name
   * @param value
   *
   * @private
   */
  private appendInputToForm($form: JQuery, name: string, value: string): void {
    $('<input>').attr({
      name,
      type: 'hidden',
      value,
    }).appendTo($form);
  }
}
