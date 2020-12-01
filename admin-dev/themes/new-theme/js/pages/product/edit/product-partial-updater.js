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

const {$} = window;

export default class ProductPartialUpdater {
  constructor($productForm, $productFormSubmitButton) {
    this.$productForm = $productForm;
    this.$productFormSubmitButton = $productFormSubmitButton;
  }

  watch() {
    this.$productFormSubmitButton.prop('disabled', true);
    this.initialData = this.getFormDataAsObject();
    this.$productForm.submit((e) => this.updatePartialForm(e));
    this.$productForm.on('change', ':input', () => this.updateSubmitState());
  }

  updatePartialForm(event) {
    event.stopImmediatePropagation();

    const updatedData = this.getUpdatedFormData();
    if (updatedData !== null) {
      this.postUpdatedData(updatedData);
    } else {
      alert('no fields updated');
    }

    return false;
  }

  postUpdatedData(updatedData) {
    this.$productFormSubmitButton.prop('disabled', true);
    const $updatedForm = this.$productForm.clone();
    $updatedForm.empty();
    $updatedForm.prop('class', '');
    Object.keys(updatedData).forEach((fieldName) => {
      $('<input>').attr({
        name: fieldName,
        type: 'hidden',
        value: updatedData[fieldName],
      }).appendTo($updatedForm);
    });

    $updatedForm.appendTo('body');
    $updatedForm.submit();
  }

  updateSubmitState() {
    const updatedData = this.getUpdatedFormData();
    this.$productFormSubmitButton.prop('disabled', updatedData === null);
  }

  getUpdatedFormData() {
    const currentData = this.getFormDataAsObject();
    // Loop through current form data and remove the one that did not change
    // This way only updated AND new values remain
    Object.keys(this.initialData).forEach((fieldName) => {
      const fieldValue = this.initialData[fieldName];
      if (currentData[fieldName] === fieldValue) {
        delete currentData[fieldName];
      }
    });

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

  getFormDataAsObject() {
    const formArray = this.$productForm.serializeArray();
    const serializedForm = {};
    formArray.forEach((formField) => {
      serializedForm[formField.name] = formField.value;
    });

    return serializedForm;
  }
}
