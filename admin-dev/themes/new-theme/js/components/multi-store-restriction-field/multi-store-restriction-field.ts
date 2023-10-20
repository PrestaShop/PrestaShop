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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import multiStoreRestrictionFieldMap from './multi-store-restriction-field-map';

const {$} = window;

/**
 * Enables multi store functionality for the page. It includes switch functionality and checkboxes
 */
export default class MultiStoreRestrictionField {
  constructor() {
    $(document).on(
      'change',
      multiStoreRestrictionFieldMap.multiStoreRestrictionCheckbox,
      (e: JQueryEventObject) => this.multiStoreRestrictionCheckboxFieldChangeEvent(e),
    );

    $(document).on(
      'change',
      multiStoreRestrictionFieldMap.multiStoreRestrictionSwitch,
      (e: JQueryEventObject) => this.multiStoreRestrictionSwitchFieldChangeEvent(e),
    );
  }

  /**
   * Toggles the checkbox field and enables or disables its related field.
   *
   * @param {Event} e
   * @private
   */
  private multiStoreRestrictionCheckboxFieldChangeEvent(
    e: JQueryEventObject,
  ): void {
    const $currentItem = <JQuery<HTMLElement>>$(e.currentTarget);

    this.toggleSourceFieldByTargetElement(
      $currentItem,
      !$currentItem.is(':checked'),
    );
  }

  /**
   * Mass updates multi-store checkbox fields - it enables or disabled the switch and after that
   * it calls the function
   * which handles the toggle update related form field by its current state.
   * @param {Event} e
   * @private
   */
  private multiStoreRestrictionSwitchFieldChangeEvent(
    e: JQueryEventObject,
  ): void {
    const $currentItem = $(e.currentTarget);
    const isSelected = parseInt(<string>$currentItem.val(), 10) === 1;
    const targetFormName = $currentItem.data('targetFormName');

    $(`form[name="${targetFormName}"]`)
      .find(multiStoreRestrictionFieldMap.multiStoreRestrictionCheckbox)
      .each((index, el) => {
        const $el = $(el);
        $el.prop('checked', isSelected);
        this.toggleSourceFieldByTargetElement($el, !isSelected);
      });
  }

  /**
   * Changes related form fields state to disabled or enabled.
   * It also toggles class disabled since for some fields
   * this class is used instead of the native disabled attribute.
   *
   * @param {jquery} $targetElement
   * @param {boolean} isDisabled
   * @private
   */
  private toggleSourceFieldByTargetElement(
    $targetElement: JQuery,
    isDisabled: boolean,
  ): void {
    const targetValue = $targetElement.data('shopRestrictionTarget');
    const $sourceFieldSelector = $(
      multiStoreRestrictionFieldMap.sourceField(targetValue),
    );
    $sourceFieldSelector.prop('disabled', isDisabled);
    $sourceFieldSelector.toggleClass('disabled', isDisabled);
  }
}
