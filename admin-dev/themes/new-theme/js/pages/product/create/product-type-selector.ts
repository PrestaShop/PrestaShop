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

const ProductTypeMap = ProductMap.productType.productTypeSelector;

export default class ProductTypeSelector {
  private $typeSelector: JQuery;

  private $descriptionContainer: JQuery;

  private initialType?: string;

  constructor(typeSelector: string, initialType: string | undefined = undefined) {
    this.$typeSelector = $(typeSelector);
    this.$descriptionContainer = $(ProductTypeMap.typeDescription);
    this.initialType = initialType;
    this.init();
  }

  private init() {
    $(ProductTypeMap.choicesContainer).on('click', ProductTypeMap.typeChoices, (event: JQuery.ClickEvent) => {
      const clickedChoice = $(event.currentTarget);

      this.selectChoice(clickedChoice.data('value'));
    });

    // On over/out toggle displayed description
    $(ProductTypeMap.choicesContainer).on('mouseenter', ProductTypeMap.typeChoices, (event: JQuery.TriggeredEvent) => {
      const overChoice = $(event.currentTarget);
      this.displayDescription(overChoice.data('description'));
    },
    );
    $(ProductTypeMap.choicesContainer).on('mouseleave', ProductTypeMap.typeChoices, () => {
      this.displaySelectedDescription();
    });

    // Display initial value
    this.selectChoice(<string> this.$typeSelector.find(':selected').val());
    if (this.initialType) {
      const $initialChoice = $(`${ProductTypeMap.typeChoices}[data-value=${this.initialType}]`);
      $initialChoice.prop('disabled', true);
    }
  }

  /**
   * @param {string} value
   * @private
   */
  private selectChoice(value: string): void {
    const selectedChoice = $(`${ProductTypeMap.typeChoices}[data-value=${value}]`);

    // Reset all options
    $(ProductTypeMap.typeChoices).removeClass(ProductTypeMap.selectedChoiceClass);
    $(ProductTypeMap.typeChoices).addClass(ProductTypeMap.defaultChoiceClass);

    // Select clicked one
    selectedChoice.removeClass(ProductTypeMap.defaultChoiceClass);
    selectedChoice.addClass(ProductTypeMap.selectedChoiceClass);

    // Update selected option in select input, trigger change for those who listen
    this.$typeSelector.val(<string> selectedChoice.data('value')).trigger('change');
    this.displaySelectedDescription();
  }

  /**
   * @param {string} description
   * @private
   */
  private displayDescription(description: string): void {
    this.$descriptionContainer.html(description);
  }

  /**
   * @private
   */
  private displaySelectedDescription(): void {
    this.displayDescription(this.$typeSelector.find(':selected').data('description'));
  }
}
