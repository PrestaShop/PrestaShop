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

const {$} = window;

/**
 * This is able to watch an HTML form and parse it as a Javascript object based on a configurable
 * mapping. Each field from the model is mapped to a form input, or several, each input is watched
 * to keep the model consistent.
 *
 * The model mapping used for this component is an object which uses the modelKey as a key (it represents
 * the property path in the object, separated by a dot) and the input names as value (they follow Symfony
 * convention naming using brackets). Here is an example of mapping:
 *
 * const modelMapping = {
 *  'product.stock.quantity': 'product[stock][quantity]',
 *  'product.price.priceTaxExcluded': [
 *    'product[price][price_tax_excluded]',
 *    'product[shortcuts][price][price_tax_excluded]',
 *  ],
 * };
 *
 * As you can see for priceTaxExcluded it is possible to assign multiple inputs to the same modelKey, thus
 * any update in one of the inputs will update the model, and all these inputs are kept in sync.
 *
 * With the previous configuration this component would return an object that looks like this:
 *
 * {
 *   product: {
 *     stock: {
 *       // Mapped to product[stock][quantity] input
 *       quantity: 200,
 *     },
 *     price: {
 *       // Mapped to two inputs product[price][price_tax_excluded] and product[shortcuts][price][price_tax_excluded]
 *       priceTaxExcluded: 20.45,
 *     }
 *   }
 * }
 */
export default class FormObjectMapper {
  /**
   * @param {jQuery} $form - Form element to attach the mapper to
   * @param {Object} modelMapping - Structure mapping a model to form names
   * @param {EventEmitter} eventEmitter
   * @param {Object} [config] - Event names
   * @param {Object} [config.updateModel] - Name of the event to listen to trigger a refresh of the model update
   * @param {Object} [config.modelUpdated] - Name of the event emitted each time the model is updated
   * @param {Object} [config.modelFieldUpdated] - Name of the event emitted each time a field is updated
   * @return {Object}
   */
  constructor($form, modelMapping, eventEmitter, config) {
    this.$form = $form;
    this.fullModelMapping = modelMapping;
    this.eventEmitter = eventEmitter;

    const inputConfig = config || {};

    // This event is registered so when it is triggered it forces the form mapping and object update,
    // it can be useful when some new inputs have been added in the DOM (or removed) so that the model
    // acknowledges the update
    this.updateModelEventName = inputConfig.updateModel || 'updateModel';

    // This event is emitted each time the object is updated (from both input change and external event)
    this.modelUpdatedEventName = inputConfig.modelUpdated || 'modelUpdated';
    // This event is emitted each time an object field is updated (from both input change and external event)
    this.modelFieldUpdatedEventName = inputConfig.modelFieldUpdated || 'modelFieldUpdated';

    // Contains callbacks identified by model keys
    this.watchedProperties = {};

    this.initFormMapping();
    this.updateFullObject();
    this.watchUpdates();

    return {
      /**
       * Returns the model mapped to the form (current live state)
       *
       * @returns {*|{}}
       */
      getModel: () => this.model,

      /**
       * Returns all inputs associated to a model field.
       *
       * @param {string} modelKey
       *
       * @returns {undefined|jQuery}
       */
      getInputsFor: (modelKey) => {
        if (!Object.prototype.hasOwnProperty.call(this.fullModelMapping, modelKey)) {
          return undefined;
        }

        const inputNames = this.fullModelMapping[modelKey];

        // We must loop manually to keep the order in configuration, if we use jQuery multiple selectors the collection
        // will be filled respecting the order in the DOM
        const inputs = [];
        const domForm = this.$form.get(0);
        inputNames.forEach((inputName) => {
          const inputsByName = domForm.querySelectorAll(`[name="${inputName}"]`);

          if (inputsByName.length) {
            inputsByName.forEach((input) => {
              inputs.push(input);
            });
          }
        });

        return inputs.length ? $(inputs) : undefined;
      },

      /**
       * Set a value to a field of the object based on the model key, the object itself is updated
       * of course but the mapped inputs are also synced (all of them if multiple). Events are also
       * triggered to indicate the object has been updated (the general and the individual field ones).
       *
       * @param {string} modelKey
       * @param {*|{}} value
       */
      set: (modelKey, value) => {
        if (!Object.prototype.hasOwnProperty.call(this.modelMapping, modelKey) || value === this.getValue(modelKey)) {
          return;
        }

        // First update the inputs then the model, so that the event is sent at last
        this.updateInputValue(modelKey, value);
        this.updateObjectByKey(modelKey, value);
        this.eventEmitter.emit(this.modelUpdatedEventName, this.model);
      },

      /**
       * Alternative to the event listening, you can watch a specific field of the model and assign a callback.
       * When the specified model field is updated the event is still thrown but additionally any callback assigned
       * to this specific value is also called, the parameter is the same event.
       *
       * @param {string} modelKey
       * @param {function} callback
       */
      watch: (modelKey, callback) => {
        if (!Object.prototype.hasOwnProperty.call(this.watchedProperties, modelKey)) {
          this.watchedProperties[modelKey] = [];
        }
        this.watchedProperties[modelKey].push(callback);
      },
    };
  }

  /**
   * Get a field from the object based on the model key, you can even get a sub part of the whole model,
   * this internal method is used by both get and set public methods.
   *
   * @param {string} modelKey
   *
   * @returns {*|{}|undefined} Returns any element from the model, undefined if not found
   * @private
   */
  getValue(modelKey) {
    const modelKeys = modelKey.split('.');

    return $.serializeJSON.deepGet(this.model, modelKeys);
  }

  /**
   * Watches if changes happens from the form or via an event.
   *
   * @private
   */
  watchUpdates() {
    this.$form.on('keyup change dp.change', ':input', _.debounce(
      (event) => this.inputUpdated(event),
      350,
      {maxWait: 1500},
    ));
    this.eventEmitter.on(this.updateModelEventName, () => this.updateFullObject());
  }

  /**
   * Triggered when a form input has been changed.
   *
   * @param {jQuery.Event} event
   *
   * @private
   */
  inputUpdated(event) {
    const target = event.currentTarget;

    // All inputs changes are watched, but not all of them are part of the mapping so we ignore them
    if (!Object.prototype.hasOwnProperty.call(this.formMapping, target.name)) {
      return;
    }

    const updatedValue = $(target).val();
    const updatedModelKey = this.formMapping[target.name];

    // Update the mapped input fields
    this.updateInputValue(updatedModelKey, updatedValue, target.name);

    // Then update model and emit event
    this.updateObjectByKey(updatedModelKey, updatedValue);
    this.eventEmitter.emit(this.modelUpdatedEventName, this.model);
  }

  /**
   * Update all the inputs mapped to a model key
   *
   * @param {string} modelKey
   * @param {*|{}} value
   * @param {string|undefined} sourceInputName Source of the change (no need to update it)
   *
   * @private
   */
  updateInputValue(modelKey, value, sourceInputName = undefined) {
    const modelInputs = this.fullModelMapping[modelKey];

    // Update linked inputs (when there is more than one input associated to the model field)
    if (Array.isArray(modelInputs)) {
      modelInputs.forEach((inputName) => {
        if (sourceInputName === inputName) {
          return;
        }

        this.updateInputByName(inputName, value);
      });
    } else if (sourceInputName !== modelInputs) {
      this.updateInputByName(modelInputs, value);
    }
  }

  /**
   * Update individual input based on its name
   *
   * @param {string} inputName
   * @param {*|{}} value
   *
   * @private
   */
  updateInputByName(inputName, value) {
    const $input = $(`[name="${inputName}"]`, this.$form);

    if (!$input.length) {
      console.error(`Input with name ${inputName} is not present in form.`);

      return;
    }

    // This check is important to avoid infinite loops, we don't use strict equality on purpose because it would result
    // into a potential infinite loop if type don't match, which can easily happen with a number value and a text input.
    // eslint-disable-next-line eqeqeq
    if ($input.val() != value) {
      $input.val(value);

      if ($input.data('toggle') === 'select2') {
        // This is required for select2, because only changing the val doesn't update the wrapping component
        $input.trigger('change');
      }
    }
  }

  /**
   * Serializes and updates the object based on form content and the mapping configuration, finally
   * emit an event for external components that may need the update.
   *
   * This method is called when this component initializes or when triggered by an external event.
   *
   * @private
   */
  updateFullObject() {
    const serializedForm = this.$form.serializeJSON();
    this.model = {};
    Object.keys(this.modelMapping).forEach((modelKey) => {
      const formMapping = this.modelMapping[modelKey];
      const formKeys = $.serializeJSON.splitInputNameIntoKeysArray(formMapping);
      const formValue = $.serializeJSON.deepGet(serializedForm, formKeys);

      this.updateObjectByKey(modelKey, formValue);
    });

    this.eventEmitter.emit(this.modelUpdatedEventName, this.model);
  }

  /**
   * Update a specific field of the object.
   *
   * @param {string} modelKey
   * @param {*|{}} value
   *
   * @private
   */
  updateObjectByKey(modelKey, value) {
    const modelKeys = modelKey.split('.');
    const previousValue = $.serializeJSON.deepGet(this.model, modelKeys);

    // This check has two interests, there is no point in modifying a value or emit an event for a value that did not
    // change, and it avoids infinite loops when the object field are co-dependent and need to be updated dynamically
    // (ex: update price tax included when price tax excluded is updated and vice versa, without this check an infinite
    // loop would happen)
    if (previousValue === value) {
      return;
    }

    $.serializeJSON.deepSet(this.model, modelKeys, value);

    const updateEvent = {
      object: this.model,
      modelKey,
      value,
      previousValue,
    };
    this.eventEmitter.emit(this.modelFieldUpdatedEventName, updateEvent);

    if (Object.prototype.hasOwnProperty.call(this.watchedProperties, modelKey)) {
      const propertyWatchers = this.watchedProperties[modelKey];
      propertyWatchers.forEach((callback) => {
        callback(updateEvent);
      });
    }
  }

  /**
   * Reverse the initial mapping Model->Form to the opposite Form->Model
   * This simplifies the sync in when data updates.
   *
   * @private
   */
  initFormMapping() {
    // modelMapping is a light version of the fullModelMapping, it only contains one input name which is considered
    // as the default one (when full object is updated, only the default input is used)
    this.modelMapping = {};

    // formMapping is the inverse of modelMapping for each input name it associated the model key, it is generated for
    // performance and convenience, this allows to get mapping data faster in other functions
    this.formMapping = {};

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
   * @param {string} formName
   * @param {string} modelMapping
   *
   * @private
   */
  addFormMapping(formName, modelMapping) {
    if (Object.prototype.hasOwnProperty.call(this.formMapping, formName)) {
      console.error(`The form element ${formName} is already mapped to ${this.formMapping[formName]}`);

      return;
    }

    this.formMapping[formName] = modelMapping;
    this.modelMapping[modelMapping] = formName;
  }
}
