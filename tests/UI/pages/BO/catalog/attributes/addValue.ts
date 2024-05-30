import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import {
  type FakerAttributeValue,
} from '@prestashop-core/ui-testing';

/**
 * Add value page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddValue extends BOBasePage {
  public readonly createPageTitle: string;

  public readonly editPageTitle: (name: string) => string;

  private readonly attributeGroupSelect: string;

  private readonly valueInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly colorInput: string;

  private readonly saveButton: string;

  private readonly saveAndStayButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add value page
   */
  constructor() {
    super();

    this.createPageTitle = `New attribute value • ${global.INSTALL.SHOP_NAME}`;
    this.editPageTitle = (name: string) => `Editing attribute value ${name} • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.attributeGroupSelect = '#attribute_attribute_group';
    this.valueInput = '#attribute_name_1';
    this.urlInput = '#attribute_url_name_1';
    this.metaTitleInput = '#attribute_meta_title_1';
    this.colorInput = '#color_0';
    this.saveButton = 'form[name="attribute"] div.card-footer button#save-button';
    this.saveAndStayButton = 'form[name="attribute"] div.card-footer button[name="save-and-add-new"]';
  }

  /*
  Methods
   */

  /**
   * Fill value form and save it
   * @param page {Page} Browser tab
   * @param valueData {FakerAttributeValue} Data to set on add/edit value form
   * @param saveAndStay {boolean} True if we need to save and stay, false if not
   * @return {Promise<string>}
   */
  async addEditValue(page: Page, valueData: FakerAttributeValue, saveAndStay: boolean = false): Promise<string> {
    // Set group and value
    await this.selectByVisibleText(page, this.attributeGroupSelect, `${valueData.attributeName} (#${valueData.attributeID})`);
    await this.setValue(page, this.valueInput, valueData.value);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, valueData.url);
    await this.setValue(page, this.metaTitleInput, valueData.metaTitle);

    // Set color and texture inputs
    if (await this.elementVisible(page, this.colorInput, 1000)) {
      await this.setValue(page, this.colorInput, valueData.color);
    }

    // Save value
    if (saveAndStay) {
      await page.locator(this.saveAndStayButton).click();
    } else {
      await this.clickAndWaitForURL(page, this.saveButton);
    }

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddValue();
