require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add cart rule page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCartRule extends BOBasePage {
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

    this.nameInput = ID => `#name_${ID}`;
    this.descriptionTextArea = `${this.cartRuleForm} textarea[name='description']`;

    // Discount code selectors
    this.codeInput = '#code';
    this.generateButton = '#cart_rule_informations  a.btn-default';

    // Toggle Selectors
    this.highlightToggle = toggle => `${this.cartRuleForm} #highlight_${toggle}`;
    this.partialUseToggle = toggle => `${this.cartRuleForm} #partial_use_${toggle}`;
    this.priorityInput = `${this.cartRuleForm} input[name='priority']`;
    this.statusToggle = toggle => `${this.cartRuleForm} #active_${toggle}`;

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

    // Actions tab
    this.actionsTabLink = '#cart_rule_link_actions';
    this.freeShippingToggle = toggle => `${this.cartRuleForm} #free_shipping_${toggle}`;

    // Discount percent selectors
    this.applyDiscountRadioButton = toggle => `${this.cartRuleForm} #apply_discount_${toggle}`;
    this.discountPercentRadioButton = this.applyDiscountRadioButton('percent');
    this.discountPercentInput = '#reduction_percent';

    // Discount amount selectors
    this.discountAmountRadioButton = this.applyDiscountRadioButton('amount');
    this.discountAmountInput = 'input[name=reduction_amount]';
    this.discountAmountCurrencySelect = 'select[name=reduction_currency]';
    this.discountAmountTaxSelect = 'select[name=reduction_tax]';

    // Discount others selectors
    this.discountOffRadioButton = this.applyDiscountRadioButton('off');

    // Exclude discount products and free gift selectors
    this.excludeDiscountProductsToggle = toggle => `${this.cartRuleForm} #reduction_exclude_special_${toggle}`;
    this.sendFreeGifToggle = toggle => `${this.cartRuleForm} #free_gift_${toggle}`;
    this.freeGiftFilterInput = '#giftProductFilter';
    this.freeGiftProductSelect = '#gift_product';

    // Form footer selectors
    this.saveButton = '#desc-cart_rule-save';
  }

  /* Methods */
  /**
   * Fill form in information tab
   * @param page {Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on information form
   * @return {Promise<void>}
   */
  async fillInformationForm(page, cartRuleData) {
    // Go to tab conditions
    await page.click(this.infomationsTabLink);

    // Fill information form
    await this.setValue(page, this.nameInput(1), cartRuleData.name);
    await this.setValue(page, this.descriptionTextArea, cartRuleData.description);

    // Generate a discount code
    if (cartRuleData.generateCode) {
      await page.click(this.generateButton);
    } else if (cartRuleData.code === null) {
      await this.deleteTextFromInput(page, this.codeInput);
    } else {
      await this.setValue(page, this.codeInput, cartRuleData.code);
    }

    // Set toggles
    await page.check(this.highlightToggle(cartRuleData.highlight ? 'on' : 'off'));
    await page.check(this.partialUseToggle(cartRuleData.partialUse ? 'on' : 'off'));

    // Set priority
    await this.setValue(page, this.priorityInput, cartRuleData.priority);

    // Set status
    await page.check(this.statusToggle(cartRuleData.status ? 'on' : 'off'));
  }

  /**
   * Fill form in condition tab
   * @param page {Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on conditions form
   * @return {Promise<void>}
   */
  async fillConditionsForm(page, cartRuleData) {
    // Go to tab conditions
    await page.click(this.conditionsTabLink);

    // Set Customer
    // Customer will not be set if we want to use the cart rule for any customer
    if (cartRuleData.customer) {
      await this.setValue(page, this.singleCustomerInput, cartRuleData.customer);
      await this.waitForVisibleSelector(page, `${this.singleCustomerResultBlock}:not([style*='display: none;'])`);
      await page.click(this.singleCustomerResultItem);
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
   * @param page {Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on actions form
   * @return {Promise<void>}
   */
  async fillActionsForm(page, cartRuleData) {
    // Go to actions tab
    await page.click(this.actionsTabLink);

    // Set free shipping toggle
    await page.check(this.freeShippingToggle(cartRuleData.freeShipping ? 'on' : 'off'));

    switch (cartRuleData.discountType) {
      case 'Percent':
        await page.check(this.discountPercentRadioButton);
        await this.setValue(page, this.discountPercentInput, cartRuleData.discountPercent);
        await page.check(this.excludeDiscountProductsToggle(cartRuleData.excludeDiscountProducts ? 'on' : 'off'));
        break;
      case 'Amount':
        await page.check(this.discountAmountRadioButton);
        await this.setValue(page, this.discountAmountInput, cartRuleData.discountAmount.value);
        await this.selectByVisibleText(page, this.discountAmountCurrencySelect, cartRuleData.discountAmount.currency);
        await this.selectByVisibleText(page, this.discountAmountTaxSelect, cartRuleData.discountAmount.tax);
        break;
      case 'None':
        await page.check(this.discountOffRadioButton);
        await page.check(this.excludeDiscountProductsToggle(cartRuleData.excludeDiscountProducts ? 'on' : 'off'));
        break;
      default:
        // Do nothing for this option
        throw new Error(`${cartRuleData.discountType} was not found as a discount option`);
    }

    // Set free gift
    await page.check(this.sendFreeGifToggle(cartRuleData.freeGift ? 'on' : 'off'));

    if (cartRuleData.freeGift) {
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
   * Create/edit cart rule
   * @param page {Page} Browser tab
   * @param cartRuleData {CartRuleData} Data to set on add/edit cart rule form
   * @returns {Promise<string>}
   */
  async createEditCartRules(page, cartRuleData) {
    // Fill information form
    await this.fillInformationForm(page, cartRuleData);

    // Fill conditions form
    await this.fillConditionsForm(page, cartRuleData);

    // Fill actions form
    await this.fillActionsForm(page, cartRuleData);

    // Save and return successful message
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddCartRule();
