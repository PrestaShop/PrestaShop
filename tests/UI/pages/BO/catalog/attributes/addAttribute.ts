import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import {
  type FakerAttribute,
} from '@prestashop-core/ui-testing';

/**
 * Add attribute page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddAttribute extends BOBasePage {
  public readonly createPageTitle: string;

  public readonly editPageTitle: (name: string) => string;

  private readonly nameInput: string;

  private readonly publicNameInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly indexableToggle: (toggle: number) => string;

  private readonly attributeTypeSelect: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add attribute page
   */
  constructor() {
    super();

    this.createPageTitle = `New attribute • ${global.INSTALL.SHOP_NAME}`;
    this.editPageTitle = (name: string) => `Editing attribute ${name} • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.nameInput = '#attribute_group_name_1';
    this.publicNameInput = '#attribute_group_public_name_1';
    this.urlInput = '#attribute_group_url_name_1';
    this.metaTitleInput = '#attribute_group_meta_title_1';
    this.indexableToggle = (toggle: number) => `#attribute_group_is_indexable_${toggle}`;
    this.attributeTypeSelect = '#attribute_group_group_type';
    this.saveButton = 'form[name="attribute_group"] div.card-footer button';
  }
  /*
  Methods
   */

  /**
   * Fill attribute form and save it
   * @param page {Page} Browser tab
   * @param attributeData {FakerAttribute} Data to set on new/edit attribute form
   * @return {Promise<string>}
   */
  async addEditAttribute(page: Page, attributeData: FakerAttribute): Promise<string> {
    // Set names
    await this.setValue(page, this.nameInput, attributeData.name);
    await this.setValue(page, this.publicNameInput, attributeData.publicName);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, attributeData.url);
    await this.setValue(page, this.metaTitleInput, attributeData.metaTitle);

    // Set indexable toggle
    await this.setChecked(page, this.indexableToggle(attributeData.indexable ? 1 : 0));

    // Set attribute type
    await this.selectByVisibleText(page, this.attributeTypeSelect, attributeData.attributeType);

    // Save attribute
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddAttribute();
