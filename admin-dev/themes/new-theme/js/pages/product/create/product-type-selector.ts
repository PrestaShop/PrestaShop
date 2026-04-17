/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
