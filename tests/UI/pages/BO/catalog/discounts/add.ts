import BOBasePage from '@pages/BO/BObasePage';

import type CartRuleData from '@data/faker/cartRule';

import type {Frame, Page} from 'playwright';

/**
 * Add cart rule page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCartRule extends BOBasePage {
  public readonly pageTitle: string;

  public readonly editPageTitle: string;

  private readonly cartRuleForm: string;

  private readonly infomationsTabLink: string;

  private readonly nameInput: (ID: number) => string;

  private readonly descriptionTextArea: string;

  private readonly codeInput: string;

  private readonly generateButton: string;

  private readonly highlightToggle: (toggle: string) => string;

  private readonly partialUseToggle: (toggle: string) => string;

  private readonly priorityInput: string;

  private readonly statusToggle: (toggle: string) => string;

  private readonly conditionsTabLink: string;

  private readonly singleCustomerInput: string;

  private readonly singleCustomerResultBlock: string;

  private readonly singleCustomerResultItem: string;

  private readonly dateFromInput: string;

  private readonly dateToInput: string;

  private readonly minimumAmountInput: string;

  private readonly minimumAmountCurrencySelect: string;

  private readonly minimumAmountTaxSelect: string;

  private readonly minimumAmountShippingSelect: string;

  private readonly quantityInput: string;

  private readonly quantityPerUserInput: string;

  private readonly countryRestriction: string;

  private readonly countrySelection: string;

  private readonly countryGroupRemoveButton: string;

  private readonly countryGroupAddButton: string;

  private readonly carrierRestriction: string;

  private readonly carrierRestrictionPickUpInStore: string;

  private readonly carrierRestrictionDeliveryNextDay: string;

  private readonly carrierRestrictionRemoveButton: string;

  private readonly carrierRestrictionAddButton: string;

  private readonly customerGroupRestriction: string;

  private readonly customerGroupSelection: string;

  private readonly customerGroupCustomer: string;

  private readonly customerGroupGuest: string;

  private readonly customerGroupVisitor: string;

  private readonly customerGroupRemoveButton: string;

  private readonly customerGroupAddButton: string;

  private readonly productSelectionCheckboxButton: string;

  private readonly productSelectionButton: string;

  private readonly productRuleGroupTable: string;

  private readonly productSelectionGroup: (groupNumber: number) => string;

  private readonly productSelectionGroupQuantity: (groupNumber: number) => string;

  private readonly productSelectionRuleType: (groupNumber: number) => string;

  private readonly productSelectionAddButton: (groupNumber: number) => string;

  private readonly productSelectionChooseButton: (groupNumber: number) => string;

  private readonly productSelectionSelectButton: (groupNumber: number) => string;

  private readonly productRestrictionSelectAddButton: (groupNumber: number) => string;

  private readonly closeFancyBoxButton: string;

  private readonly actionsTabLink: string;

  private readonly titleOfExcludeDiscountedProduct: string;

  private readonly freeShippingToggle: (toggle: string) => string;

  private readonly applyDiscountRadioButton: (toggle: string) => string;

  private readonly discountPercentRadioButton: string;

  private readonly discountPercentInput: string;

  private readonly discountAmountRadioButton: string;

  private readonly discountAmountInput: string;

  private readonly discountAmountCurrencySelect: string;

  private readonly discountAmountTaxSelect: string;

  private readonly discountOffRadioButton: string;

  private readonly applyDiscountToOrderCheckbox: string;

  private readonly applyDiscountToSpecificProductCheckbox: string;

  private readonly productNameInput: string;

  private readonly productSearchResultBlock: string;

  private readonly productSearchResultItem: string;

  private readonly excludeDiscountProductsToggle: (toggle: string) => string;

  private readonly sendFreeGifToggle: (toggle: string) => string;

  private readonly freeGiftFilterInput: string;

  private readonly freeGiftProductSelect: string;

  private readonly desktopButtonsBlock: string;

  private readonly saveButton: string;

  private readonly cancelButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add cart rule page
   */
  constructor() {
    super();

    this.pageTitle = 'Cart Rules > Add new •';
    this.editPageTitle = 'Cart Rules > Edit';

    // Selectors
    this.cartRuleForm = '#cart_rule_form';

    // Information tab
    this.infomationsTabLink = '#cart_rule_link_informations';

    this.nameInput = (ID: number) => `#name_${ID}`;
    this.descriptionTextArea = `${this.cartRuleForm} textarea[name='description']`;

    // Discount code selectors
    this.codeInput = '#code';
    this.generateButton = '#cart_rule_informations  a.btn-default';

    // Toggle Selectors
    this.highlightToggle = (toggle: string) => `${this.cartRuleForm} #highlight_${toggle}`;
    this.partialUseToggle = (toggle: string) => `${this.cartRuleForm} #partial_use_${toggle}`;
    this.priorityInput = `${this.cartRuleForm} input[name='priority']`;
    this.statusToggle = (toggle: string) => `${this.cartRuleForm} #active_${toggle}`;

    // Conditions tab
    this.conditionsTabLink = '#cart_rule_link_conditions';

    // Customer selectors
    this.singleCustomerInput = '#customerFilter';
    this.singleCustomerResultBlock = 'div.ac_results';
    this.singleCustomerResultItem = `${this.singleCustomerResultBlock} ul li`;

    // Valid date selectors
    this.dateFromInput = 'input[name=date_from]';
    this.dateToInput = 'input[name=date_to]';

    // Minimum amount selectors
    this.minimumAmountInput = 'input[name=minimum_amount]';
    this.minimumAmountCurrencySelect = 'select[name=minimum_amount_currency]';
    this.minimumAmountTaxSelect = 'select[name=minimum_amount_tax]';
    this.minimumAmountShippingSelect = 'select[name=minimum_amount_shipping]';

    // Quantity selectors
    this.quantityInput = 'input[name=quantity]';
    this.quantityPerUserInput = 'input[name=quantity_per_user]';

    // Restrictions
    // Country Group Selection
    this.countryRestriction = '#country_restriction';
    this.countrySelection = '#country_select_2';
    this.countryGroupRemoveButton = '#country_select_remove';
    this.countryGroupAddButton = '#country_select_add';

    // Carrier Restriction
    this.carrierRestriction = '#carrier_restriction';
    this.carrierRestrictionPickUpInStore = '#carrier_select_2 > option:nth-child(1)';
    this.carrierRestrictionDeliveryNextDay = '#carrier_select_2 > option:nth-child(2)';
    this.carrierRestrictionRemoveButton = '#carrier_select_remove';
    this.carrierRestrictionAddButton = '#carrier_select_add';

    // Customer group selection
    this.customerGroupRestriction = '#group_restriction';
    this.customerGroupSelection = '#group_select_2';
    this.customerGroupCustomer = `${this.customerGroupSelection} option:nth-child(1)`;
    this.customerGroupGuest = `${this.customerGroupSelection} option:nth-child(2)`;
    this.customerGroupVisitor = `${this.customerGroupSelection} option:nth-child(3)`;
    this.customerGroupRemoveButton = '#group_select_remove';
    this.customerGroupAddButton = '#group_select_add';

    // Product selection
    this.productSelectionCheckboxButton = '#product_restriction';
    this.productSelectionButton = '#product_restriction_div a.btn-default ';
    this.productRuleGroupTable = '#product_rule_group_table';
    this.productSelectionGroup = (groupNumber: number) => `#product_rule_group_${groupNumber}_tr`;
    this.productSelectionGroupQuantity = (groupNumber: number) => `${this.productSelectionGroup(groupNumber)}`
      + ` input[name='product_rule_group_${groupNumber}_quantity']`;
    this.productSelectionRuleType = (groupNumber: number) => `#product_rule_type_${groupNumber}`;
    this.productSelectionAddButton = (groupNumber: number) => `${this.productSelectionGroup(groupNumber)}`
      + ' a[href*=addProductRule]';
    this.productSelectionChooseButton = (groupNumber: number) => `#product_rule_1_${groupNumber}_choose_link`;
    this.productSelectionSelectButton = (groupNumber: number) => `#product_rule_select_1_${groupNumber}_1`;
    this.productRestrictionSelectAddButton = (groupNumber: number) => `#product_rule_select_1_${groupNumber}_add`;
    this.closeFancyBoxButton = 'body div.fancybox-overlay.fancybox-overlay-fixed a.fancybox-close';

    // Actions tab
    this.actionsTabLink = '#cart_rule_link_actions';
    this.titleOfExcludeDiscountedProduct = '#apply_discount_to_product_special label span[data-original-title]';
    this.freeShippingToggle = (toggle: string) => `${this.cartRuleForm} #free_shipping_${toggle}`;

    // Discount percent selectors
    this.applyDiscountRadioButton = (toggle: string) => `${this.cartRuleForm} #apply_discount_${toggle}`;
    this.discountPercentRadioButton = this.applyDiscountRadioButton('percent');
    this.discountPercentInput = '#reduction_percent';

    // Discount amount selectors
    this.discountAmountRadioButton = this.applyDiscountRadioButton('amount');
    this.discountAmountInput = 'input[name=reduction_amount]';
    this.discountAmountCurrencySelect = 'select[name=reduction_currency]';
    this.discountAmountTaxSelect = 'select[name=reduction_tax]';

    // Discount others selectors
    this.discountOffRadioButton = this.applyDiscountRadioButton('off');

    // Apply discount to selectors
    this.applyDiscountToOrderCheckbox = '#apply_discount_to_order';
    this.applyDiscountToSpecificProductCheckbox = '#apply_discount_to_product';
    this.productNameInput = '#reductionProductFilter';
    this.productSearchResultBlock = 'div.ac_results';
    this.productSearchResultItem = `${this.productSearchResultBlock} .ac_even`;

    // Exclude discount products and free gift selectors
    this.excludeDiscountProductsToggle = (toggle: string) => `${this.cartRuleForm} #reduction_exclude_special_${toggle}`;
    this.sendFreeGifToggle = (toggle: string) => `${this.cartRuleForm} #free_gift_${toggle}`;
    this.freeGiftFilterInput = '#giftProductFilter';
    this.freeGiftProductSelect = '#gift_product';

    // Form footer selectors
    this.desktopButtonsBlock = '.desktop-buttons';
    this.saveButton = `${this.desktopButtonsBlock} #desc-cart_rule-save`;
    this.cancelButton = '#desc-cart_rule-cancel';
  }

  /* Methods */
  /**
   * Get generate button name
   * @param page {Frame|Page} Browser tab
   * @return {Promise<string>}
   */
  async getGenerateButtonName(page: Page): Promise<string> {
    return this.getTextContent(page, this.generateButton);
  }

  /**
   * Fill form in information tab
   * @param page {Frame|Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on information form
   * @return {Promise<void>}
   */
  async fillInformationForm(page: Frame | Page, cartRuleData: CartRuleData): Promise<void> {
    // Go to tab conditions
    await page.locator(this.infomationsTabLink).click();

    // Fill information form
    await this.setValue(page, this.nameInput(1), cartRuleData.name);
    await this.setValue(page, this.descriptionTextArea, cartRuleData.description);

    // Generate a discount code
    if (cartRuleData.generateCode) {
      await page.locator(this.generateButton).click();
    } else if (cartRuleData.code === null) {
      await this.clearInput(page, this.codeInput);
    } else {
      await this.setValue(page, this.codeInput, cartRuleData.code);
    }

    // Set toggles
    await this.setChecked(page, this.highlightToggle(cartRuleData.highlight ? 'on' : 'off'));
    await this.setChecked(page, this.partialUseToggle(cartRuleData.partialUse ? 'on' : 'off'));

    // Set priority
    await this.setValue(page, this.priorityInput, cartRuleData.priority);

    // Set status
    await this.setChecked(page, this.statusToggle(cartRuleData.status ? 'on' : 'off'));
  }

  /**
   * Fill form in condition tab
   * @param page {Frame|Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on conditions form
   * @return {Promise<void>}
   */
  async fillConditionsForm(page: Frame | Page, cartRuleData: CartRuleData): Promise<void> {
    // Go to tab conditions
    await page.locator(this.conditionsTabLink).click();

    // Set Customer
    // Customer will not be set if we want to use the cart rule for any customer
    if (cartRuleData.customer) {
      await this.setValue(page, this.singleCustomerInput, cartRuleData.customer.email);
      await this.waitForVisibleSelector(page, `${this.singleCustomerResultBlock}:not([style*='display: none;'])`);
      await page.locator(this.singleCustomerResultItem).click();
    }

    // Fill date from if its changed
    if (cartRuleData.dateFrom) {
      await this.setValue(page, this.dateFromInput, cartRuleData.dateFrom);
      await page.press(this.dateFromInput, 'Enter');
    }

    // Fill date to if its changed
    if (cartRuleData.dateTo) {
      await this.setValue(page, this.dateToInput, cartRuleData.dateTo);
      await page.press(this.dateToInput, 'Enter');
    }

    // Set carrier discount
    if (cartRuleData.carrierRestriction) {
      await this.setChecked(page, this.carrierRestriction);
      await page.locator(this.carrierRestrictionPickUpInStore).click();
      await page.locator(this.carrierRestrictionRemoveButton).click();
    }

    // Choose the country selection
    if (cartRuleData.countrySelection) {
      await this.setChecked(page, this.countryRestriction);
      await this.selectByValue(page, this.countrySelection, cartRuleData.countryIDToRemove);
      await page.locator(this.countryGroupRemoveButton).click();
    }

    // Set Customer Group Selection
    if (cartRuleData.customerGroupSelection) {
      await this.setChecked(page, this.customerGroupRestriction);
      await page.locator(this.customerGroupCustomer).click();
      await page.locator(this.customerGroupRemoveButton).click();
    }

    // Set product selection
    if (cartRuleData.productSelection) {
      await this.setChecked(page, this.productSelectionCheckboxButton);

      for (let i = 0; i < cartRuleData.productSelectionNumber; i++) {
        const selectorIndex = i + 1;
        await this.waitForSelectorAndClick(page, this.productSelectionButton);
        await this.setValue(page, this.productSelectionGroupQuantity(selectorIndex), cartRuleData.productRestriction[i].quantity);
        await this.selectByVisibleText(
          page,
          this.productSelectionRuleType(selectorIndex),
          cartRuleData.productRestriction[i].ruleType,
        );
        await this.waitForSelectorAndClick(page, this.productSelectionAddButton(selectorIndex));
        await this.waitForSelectorAndClick(page, this.productSelectionChooseButton(selectorIndex));
        await this.selectByValue(
          page,
          this.productSelectionSelectButton(selectorIndex),
          cartRuleData.productRestriction[i].value,
        );
        await this.waitForSelectorAndClick(page, this.productRestrictionSelectAddButton(selectorIndex));
        await this.waitForSelectorAndClick(page, this.closeFancyBoxButton);
      }
    }

    // Fill minimum amount values
    await this.setValue(page, this.minimumAmountInput, cartRuleData.minimumAmount.value);
    await this.selectByVisibleText(page, this.minimumAmountCurrencySelect, cartRuleData.minimumAmount.currency);
    await this.selectByVisibleText(page, this.minimumAmountTaxSelect, cartRuleData.minimumAmount.tax);
    await this.selectByVisibleText(page, this.minimumAmountShippingSelect, cartRuleData.minimumAmount.shipping);

    // Fill quantities
    await this.setValue(page, this.quantityInput, cartRuleData.quantity);
    await this.setValue(page, this.quantityPerUserInput, cartRuleData.quantityPerUser);
  }

  /**
   * Fill actions tab
   * @param page {Frame|Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on actions form
   * @return {Promise<void>}
   */
  async fillActionsForm(page: Frame | Page, cartRuleData: CartRuleData): Promise<void> {
    // Go to actions tab
    await page.locator(this.actionsTabLink).click();

    // Set free shipping toggle
    await this.setChecked(page, this.freeShippingToggle(cartRuleData.freeShipping ? 'on' : 'off'));

    switch (cartRuleData.discountType) {
      case 'Percent':
        await this.setChecked(page, this.discountPercentRadioButton);
        if (cartRuleData.discountPercent) {
          await this.setValue(page, this.discountPercentInput, cartRuleData.discountPercent);
        }
        await this.setChecked(
          page,
          this.excludeDiscountProductsToggle(cartRuleData.excludeDiscountProducts ? 'on' : 'off'),
        );
        break;
      case 'Amount':
        await this.setChecked(page, this.discountAmountRadioButton);
        if (cartRuleData.discountAmount) {
          await this.setValue(page, this.discountAmountInput, cartRuleData.discountAmount.value);
          await this.selectByVisibleText(page, this.discountAmountCurrencySelect, cartRuleData.discountAmount.currency);
          await this.selectByVisibleText(page, this.discountAmountTaxSelect, cartRuleData.discountAmount.tax);
        }
        break;
      case 'None':
        await this.setChecked(page, this.discountOffRadioButton);
        await this.setChecked(
          page,
          this.excludeDiscountProductsToggle(cartRuleData.excludeDiscountProducts ? 'on' : 'off'),
        );
        break;
      default:
        // Do nothing for this option
        throw new Error(`${cartRuleData.discountType} was not found as a discount option`);
    }

    // Set apply discount
    switch (cartRuleData.applyDiscountTo) {
      case 'Order':
        await this.setChecked(page, this.applyDiscountToOrderCheckbox);
        break;
      case 'Specific product':
        await this.setChecked(page, this.applyDiscountToSpecificProductCheckbox);
        if (cartRuleData.product) {
          await this.setValue(page, this.productNameInput, cartRuleData.product);
        }
        await this.waitForVisibleSelector(page, this.productSearchResultBlock);
        await this.waitForSelectorAndClick(page, this.productSearchResultItem);
        break;
      default:
        // Do nothing for this option
        throw new Error(`${cartRuleData.applyDiscountTo} was not found as apply a discount to option`);
    }

    // Set free gift
    await this.setChecked(page, this.sendFreeGifToggle(cartRuleData.freeGift ? 'on' : 'off'));

    if (cartRuleData.freeGift && cartRuleData.freeGiftProduct) {
      await this.setValue(page, this.freeGiftFilterInput, cartRuleData.freeGiftProduct.name);
      await this.waitForVisibleSelector(page, this.freeGiftProductSelect);
      await this.selectByVisibleText(
        page,
        this.freeGiftProductSelect,
        `${cartRuleData.freeGiftProduct.name} - €${cartRuleData.freeGiftProduct.price}`,
      );
    }
  }

  /**
   * Get title of exclude discounted product
   * @param page
   */
  async getTitleOfExcludeDiscountedProduct(page: Page): Promise<string> {
    // Go to actions tab
    await page.locator(this.actionsTabLink).click();

    return this.getAttributeContent(page, this.titleOfExcludeDiscountedProduct, 'data-original-title');
  }

  /**
   * Create/edit cart rule
   * @param page {Frame|Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on add/edit cart rule form
   * @param waitForNavigation {boolean} True if we need to save and waitForNavigation
   * @returns {Promise<string|null>}
   */
  async createEditCartRules(
    page: Frame | Page,
    cartRuleData: CartRuleData,
    waitForNavigation: boolean = true,
  ): Promise<string | null> {
    // Fill information form
    await this.fillInformationForm(page, cartRuleData);

    // Fill conditions form
    await this.fillConditionsForm(page, cartRuleData);

    // Fill actions form
    await this.fillActionsForm(page, cartRuleData);

    if (waitForNavigation) {
      // Save and return successful message
      return this.saveCartRule(page);
    }

    // Save
    await this.waitForSelectorAndClick(page, this.saveButton);
    return null;
  }

  /**
   * Save cart rule
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveCartRule(page: Frame | Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.saveButton);

    if (await this.elementVisible(page, `${this.alertSuccessBlock}[role='alert']`, 2000)) {
      return this.getTextContent(page, `${this.alertSuccessBlock}[role='alert']`);
    }
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get limit single customer
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getLimitSingleCustomer(page: Page): Promise<string | null> {
    // Go to tab conditions
    await page.locator(this.conditionsTabLink).click();

    return this.getAttributeContent(page, this.singleCustomerInput, 'value');
  }

  /**
   * Get amount value
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAmountValue(page: Page): Promise<string | null> {
    // Go to actions tab
    await page.locator(this.actionsTabLink).click();

    return this.getAttributeContent(page, this.discountAmountInput, 'value');
  }

  /**
   * Click on cancel button
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnCancelButton(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.cancelButton);
  }
}

export default new AddCartRule();
