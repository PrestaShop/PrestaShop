import BOBasePage from '@pages/BO/BObasePage';

import type AttributeData from '@data/faker/attribute';

import type {Page} from 'playwright';

/**
 * Add attribute page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddAttribute extends BOBasePage {
  public readonly createPageTitle: string;

  public readonly editPageTitle: string;

  private readonly nameInput: string;

  private readonly publicNameInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly indexableToggle: (toggle: string) => string;

  private readonly attributeTypeSelect: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add attribute page
   */
  constructor() {
    super();

    this.createPageTitle = 'Attributes > Add New Attribute • ';
    this.editPageTitle = 'Attributes > Edit:';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.nameInput = '#name_1';
    this.publicNameInput = '#public_name_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.indexableToggle = (toggle: string) => `#indexable_${toggle}`;
    this.attributeTypeSelect = '#group_type';
    this.saveButton = '#attribute_group_form_submit_btn';
  }
  /*
  Methods
   */

  /**
   * Fill attribute form and save it
   * @param page {Page} Browser tab
   * @param attributeData {AttributeData} Data to set on new/edit attribute form
   * @return {Promise<string>}
   */
  async addEditAttribute(page: Page, attributeData: AttributeData): Promise<string> {
    // Set names
    await this.setValue(page, this.nameInput, attributeData.name);
    await this.setValue(page, this.publicNameInput, attributeData.publicName);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, attributeData.url);
    await this.setValue(page, this.metaTitleInput, attributeData.metaTitle);

    // Set indexable toggle
    await this.setChecked(page, this.indexableToggle(attributeData.indexable ? 'on' : 'off'));

    // Set attribute type
    await this.selectByVisibleText(page, this.attributeTypeSelect, attributeData.attributeType);

    // Save attribute
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddAttribute();
