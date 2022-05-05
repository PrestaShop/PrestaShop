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
import CombinationsListRenderer from '@pages/product/combination/combinations-list-renderer';
import {EventEmitter} from 'events';
import {isUndefined} from '@PSTypes/typeguard';
import BigNumber from '@node_modules/bignumber.js';
import {notifyFormErrors} from '@components/form/helpers';
import CombinationsService from '@pages/product/services/combinations-service';

import ChangeEvent = JQuery.ChangeEvent;

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;
const CombinationsMap = ProductMap.combinations;

export default class CombinationsListEditor {
  private readonly productId: number;

  private readonly eventEmitter: EventEmitter;

  private readonly renderer: CombinationsListRenderer;

  private readonly $combinationsFormContainer: JQuery;

  private readonly $paginatedList: JQuery;

  private readonly editionDisabledElements: string[] = [
    CombinationsMap.bulkActionsDropdownBtn,
    CombinationsMap.tableRow.isSelectedCombination,
    CombinationsMap.bulkSelectAll,
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

  closeEdition(): void {
    this.disableEditionMode();
  }

  updateForm(formContent: string): void {
    // Replace form content with output from response
    this.$combinationsFormContainer.html(formContent);

    // Now re-watch the new inputs and set their initial value
    Object.keys(this.savedInputValues).forEach((inputName: string) => {
      const $input = $(`[name="${inputName}"]`, this.$combinationsFormContainer);
      this.watchInputChange($input, this.savedInputValues[inputName]);
    });
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

    this.$combinationsFormContainer.on('change', CombinationsMap.list.fieldInputs, (event: ChangeEvent) => {
      const $input = $(event.currentTarget);

      if (
        typeof $input.val() !== 'undefined'
        && typeof $input.data('initialValue') !== 'undefined'
        && $input.val() !== $input.data('initialValue')
      ) {
        this.enableEditionMode();
      }
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
    this.toggleChangedStatus($input, initialValue);

    $input.on('change', () => {
      this.toggleChangedStatus($input, $input.data('initialValue'));
    });
  }

  private toggleChangedStatus($input: JQuery, initialValue: string | number | string[]): void {
    let valueModified;
    const initialNumberValue = new BigNumber(Number(initialValue));
    const inputNumberValue = new BigNumber(Number($input.val()));

    if (!initialNumberValue.isNaN() && !inputNumberValue.isNaN()) {
      valueModified = !initialNumberValue.isEqualTo(inputNumberValue);
    } else {
      valueModified = initialValue !== $input.val();
    }

    $input.toggleClass(ProductMap.combinations.list.modifiedFieldClass, valueModified);
  }

  private enableEditionMode(): void {
    this.editionMode = true;
    this.$paginatedList.addClass(CombinationsMap.list.editionModeClass);

    // Disabled elements (bulk actions, filters, ...)
    this.editionDisabledElements.forEach((disabledSelector: string) => {
      const $disabledElements = this.$paginatedList.find(disabledSelector);
      $disabledElements.each((index: number, disabledElement: HTMLElement): void => {
        const $disabledElement = $(disabledElement);
        $disabledElement.data('initialDisabled', $disabledElement.is(':disabled'));
        $disabledElement.prop('disabled', true);
      });
    });
  }

  private disableEditionMode(): void {
    this.$paginatedList.removeClass(CombinationsMap.list.editionModeClass);

    // Re-enabled disabled elements
    this.editionDisabledElements.forEach((disabledSelector: string) => {
      const $disabledElements = this.$paginatedList.find(disabledSelector);
      $disabledElements.each((index: number, disabledElement: HTMLElement): void => {
        const $disabledElement = $(disabledElement);
        $disabledElement.prop('disabled', $disabledElement.data('initialDisabled'));
      });
    });
    this.editionMode = false;
  }

  private resetEdition(): void {
    $(CombinationsMap.list.fieldInputs, this.$combinationsFormContainer).each((index, input) => {
      const $input = $(input);

      if (
        typeof $input.val() !== 'undefined'
        && typeof $input.data('initialValue') !== 'undefined'
        && $input.val() !== $input.data('initialValue')
      ) {
        $input.val($input.data('initialValue')).trigger('change');
        // Remove modified class to reset display UX
        $input.removeClass(ProductMap.combinations.list.modifiedFieldClass);
        // Remove invalid class in case the field is an output with form errors
        $input.removeClass(ProductMap.combinations.list.invalidClass);
      }
    });

    // Clean all the alerts that may come from an output with form errors
    $(CombinationsMap.list.errorAlerts, this.$combinationsFormContainer).remove();
  }

  private cancelEdition(): void {
    this.resetEdition();
    this.disableEditionMode();
  }

  private async saveEdition(): Promise<void> {
    this.renderer.toggleLoading(true);

    const response = await this.combinationsService.updateCombinationList(this.productId, this.getFormData());
    const jsonResponse = await response.json();

    if (jsonResponse.errors) {
      // If formContent is available we can replace the content to display the inline errors
      if (jsonResponse.formContent) {
        this.updateForm(jsonResponse.formContent);
      } else {
        notifyFormErrors(jsonResponse);
      }
      this.renderer.toggleLoading(false);
    } else if (jsonResponse.message) {
      $.growl({message: jsonResponse.message});
      this.disableEditionMode();
      this.eventEmitter.emit(CombinationEvents.refreshCombinationList);
    }
  }

  private getFormData(): FormData {
    const combinationListForm = document.querySelector<HTMLFormElement>(ProductMap.combinations.list.form);

    if (!combinationListForm) {
      return new FormData();
    }

    return new FormData(combinationListForm);
  }
}
