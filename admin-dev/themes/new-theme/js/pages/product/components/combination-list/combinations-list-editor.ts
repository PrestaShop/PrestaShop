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
import ProductEventMap from '@pages/product/product-event-map';
import CombinationsListRenderer from '@pages/product/components/combination-list/combinations-list-renderer';
import {EventEmitter} from 'events';
import {isUndefined} from '@PSTypes/typeguard';
import BigNumber from '@node_modules/bignumber.js';
import {notifyFormErrors} from '@components/form/helpers';
import CombinationsService from '@pages/product/services/combinations-service';

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;
const CombinationsMap = ProductMap.combinations;

/**
 * This component handles the edition mode  of the list, it watches any modification in the field to enable
 * the edition mode. It is also responsible for handling the update query of modified fields.
 *
 * If the query fails because of form errors the controller returns the form content in HTML which is used
 * to replace the list content, this leaves the validation process to the controller allows displaying inline
 * form errors.
 */
export default class CombinationsListEditor {
  private readonly productId: number;

  private readonly eventEmitter: EventEmitter;

  private readonly renderer: CombinationsListRenderer;

  private readonly $combinationsFormContainer: JQuery;

  private readonly $paginatedList: JQuery;

  private readonly editionDisabledElements: string[] = [
    CombinationsMap.bulkActionsDropdownBtn,
    CombinationsMap.tableRow.isSelectedCombination,
    CombinationsMap.commonBulkAllSelector,
    CombinationsMap.bulkCheckboxesDropdownButton,
    CombinationsMap.filtersSelectorButtons,
    CombinationsMap.generateCombinationsButton,
    CombinationsMap.list.rowActionButtons,
  ];

  private readonly combinationsService: CombinationsService;

  private editionMode: boolean = false;

  private savedInputValues: Record<string, any>;

  constructor(
    productId: number,
    eventEmitter: EventEmitter,
    combinationsRenderer: CombinationsListRenderer,
    combinationsService: CombinationsService,
  ) {
    this.productId = productId;
    this.eventEmitter = eventEmitter;
    this.renderer = combinationsRenderer;
    this.combinationsService = combinationsService;

    this.$combinationsFormContainer = $(CombinationsMap.combinationsFormContainer);
    this.$paginatedList = $(CombinationsMap.combinationsPaginatedList);
    this.savedInputValues = {};

    this.init();
  }

  get editionEnabled(): boolean {
    return this.editionMode;
  }

  private init(): void {
    // Preset initial data attribute after each list rendering
    this.eventEmitter.on(CombinationEvents.listRendered, () => {
      // Reset saved values we only keep the last one rendered
      this.savedInputValues = {};

      $(ProductMap.combinations.list.fieldInputs, this.$combinationsFormContainer).each((index, input) => {
        const $input = $(input);
        const inputValue = $input.val();

        if (!isUndefined(inputValue) && !isUndefined($input.prop('name'))) {
          this.savedInputValues[$input.prop('name')] = inputValue;
          this.watchInputChange($input, inputValue);
        }
      });
    });

    $(CombinationsMap.list.footer.cancel).on('click', () => {
      this.cancelEdition();
    });

    $(CombinationsMap.list.footer.reset).on('click', () => {
      this.resetEdition();
    });

    $(CombinationsMap.list.footer.save).on('click', () => {
      this.saveEdition();
    });
  }

  private watchInputChange($input: JQuery, initialValue: string | number | string[]): void {
    $input.data('initialValue', initialValue);
    $input.data('initialChecked', $input.is(':checked'));
    this.updateInput($input, initialValue, $input.is(':checked'));

    $input.on('change', () => {
      this.updateInput($input, $input.data('initialValue'), $input.data('initialChecked'));
    });
  }

  private updateInput($input: JQuery, initialValue: string | number | string[] | undefined, initialChecked: boolean): void {
    const inputChecked = $input.is(':checked');
    const inputValue = $input.val();
    let valueModified;

    if (!isUndefined(inputChecked) && !isUndefined(initialChecked) && inputChecked !== initialChecked) {
      valueModified = true;
    } else if (!isUndefined(initialValue) && !isUndefined(inputValue)) {
      const initialNumberValue = new BigNumber(Number(initialValue));
      const inputNumberValue = new BigNumber(Number($input.val()));

      if (!initialNumberValue.isNaN() && !inputNumberValue.isNaN()) {
        valueModified = !initialNumberValue.isEqualTo(inputNumberValue);
      } else {
        valueModified = initialValue !== $input.val();
      }
    }
    $input.toggleClass(ProductMap.combinations.list.modifiedFieldClass, valueModified);

    if (valueModified) {
      this.enableEditionMode();
    }
  }

  private enableEditionMode(): void {
    if (this.editionMode) {
      return;
    }

    this.editionMode = true;
    this.$paginatedList.addClass(CombinationsMap.list.editionModeClass);
    this.disableElements();
    this.eventEmitter.emit(CombinationEvents.listEditionMode, this.editionMode);
  }

  /**
   * Disabled elements (bulk actions, filters, ...) that could mess with the pagination and the edition mode
   */
  private disableElements(): void {
    this.editionDisabledElements.forEach((disabledSelector: string) => {
      const $disabledElements = $(disabledSelector);
      $disabledElements.each((index: number, disabledElement: HTMLElement): void => {
        const $disabledElement = $(disabledElement);
        $disabledElement.data('previousDisabled', $disabledElement.is(':disabled'));
        $disabledElement.prop('disabled', true);
      });
    });
    this.renderer.setSorting(false);
  }

  private disableEditionMode(): void {
    if (!this.editionMode) {
      return;
    }

    this.$paginatedList.removeClass(CombinationsMap.list.editionModeClass);

    // Re-enabled disabled elements
    this.editionDisabledElements.forEach((disabledSelector: string) => {
      const $disabledElements = $(disabledSelector);
      $disabledElements.each((index: number, disabledElement: HTMLElement): void => {
        const $disabledElement = $(disabledElement);
        $disabledElement.prop('disabled', $disabledElement.data('previousDisabled'));
      });
    });
    this.renderer.setSorting(true);
    this.editionMode = false;
    this.eventEmitter.emit(CombinationEvents.listEditionMode, this.editionMode);
  }

  private resetEdition(): void {
    $(CombinationsMap.list.fieldInputs, this.$combinationsFormContainer).each((index, input) => {
      const $input = $(input);
      const inputInitialValue = $input.data('initialValue');
      const inputInitialChecked = $input.data('initialChecked');

      if (!isUndefined(inputInitialValue)) {
        $input.val(inputInitialValue).trigger('change');
      }

      if (!isUndefined(inputInitialChecked)) {
        $input.prop('checked', inputInitialChecked);
      }

      // Remove modified class to reset display UX
      $input.removeClass(ProductMap.combinations.list.modifiedFieldClass);
      // Remove invalid class in case the field is an output with form errors
      $input.removeClass(ProductMap.combinations.list.invalidClass);
    });

    // Clean all the alerts that may come from an output with form errors
    $(CombinationsMap.list.errorAlerts, this.$combinationsFormContainer).remove();
  }

  private cancelEdition(): void {
    this.resetEdition();
    this.disableEditionMode();
  }

  private async saveEdition(): Promise<void> {
    this.renderer.setLoading(true);

    const response = await this.combinationsService.updateCombinationList(this.productId, this.getFormData());
    const jsonResponse = await response.json();

    if (jsonResponse.errors) {
      // If formContent is available we can replace the content to display the inline errors
      if (jsonResponse.formContent) {
        this.updateForm(jsonResponse.formContent);
      } else {
        notifyFormErrors(jsonResponse);
      }
      this.renderer.setLoading(false);
    } else if (jsonResponse.message) {
      $.growl({message: jsonResponse.message});
      this.disableEditionMode();
      this.eventEmitter.emit(CombinationEvents.refreshPage);
    }
  }

  /**
   * The product page doesn't include the combination list into a form tag because it is rendered inside a form
   * itself. So to get the data we create a dynamic form tag and include all the element from the list container
   * inside it, this way it is as if they were actually in a form tag.
   */
  private getFormData(): FormData {
    const combinationListForm = document.createElement('form');
    this.$combinationsFormContainer.get().forEach((formElement: HTMLElement) => {
      // We need to use appendChild and not innerHTML string content because the string content would lose the dynamic
      // values from the DOM (especially input values) and would only rely on the initial value from the first rendered
      // layout. We also need to clone each element before appending them or they would be removed from the DOM and the user
      // would not see them anymore.
      combinationListForm.appendChild(formElement.cloneNode(true));
    });

    return new FormData(combinationListForm);
  }

  private updateForm(formContent: string): void {
    // Replace form content with output from response
    this.$combinationsFormContainer.html(formContent);

    // Now re-watch the new inputs and set their initial value
    Object.keys(this.savedInputValues).forEach((inputName: string) => {
      const $input = $(`[name="${inputName}"]`, this.$combinationsFormContainer);
      this.watchInputChange($input, this.savedInputValues[inputName]);
    });

    // Trigger event so that external components can update the content (like checkboxes labels)
    this.eventEmitter.emit(CombinationEvents.listRendered);
    // Elements were re-rendered so they must be disabled again
    this.disableElements();
  }
}
