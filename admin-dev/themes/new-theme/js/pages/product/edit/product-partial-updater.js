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
  /**
   * @param eventEmitter {EventEmitter}
   * @param $productForm {jQuery}
   * @param $productFormSubmitButton {jQuery}
   */
  constructor(eventEmitter, $productForm, $productFormSubmitButton) {
    this.eventEmitter = eventEmitter;
    this.$productForm = $productForm;
    this.$productFormSubmitButton = $productFormSubmitButton;
  }

  /**
   * This the public method you need to use to start this component
   * ex: new ProductPartialUpdater($productForm, $productFormSubmitButton).watch();
   */
  watch() {
    // Avoid submitting form when pressing Enter
    this.$productForm.keypress((e) => e.which !== 13);
    this.$productFormSubmitButton.prop('disabled', true);
    this.initialData = this.getFormDataAsObject();
    this.$productForm.submit(() => this.updatePartialForm());
    // 'dp.change' event allows tracking datepicker input changes
    this.$productForm.on('keyup change dp.change', ':input', () => this.updateSubmitButtonState());
    this.eventEmitter.on(ProductEventMap.updateSubmitButtonState, () => this.updateSubmitButtonState());

    this.watchCustomizations();
    this.initFormattedTextarea();
  }

  /**
   * Watch events specifically related to customizations subform
   */
  watchCustomizations() {
    this.eventEmitter.on(ProductEventMap.customizations.rowAdded, () => this.updateSubmitButtonState());
    this.eventEmitter.on(ProductEventMap.customizations.rowRemoved, () => this.updateSubmitButtonState());
  }

  /**
   * Rich editors apply a layer over initial textarea fields therefore they need to be watched differently.
   */
  initFormattedTextarea() {
    this.eventEmitter.on('tinymceEditorSetup', (event) => {
      event.editor.on('change', () => this.updateSubmitButtonState());
    });
  }

  /**
   * This methods handles the form submit
   *
   * @returns {boolean}
   *
   * @private
   */
  updatePartialForm() {
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
   */
  submitUpdatedData(updatedData) {
    this.$productFormSubmitButton.prop('disabled', true);
    const $updatedForm = this.createShadowForm(updatedData);

    $updatedForm.appendTo('body');
    $updatedForm.submit();
  }

  /**
   * @param updatedData
   *
   * @returns {Object} Form clone (Jquery object)
   */
  createShadowForm(updatedData) {
    const $updatedForm = this.$productForm.clone();
    $updatedForm.empty();
    $updatedForm.prop('class', '');
    Object.keys(updatedData).forEach((fieldName) => {
      if (Array.isArray(updatedData[fieldName])) {
        updatedData[fieldName].forEach((value) => {
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
   */
  updateSubmitButtonState() {
    const updatedData = this.getUpdatedFormData();
    this.$productFormSubmitButton.prop('disabled', updatedData === null);
  }

  /**
   * Returns the updated data, only fields which are different from the initial page load
   * are returned (token and method are added since they are required for a valid request).
   *
   * If no fields have been modified this method returns null.
   *
   * @returns {{}|null}
   */
  getUpdatedFormData() {
    const currentData = this.getFormDataAsObject();
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
   * @returns {{}}
   */
  getFormDataAsObject() {
    const formArray = this.$productForm.serializeArray();
    const serializedForm = {};

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
    $('input[type="file"]', this.$productForm).each((inputIndex, fileInput) => {
      $.each($(fileInput)[0].files, (fileIndex, file) => {
        serializedForm[fileInput.name] = file;
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
  appendInputToForm($form, name, value) {
    $('<input>').attr({
      name,
      type: 'hidden',
      value,
    }).appendTo($form);
  }
}
