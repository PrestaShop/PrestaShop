/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
