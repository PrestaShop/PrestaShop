import BOBasePage from '@pages/BO/BObasePage';

import type AttributeValueData from '@data/faker/attributeValue';

import type {Page} from 'playwright';

/**
 * Add value page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddValue extends BOBasePage {
  public readonly createPageTitle: string;

  public readonly editPageTitle: string;

  private readonly attributeGroupSelect: string;

  private readonly valueInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly colorInput: string;

  private readonly textureFileInput: string;

  private readonly saveButton: string;

  private readonly saveAndStayButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add value page
   */
  constructor() {
    super();

    this.createPageTitle = 'Attributes > Add new value • ';
    this.editPageTitle = 'Attributes > Edit:';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.attributeGroupSelect = '#id_attribute_group';
    this.valueInput = '#name_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.colorInput = '#color_0';
    this.textureFileInput = '#texture-name';
    this.saveButton = '#attribute_form_submit_btn';
    this.saveAndStayButton = 'button[name=\'submitAddattributeAndStay\']';
  }

  /*
  Methods
   */

  /**
   * Fill value form and save it
   * @param page {Page} Browser tab
   * @param valueData {AttributeValueData} Data to set on add/edit value form
   * @param saveAndStay {boolean} True if we need to save and stay, false if not
   * @return {Promise<string>}
   */
  async addEditValue(page: Page, valueData: AttributeValueData, saveAndStay: boolean = false): Promise<string> {
    // Set group and value
    await this.selectByVisibleText(page, this.attributeGroupSelect, valueData.attributeName);
    await this.setValue(page, this.valueInput, valueData.value);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, valueData.url);
    await this.setValue(page, this.metaTitleInput, valueData.metaTitle);

    // Set color and texture inputs
    if (await this.elementVisible(page, this.colorInput, 1000)) {
      await this.setValue(page, this.colorInput, valueData.color);
      // await page.setInputFiles(this.textureFileInput, valueData.textureFileName);
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
