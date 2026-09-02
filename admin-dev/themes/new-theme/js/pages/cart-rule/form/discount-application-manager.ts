/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import CartRuleMap from '@pages/cart-rule/cart-rule-map';
import ProductSearchInput from '@components/form/product-search-input';

export default class DiscountApplicationManager {
  private readonly reductionTypeSelector: string;

  private readonly applicationSelect: string;

  constructor() {
    this.reductionTypeSelector = CartRuleMap.reductionTypeSelect;
    this.applicationSelect = CartRuleMap.discountApplicationSelect;
    this.init();
  }

  private init(): void {
    new ProductSearchInput(CartRuleMap.specificProductSearchComponent);

    this.updateChoices(this.getReductionTypeSelect().value);
    this.toggleExcludeDiscountedProducts(this.getReductionTypeSelect().value);
    this.toggleSpecificProductsSearch(this.getApplicationSelect().value);

    this.listenReductionTypeChanges();
    this.listenDiscountApplicationChanges();
  }

  private listenReductionTypeChanges(): void {
    const reductionTypeSelect = this.getReductionTypeSelect();

    reductionTypeSelect.addEventListener('change', (e: Event) => {
      const currentTarget = <HTMLSelectElement> e.currentTarget;
      this.updateChoices(currentTarget.value);
      this.toggleExcludeDiscountedProducts(currentTarget.value);
    });
  }

  private listenDiscountApplicationChanges(): void {
    const applicationSelect = this.getApplicationSelect();

    applicationSelect.addEventListener('change', (e: Event) => {
      const currentTarget = <HTMLSelectElement> e.currentTarget;
      this.toggleSpecificProductsSearch(currentTarget.value);
    });
  }

  private updateChoices(reductionType: string): void {
    const discountApplicationSelect = <HTMLSelectElement> document.querySelector(CartRuleMap.discountApplicationSelect);

    const selectedValue = discountApplicationSelect.value;
    $(discountApplicationSelect).empty();

    let choices: Record<string, string> = JSON.parse(<string> discountApplicationSelect.dataset.amountChoices);

    if (reductionType === 'percentage') {
      choices = JSON.parse(<string> discountApplicationSelect.dataset.percentageChoices);
    }

    Object.entries(choices).forEach(([label, value]) => {
      const newOption = document.createElement('option');

      newOption.label = label;
      newOption.value = value;
      newOption.selected = selectedValue === value;

      discountApplicationSelect.add(newOption);
    });
  }

  private toggleExcludeDiscountedProducts(reductionType: string): void {
    const excludeDiscountedProductsEl = <HTMLDivElement> document.querySelector(CartRuleMap.applyToDiscountedProductsContainer);

    if (reductionType === 'percentage') {
      $(excludeDiscountedProductsEl).fadeIn();
    } else {
      $(excludeDiscountedProductsEl).fadeOut();
    }
  }

  private toggleSpecificProductsSearch(applicationChoice: string): void {
    const specificProductSearchContainer = <HTMLDivElement> document.querySelector(CartRuleMap.specificProductSearchContainer);

    if (applicationChoice === 'specific_product') {
      $(specificProductSearchContainer).fadeIn();
    } else {
      $(specificProductSearchContainer).fadeOut();
    }
  }

  private getReductionTypeSelect(): HTMLSelectElement {
    return <HTMLSelectElement> document.querySelector(this.reductionTypeSelector);
  }

  private getApplicationSelect(): HTMLSelectElement {
    return <HTMLSelectElement> document.querySelector(this.applicationSelect);
  }
}
