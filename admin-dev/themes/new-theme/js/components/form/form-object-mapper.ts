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

/* eslint max-classes-per-file: ["error", 2] */

import BigNumber from 'bignumber.js';
import {transform as numberCommaTransform} from '@js/app/utils/number-comma-transformer';
import {isUndefined} from '@PSTypes/typeguard';

import NameValuePair = JQuery.NameValuePair;

const {$} = window;

export interface FormUpdateEvent {
  object: Record<string, any>,
  modelKey: string,
  value: any,
  previousValue: any,

  /**
   * You can stop the propagation of an event propagation to avoid the other watchers to be triggered,
   * this allows preventing infinite loop in case you change the value from an event (by stopping it
   * and re-setting new value).
   */
  stopPropagation(): void,

  /**
   * When true the watchers callback stop being called (for this specific event instance only).
   */
  isPropagationStopped(): boolean,
}

/**
 * Internal class for the event object that implements the FormUpdateEvent interface.
 */
class UpdateEvent implements FormUpdateEvent {
  object: Record<string, any>;

  modelKey: string;

  value: any;

  previousValue: any;

  private propagationStopped: boolean;

  constructor(
    object: Record<string, any>,
    modelKey: string,
    value: any,
    previousValue: any,
  ) {
    this.object = object;
    this.modelKey = modelKey;
    this.value = value;
    this.previousValue = previousValue;
    this.propagationStopped = false;
  }

  stopPropagation(): void {
    this.propagationStopped = true;
  }

  isPropagationStopped(): boolean {
    return this.propagationStopped;
  }
}

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
 * As you can see for priceTaxExcluded it is possible to assign
 * multiple inputs to the same modelKey, thus
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
 *       // Mapped to two inputs product[price][price_tax_excluded]
 *       // and product[shortcuts][price][price_tax_excluded]
 *       priceTaxExcluded: 20.45,
 *     }
 *   }
 * }
 */
export default class FormObjectMapper {
  private $form: JQuery<HTMLElement>;

  private fullModelMapping: Record<string, any>;

  private model: Record<string, any>;

  private modelMapping: Record<string, any>;

  private formMapping: Record<string, any>;

  private watchedProperties: Record<string, Array<(event: FormUpdateEvent) => void>>;

  /**
   * @param {JQuery} $form - Form element to attach the mapper to
   * @param {Object} modelMapping - Structure mapping a model to form names
   * @return {Object}
   */
  constructor(
    $form: JQuery<HTMLElement>,
    modelMapping: Record<string, any>,
  ) {
    if (!$form.length) {
      console.error('Invalid empty form as input');
    }

    this.$form = $form;
    this.fullModelMapping = modelMapping;
    this.model = {};
    this.modelMapping = {};

    // modelMapping is a light version of the fullModelMapping,
    // it only contains one input name which is considered
    // as the default one (when full object is updated, only the default input is used)
    this.modelMapping = {};

    // formMapping is the inverse of modelMapping for each input name
    // it associated the model key, it is generated for
    // performance and convenience, this allows to get mapping data faster in other functions
    this.formMapping = {};

    // Contains callbacks identified by model keys
    this.watchedProperties = {};

    this.initFormMapping();
    this.updateFullObject();
    this.watchUpdates();
  }

  /**
   * Returns the model mapped to the form (current live state)
   *
   * @returns {*|{}}
   */
  getModel(): Record<string, any> {
    return this.model;
  }

  /**
   * Returns all inputs associated to a model field.
   *
   * @param {string} modelKey
   *
   * @returns {undefined|JQuery}
   */
  getInputsFor(modelKey: string): JQuery<HTMLElement> | undefined {
    if (
      !Object.prototype.hasOwnProperty.call(this.fullModelMapping, modelKey)
    ) {
      return undefined;
    }

    let inputNames = this.fullModelMapping[modelKey];

    // Turn single identifier into array to limit duplicated code in the following code
    if (!Array.isArray(inputNames)) {
      inputNames = [inputNames];
    }

    // We must loop manually to keep the order in configuration,
    // if we use JQuery multiple selectors the collection
    // will be filled respecting the order in the DOM
    const inputs: Array<HTMLElement> = [];
    const domForm = this.$form.get(0);

    if (!domForm) return undefined;

    inputNames.forEach((inputName: string) => {
      const inputsByName = domForm.querySelectorAll(`[name="${inputName}"]`);

      if (inputsByName.length) {
        inputsByName.forEach((input) => {
          inputs.push(<HTMLElement>input);
        });
      }
    });

    return inputs.length ? $(inputs) : undefined;
  }

  /**
   * Set a value to a field of the object based on the model key, the object itself is updated
   * of course but the mapped inputs are also synced (all of them if multiple). Events are also
   * triggered to indicate the object has been updated (the general and the individual field ones).
   *
   * @param {string} modelKey
   * @param {*|{}} value
   */
  set(modelKey: string, value: string | number | string[] | undefined): void {
    if (
      !Object.prototype.hasOwnProperty.call(this.modelMapping, modelKey)
      || value === this.getValue(modelKey)
    ) {
      return;
    }

    // First update the inputs then the model, so that the event is sent at last
    this.updateInputValue(modelKey, value);
    this.updateObjectByKey(modelKey, value);
  }

  /**
   * Alternative to the event listening, you can watch a specific field of the model
   * and assign a callback.
   * When the specified model field is updated the event is still thrown but
   * additionally any callback assigned
   * to this specific value is also called, the parameter is the same event.
   *
   * @param {string | string[]} modelKeys
   * @param {function} callback
   */
  watch(modelKeys: string | string[], callback: (event: FormUpdateEvent) => void): void {
    const watchedKeys: string[] = Array.isArray(modelKeys) ? modelKeys : [modelKeys];

    watchedKeys.forEach((modelKey: string) => {
      if (
        !Object.prototype.hasOwnProperty.call(this.watchedProperties, modelKey)
      ) {
        this.watchedProperties[modelKey] = [];
      }
      this.watchedProperties[modelKey].push(callback);
    });
  }

  /**
   * Returns a model field by modelKey converted as a BigNumber instance, it also cleans
   * any invalid comma to avoid conversion error (since some languages sue comma as a decimal
   * separator).
   *
   * @param modelKey
   */
  getBigNumber(modelKey: string): BigNumber | undefined {
    const numberValue = this.getValue(modelKey);

    return isUndefined(numberValue) ? undefined : new BigNumber(numberCommaTransform(numberValue));
  }

  /**
   * Get a field from the object based on the model key,
   * you can even get a sub part of the whole model,
   * Get a field from the object based on the model key,
   * you can even get a sub part of the whole model,
   * this internal method is used by both get and set public methods.
   *
   * @param {string} modelKey
   *
   * @returns {*|{}|undefined} Returns any element from the model, undefined if not found
   */
  getValue(modelKey: string): string | number | string[] | undefined {
    const modelKeys = modelKey.split('.');

    return $.serializeJSON.deepGet(this.model, modelKeys);
  }

  /**
   * Serializes and updates the object based on form content and the mapping configuration, an event will be triggered
   * for each field of the object.
   */
  updateFullObject():void {
    // Temporarily enable all inputs or they will not be serialized
    const $disabledInputs: JQuery<HTMLElement> = this.$form.find(':input:disabled').removeAttr('disabled');
    const serializedFormArray = this.$form.serializeArray();
    // Restore initial disabled state
    $disabledInputs.prop('disabled', true);

    const serializedFormMap: Record<string, any> = {};
    serializedFormArray.forEach((value: NameValuePair) => {
      serializedFormMap[value.name] = value.value;
    });

    this.model = {};
    Object.keys(this.modelMapping).forEach((modelKey) => {
      const formMapping = this.modelMapping[modelKey];
      const formValue = serializedFormMap[formMapping];

      this.updateObjectByKey(modelKey, formValue);
    });
  }

  /**
   * Watches if changes happens from the form or via an event.
   */
  private watchUpdates(): void {
    // Only watch change event, not keyup event, this reduces the number of computing while typing and it prevents a
    // bug when using the NumberFormatter component which only applies on change event So both component must trigger
    // on change event only if we want them to apply their modifications appropriately The second advantage is that
    // debounce is not needed anymore which prevents any bug when form is submitted before un-focusing the input
    this.$form.on(
      'change dp.change',
      ':input',
      (event: JQuery.TriggeredEvent) => this.inputUpdated(event),
    );
  }

  /**
   * Triggered when a form input has been changed.
   *
   * @param {JQuery.TriggeredEvent} event
   */
  private inputUpdated(event: JQuery.TriggeredEvent): void {
    const target = <HTMLInputElement>event.currentTarget;

    // All inputs changes are watched, but not all of them are part of the mapping so we ignore them
    if (!Object.prototype.hasOwnProperty.call(this.formMapping, target.name)) {
      return;
    }

    const updatedValue = this.getInputValue($(target));
    const updatedModelKey = this.formMapping[target.name];

    // Update the mapped input fields
    this.updateInputValue(updatedModelKey, updatedValue, target.name);

    // Then update model and emit event
    this.updateObjectByKey(updatedModelKey, updatedValue);
  }

  /**
   * @param {jQuery} $input
   *
   * @returns {*}
   */
  private getInputValue($input: JQuery): string | number | string[] | boolean | undefined {
    if ($input.is(':checkbox')) {
      return $input.is(':checked');
    }

    return $input.val();
  }

  /**
   * Update all the inputs mapped to a model key
   *
   * @param {string} modelKey
   * @param {*|{}} value
   * @param {string|undefined} sourceInputName Source of the change (no need to update it)
   */
  private updateInputValue(
    modelKey: string,
    value: string | number | string[] | boolean | undefined,
    sourceInputName?: string,
  ): void {
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
   */
  private updateInputByName(
    inputName: string,
    value: string | number | string[] | boolean | undefined,
  ): void {
    const $input: JQuery = $(`[name="${inputName}"]`, this.$form);

    if (!$input.length) {
      console.error(`Input with name ${inputName} is not present in form.`);

      return;
    }

    if (!this.hasSameValue(this.getInputValue($input), value)) {
      if ($input.is(':checkbox')) {
        $input.val(value ? 1 : 0);
        $input.prop('checked', !!value);
      } else {
        $input.val(<string>value);
      }

      if ($input.data('toggle') === 'select2') {
        // This is required for select2, because only changing the val doesn't update
        // the wrapping component
        $input.trigger('change');
      }

      this.triggerChangeEvent(inputName);
    }
  }

  /**
   * Simulate change event programmatically, this is required because when changing the value of an input via js no
   * change event is triggered, so if you added a listener for this event it won't trigger and your app will not
   * behave as expected.
   *
   * @param inputName
   */
  private triggerChangeEvent(inputName: string): void {
    const input: HTMLInputElement = <HTMLInputElement>document.querySelector(`[name="${inputName}"]`);

    if (!input) {
      return;
    }

    const event = document.createEvent('HTMLEvents');
    event.initEvent('change', false, true);
    input.dispatchEvent(event);
  }

  /**
   * Check if both values are equal regardless of their type.
   *
   * @param inputValue
   * @param referenceValue
   * @private
   */
  private hasSameValue(
    inputValue: string | number | boolean | string[] | undefined,
    referenceValue: string | number | boolean | string[] | undefined,
  ): boolean {
    /*
     * We need a custom checking method for equality, we don't use strict equality on purpose because it would result
     * into a potential infinite loop if type doesn't match, which can easily happen when checking values with different
     * type but same values in essence.
     */
    if (typeof inputValue === 'boolean' || typeof referenceValue === 'boolean') {
      return <boolean>inputValue === <boolean>referenceValue;
    }

    /*
     * And we also try to see if both values have the same BigNumber value, this avoids forcing a number input value when
     * it's not written exactly the same way (like pending zeros). When checking a number we use the numberCommaTransform
     * as numbers can be written with comma separator depending on the language.
     */
    const referenceBigNumber: BigNumber = new BigNumber(numberCommaTransform(referenceValue));
    const inputBigNumber: BigNumber = new BigNumber(numberCommaTransform(inputValue));

    if (inputBigNumber.isEqualTo(referenceBigNumber)) {
      return true;
    }

    // eslint-disable-next-line eqeqeq
    return referenceValue == inputValue;
  }

  /**
   * Update a specific field of the object.
   *
   * @param {string} modelKey
   * @param {*|{}} value
   */
  private updateObjectByKey(
    modelKey: string,
    value: string | number | string[] | boolean | undefined,
  ): void {
    const modelKeys = modelKey.split('.');
    const previousValue = $.serializeJSON.deepGet(this.model, modelKeys);

    // This check has two interests, there is no point in modifying a value
    // or emit an event for a value that did not
    // change, and it avoids infinite loops when the object field are co-dependent and
    // need to be updated dynamically
    // (ex: update price tax included when price tax excluded is updated and
    // vice versa, without this check an infinite
    // loop would happen)
    if (previousValue === value) {
      return;
    }

    $.serializeJSON.deepSet(this.model, modelKeys, value);

    const updateEvent: FormUpdateEvent = new UpdateEvent(
      this.model,
      modelKey,
      value,
      previousValue,
    );

    if (
      Object.prototype.hasOwnProperty.call(this.watchedProperties, modelKey)
    ) {
      const propertyWatchers = this.watchedProperties[modelKey];
      propertyWatchers.forEach(
        (callback: (param: FormUpdateEvent) => void) => {
          if (!updateEvent.isPropagationStopped()) {
            callback(updateEvent);
          }
        },
      );
    }
  }

  /**
   * Reverse the initial mapping Model->Form to the opposite Form->Model
   * This simplifies the sync in when data updates.
   */
  private initFormMapping(): void {
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
   */
  private addFormMapping(formName: string, modelMapping: string): void {
    if (Object.prototype.hasOwnProperty.call(this.formMapping, formName)) {
      console.error(
        `The form element ${formName} is already mapped to ${this.formMapping[formName]}`,
      );

      return;
    }

    this.formMapping[formName] = modelMapping;
    this.modelMapping[modelMapping] = formName;
  }
}
