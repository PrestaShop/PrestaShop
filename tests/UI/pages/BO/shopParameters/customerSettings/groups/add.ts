import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import GroupData from '@data/faker/group';

/**
 * Add group page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddGroup extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly groupForm: string;

  private readonly nameInput: (idLang: number) => string;

  private readonly discountInput: string;

  private readonly priceDisplayMethodSelect: string;

  private readonly showPricesToggle: (toggle: string) => string;

  private readonly saveButton: string;

  private readonly dropdownButton: string;

  private readonly dropdownMenu: string;

  private readonly dropdownMenuItemLink: (idLang: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add group page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Groups > Add new •';
    this.pageTitleEdit = 'Groups > Edit:';

    // Form selectors
    this.groupForm = '#group_form';
    this.nameInput = (idLang: number) => `#name_${idLang}`;
    this.discountInput = '#reduction';
    this.priceDisplayMethodSelect = '#price_display_method';
    this.showPricesToggle = (toggle: string) => `${this.groupForm} #show_prices_${toggle}`;
    this.saveButton = '#group_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';

    // Language selectors
    this.dropdownButton = `${this.groupForm} button.dropdown-toggle`;
    this.dropdownMenu = `${this.groupForm} ul.dropdown-menu`;
    this.dropdownMenuItemLink = (idLang: number) => `${this.dropdownMenu} li:nth-child(${idLang}) a`;
  }

  /*
  Methods
   */

  /**
   * Change language in form
   * @param page {Page} Browser tab
   * @param idLang {number} Language to change 1 for 'EN' 2 for 'FR'
   * @return {Promise<void>}
   */
  async changeLanguage(page: Page, idLang: number): Promise<void> {
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
   * @param page {Page} Browser tab
   * @param groupData {GroupData} Data to set on create/edit form
   * @return {Promise<string>}
   */
  async createEditGroup(page: Page, groupData: GroupData): Promise<string> {
    await this.changeLanguage(page, 1);
    await this.setValue(page, this.nameInput(1), groupData.name);

    await this.changeLanguage(page, 2);
    await this.setValue(page, this.nameInput(2), groupData.frName);

    await this.setValue(page, this.discountInput, groupData.discount.toString());

    await this.selectByVisibleText(page, this.priceDisplayMethodSelect, groupData.priceDisplayMethod);

    await this.setChecked(page, this.showPricesToggle(groupData.shownPrices ? 'on' : 'off'));

    // Save group
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set price display method and save the form
   * @param page {Page} Browser tab
   * @param priceDisplayMethod {string} Value to select on price display method select
   * @returns {Promise<string>}
   */
  async setPriceDisplayMethod(page: Page, priceDisplayMethod: string): Promise<string> {
    await this.selectByVisibleText(page, this.priceDisplayMethodSelect, priceDisplayMethod);

    // Save customer group
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddGroup();
