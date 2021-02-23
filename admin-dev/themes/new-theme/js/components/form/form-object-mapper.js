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

/**
 * This is able to watch an HTML form and parse it as a Javascript object based on a configurable
 * mapping. Each field from the model is mapped to a form input, or several, each input is watched
 * to keep the model consistent.
 *
 * When a model field has several inputs associated it keeps the in sync when either of them is updated.
 */
export default class FormObjectMapper {
  /**
   * @param $form {jQuery}
   * @param modelMapping {Object}
   * @param eventEmitter {EventEmitter}
   * @param config {Object}
   */
  constructor($form, modelMapping, eventEmitter, config) {
    this.$form = $form;
    this.fullModelMapping = modelMapping;
    this.eventEmitter = eventEmitter;

    const inputConfig = config || {};

    // This event is registered so when it is triggered it forces the object update
    this.updateModelEventName = inputConfig.updateModel || 'updateModel';
    // This event is emitted each time the object is updated (from both input change and external event)
    this.modelUpdatedEventName = inputConfig.modelUpdated || 'modelUpdated';

    this.modelMapping = {};
    this.formMapping = {};
    this.object = {};

    this.initFormMapping();
    this.watchUpdates();
    this.updateFullObject();
  }

  getObject() {
    return this.object;
  }

  /**
   * Watches if changes happens from the form or via an event.
   */
  watchUpdates() {
    this.$form.on('keyup change dp.change', ':input', (event) => this.inputUpdated(event));
    this.eventEmitter.on(this.updateModelEventName, () => this.updateFullObject());
  }

  /**
   * Triggered when a form input has been changed.
   *
   * @param event {Object}
   */
  inputUpdated(event) {
    const target = event.currentTarget;

    if (!Object.prototype.hasOwnProperty.call(this.formMapping, target.name)) {
      return;
    }

    const updatedValue = $(target).val();
    const updatedModelKey = this.formMapping[target.name];
    const modelInputs = this.fullModelMapping[updatedModelKey];

    // Update linked inputs
    modelInputs.forEach((inputName) => {
      if (inputName === target.name) {
        return;
      }

      $(`[name="${inputName}"]`).val(updatedValue);
    });

    // Then update model and emit event
    this.updateObjectByKey(updatedModelKey, updatedValue);
    this.eventEmitter.emit(this.modelUpdatedEventName, this.object);
  }

  /**
   * Serializes and updates the object based on form content and the mapping configuration, finally
   * emit an event for external components that may need the update.
   *
   * This method is called when this component initializes or when triggered by an external event.
   */
  updateFullObject() {
    const serializedForm = this.$form.serializeJSON();
    this.object = {};
    Object.keys(this.modelMapping).forEach((modelKey) => {
      const formMapping = this.modelMapping[modelKey];
      const formKeys = $.serializeJSON.splitInputNameIntoKeysArray(formMapping);
      const formValue = $.serializeJSON.deepGet(serializedForm, formKeys);

      this.updateObjectByKey(modelKey, formValue);
    });

    this.eventEmitter.emit(this.modelUpdatedEventName, this.object);
  }

  /**
   * Update a specific field of the object.
   *
   * @param modelKey {string}
   * @param value
   */
  updateObjectByKey(modelKey, value) {
    const modelKeys = modelKey.split('.');
    $.serializeJSON.deepSet(this.object, modelKeys, value);
  }

  /**
   * Reverse the initial mapping Model->Form to the opposite Form->Model
   * This simplifies the sync in when data updates.
   */
  initFormMapping() {
    this.formMapping = {};
    this.modelMapping = {};

    Object.keys(this.fullModelMapping).forEach((modelKey) => {
      const formMapping = this.fullModelMapping[modelKey];

      if (Array.isArray(formMapping)) {
        formMapping.forEach((aliasFormMapping) => {
          this.addFormMapping(aliasFormMapping, modelKey);
        });
      } else {
        this.addFormMapping(formMapping, modelKey);
      }
    });
  }

  /**
   * @param formName {string}
   * @param modelMapping {string}
   */
  addFormMapping(formName, modelMapping) {
    if (Object.prototype.hasOwnProperty.call(this.formMapping, formName)) {
      console.error(`The form element ${formName} is already mapped to ${this.formMapping[formName]}`);

      return;
    }

    this.formMapping[formName] = modelMapping;
    // modelMapping is a light version of the fullModelMapping, it only contains one input name which is considered
    // as the default one (first in the list of full mapping)
    this.modelMapping[modelMapping] = formName;
  }
}
