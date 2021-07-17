require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddGroup extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Groups > Add new •';
    this.pageTitleEdit = 'Groups > Edit:';

    // Form selectors
    this.groupForm = '#group_form';
    this.nameInput = idLang => `#name_${idLang}`;
    this.discountInput = '#reduction';
    this.priceDisplayMethodSelect = '#price_display_method';
    this.showPricesToggle = toggle => `${this.groupForm} label[for='show_prices_${toggle}']`;
    this.saveButton = '#group_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';

    // Language selectors
    this.dropdownButton = `${this.groupForm} button.dropdown-toggle`;
    this.dropdownMenu = `${this.groupForm} ul.dropdown-menu`;
    this.dropdownMenuItemLink = idLang => `${this.dropdownMenu} li:nth-child(${idLang}) a`;
  }

  /*
  Methods
   */

  /**
   * Change language in form
   * @param page
   * @param idLang
   * @return {Promise<void>}
   */
  async changeLanguage(page, idLang) {
    await Promise.all([
      page.click(this.dropdownButton),
      this.waitForVisibleSelector(page, this.dropdownMenuItemLink(idLang)),
    ]);

    await Promise.all([
      page.click(this.dropdownMenuItemLink(idLang)),
      this.waitForHiddenSelector(page, this.dropdownMenuItemLink(idLang)),
    ]);
  }

  /**
   * Fill group form and get successful message
   * @param page
   * @param groupData
   * @return {Promise<string>}
   */
  async createEditGroup(page, groupData) {
    await this.changeLanguage(page, 1);
    await this.setValue(page, this.nameInput(1), groupData.name);

    await this.changeLanguage(page, 2);
    await this.setValue(page, this.nameInput(2), groupData.frName);

    await this.setValue(page, this.discountInput, groupData.discount.toString());

    await this.selectByVisibleText(page, this.priceDisplayMethodSelect, groupData.priceDisplayMethod);

    await page.click(this.showPricesToggle(groupData ? 'on' : 'off'));

    // Save group
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set price display method and save the form
   * @param page
   * @param priceDisplayMethod
   * @returns {Promise<void>}
   */
  async setPriceDisplayMethod(page, priceDisplayMethod) {
    await this.selectByVisibleText(page, this.priceDisplayMethodSelect, priceDisplayMethod);

    // Save customer group
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddGroup();
