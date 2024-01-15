// Import pages
import BOBasePage from '@pages/BO/BObasePage';
import createProductPage from '@pages/BO/catalog/products/add';

// Import data
import type ProductData from '@data/faker/product';
import {ProductFeatures} from '@data/types/product';

import type {Frame, Page} from 'playwright';
import {expect} from 'chai';

/**
 * Details tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DetailsTab extends BOBasePage {
  public readonly featureCustomValueNotDefaultLanguageMessage: string;

  private readonly detailsTabLink: string;

  private readonly productEAN13Input: string;

  private readonly productISBNInput: string;

  private readonly productMPNInput: string;

  private readonly productReferenceInput: string;

  private readonly productUPCInput: string;

  private readonly productConditionSelect: string;

  private readonly confirmDeleteFeatureButton: string;

  private readonly tableFeatures: string;

  private readonly tableFeaturesRow: (nthChild: number) => string;

  private readonly tableFeaturesCellAction: (nthChild: number) => string;

  private readonly tableFeaturesBtnDelete: (nthChild: number) => string;

  private readonly manageFeaturesLink: string;

  private readonly manageAllFilesLink: string;

  private readonly searchFileInput: string;

  private readonly searchFileResult: string;

  private readonly addNewFileButton: string;

  private readonly createFileFrame: string;

  private readonly fileNameInput: string;

  private readonly fileDescriptionInput: string;

  private readonly attachFileButton: string;

  private readonly saveFileButton: string;

  private readonly deleteFileConfirmButton: string;

  private readonly noFileAttachedErrorAlert: string;

  private readonly addNewCustomizationButton: string;

  private readonly confirmDeleteCustomizationButton: string;

  private readonly deleteCustomizationModal: string;

  private readonly deleteFileModal: string;

  private readonly addFeatureFeatureSelect: string;

  private readonly addFeatureValueSelect: string;

  private readonly addFeatureValueButtonLang: string;

  private readonly addFeatureValueSpanLang: (lang: string) => string;

  private readonly addFeatureValueInputLang: (langId: number) => string;

  private readonly addFeatureButton: string;

  private readonly deleteFeatureModal: string;

  private readonly deleteFileIcon: (row: number) => string;

  private readonly displayCondition: (row: number) => string;

  private readonly customizationNameInput: (row: number) => string;

  private readonly customizationTypeSelect: (row: number) => string;

  private readonly deleteCustomizationIcon: (row: number) => string;

  private readonly customizationRequiredButton: (row: number, toEnable: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on details tab
   */
  constructor() {
    super();

    // Messages
    this.featureCustomValueNotDefaultLanguageMessage = 'The field "Custom value" is required at least in your default language.';

    // Selectors in details tab
    // References section
    this.detailsTabLink = '#product_details-tab-nav';
    this.productReferenceInput = '#product_details_references_reference';
    this.productMPNInput = '#product_details_references_mpn';
    this.productUPCInput = '#product_details_references_upc';
    this.productEAN13Input = '#product_details_references_ean_13';
    this.productISBNInput = '#product_details_references_isbn';
    // Features section
    this.addFeatureFeatureSelect = '#product_details_features_feature_id';
    this.addFeatureValueSelect = '#product_details_features_feature_value_id';
    this.addFeatureValueButtonLang = '#product_details_features_custom_value_dropdown';
    this.addFeatureValueSpanLang = (lang: string) => `${this.addFeatureValueButtonLang} + div.dropdown-menu.show`
      + `> span[data-locale='${lang}']`;
    this.addFeatureValueInputLang = (langId: number) => `#product_details_features_custom_value_${langId}`;
    this.addFeatureButton = '#product_details_features_add_feature';
    this.deleteFeatureModal = '#modal-confirm-delete-feature-value';
    this.confirmDeleteFeatureButton = `${this.deleteFeatureModal} div.modal-footer button.btn-confirm-submit`;
    this.tableFeatures = '#product_details_features_feature_collection tbody';
    this.tableFeaturesRow = (nthChild: number) => `${this.tableFeatures} tr:nth-child(${nthChild})`;
    this.tableFeaturesCellAction = (nthChild: number) => `${this.tableFeaturesRow(nthChild)} td.feature-actions`;
    this.tableFeaturesBtnDelete = (nthChild: number) => `${this.tableFeaturesCellAction(nthChild)} button`;
    this.manageFeaturesLink = 'div.product-features-controls + div > a';
    // Attached files section
    this.manageAllFilesLink = '#product_details div.small.font-secondary a[href*=\'sell/attachments/\']';
    this.searchFileInput = '#product_details_attachments_attached_files_search_input';
    this.searchFileResult = '#product_details_attachments_attached_files div.search-with-icon span div';
    this.addNewFileButton = '#product_details_attachments_add_attachment_btn';
    this.createFileFrame = '#modal-create-product-attachment';
    this.fileNameInput = '#attachment_name_1';
    this.fileDescriptionInput = '#attachment_file_description_1';
    this.attachFileButton = '#attachment_file';
    this.saveFileButton = '#main-div div.card-footer button';
    this.deleteFileIcon = (row: number) => `#product_details_attachments_attached_files_${row} i.entity-item-delete`;
    this.deleteFileModal = '#modal-confirm-remove-entity';
    this.deleteFileConfirmButton = `${this.deleteFileModal} div.modal-footer button.btn-confirm-submit`;
    this.noFileAttachedErrorAlert = '#product_details_attachments_attached_files div.alert-info p.alert-text';
    // Display condition section
    this.displayCondition = (toEnable: number) => `#product_details_show_condition_${toEnable}`;
    this.productConditionSelect = '#product_details_condition';
    // Customization section
    this.addNewCustomizationButton = '#product_details_customizations_add_customization_field';
    this.customizationNameInput = (row: number) => `#product_details_customizations_customization_fields_${row}_name_1`;
    this.customizationTypeSelect = (row: number) => `#product_details_customizations_customization_fields_${row}_type`;
    this.customizationRequiredButton = (row: number, toEnable: number) => '#product_details_customizations_customization'
      + `_fields_${row}_required_${toEnable}`;
    this.deleteCustomizationIcon = (row: number) => `#product_details_customizations_customization_fields_${row}_remove`
      + ' i.material-icons';
    this.deleteCustomizationModal = '#modal-confirm-delete-customization';
    this.confirmDeleteCustomizationButton = `${this.deleteCustomizationModal} div.modal-footer button.btn-confirm-submit`;
  }

  /*
  Methods
   */
  /**
   * Set product details
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in details form
   * @returns {Promise<void>}
   */
  async setProductDetails(page: Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.detailsTabLink);
    await this.setValue(page, this.productReferenceInput, productData.reference);
  }

  /**
   * Set value for EAN 13
   * @param page {Page} Browser tab
   * @param value {string} Value
   * @returns {Promise<void>}
   */
  async setEAN13(page: Page, value: string): Promise<void> {
    await this.setValue(page, this.productEAN13Input, value);
  }

  /**
   * Set value for setISBN
   * @param page {Page} Browser tab
   * @param value {string} Value
   * @returns {Promise<void>}
   */
  async setISBN(page: Page, value: string): Promise<void> {
    await this.setValue(page, this.productISBNInput, value);
  }

  /**
   * Set value for MPN
   * @param page {Page} Browser tab
   * @param value {string} Value
   * @returns {Promise<void>}
   */
  async setMPN(page: Page, value: string): Promise<void> {
    await this.setValue(page, this.productMPNInput, value);
  }

  /**
   * Set value for UPC
   * @param page {Page} Browser tab
   * @param value {string} Value
   * @returns {Promise<void>}
   */
  async setUPC(page: Page, value: string): Promise<void> {
    await this.setValue(page, this.productUPCInput, value);
  }

  /**
   * Get error message in references form
   * @param page {Page} Browser tab
   * @param inputNumber {number} Input number to get error message
   * @returns {Promise<string>}
   */
  async getErrorMessageInReferencesForm(page: Page, inputNumber: number): Promise<string> {
    await this.clickAndWaitForLoadState(page, createProductPage.saveProductButton);

    return this.getTextContent(page, `#product_details_references div:nth-child(${inputNumber}) div.alert-text`);
  }

  /**
   * Set feature
   * @param page {Page} Browser tab
   * @param productFeatures {ProductFeatures[]} Data to set on feature form
   * @returns {Promise<void>}
   */
  async setFeature(page: Page, productFeatures: ProductFeatures[]): Promise<void> {
    for (let i: number = 0; i < productFeatures.length; i++) {
      await this.selectByVisibleText(page, this.addFeatureFeatureSelect, productFeatures[i].featureName, true);
      await this.waitForVisibleSelector(page, `${this.addFeatureValueSelect}:not([disabled])`);

      if (productFeatures[i].preDefinedValue) {
        await this.selectByVisibleText(page, this.addFeatureValueSelect, productFeatures[i].preDefinedValue!, true);
      }
      if (productFeatures[i].customizedValueEn || productFeatures[i].customizedValueFr) {
        await this.selectByValue(page, this.addFeatureValueSelect, -1, true);
        if (productFeatures[i].customizedValueEn) {
          await page.locator(this.addFeatureValueButtonLang).click();
          await page.locator(this.addFeatureValueSpanLang('en')).click();
          await this.waitForVisibleSelector(page, this.addFeatureValueInputLang(1));
          await this.setValue(page, this.addFeatureValueInputLang(1), productFeatures[i].customizedValueEn!);
        }
        if (productFeatures[i].customizedValueFr) {
          await page.locator(this.addFeatureValueButtonLang).click();
          await page.locator(this.addFeatureValueSpanLang('fr')).click();
          await this.waitForVisibleSelector(page, this.addFeatureValueInputLang(2));
          await this.setValue(page, this.addFeatureValueInputLang(2), productFeatures[i].customizedValueFr!);
        }
      }

      await this.waitForVisibleSelector(page, `${this.addFeatureButton}:not([disabled])`);
      await page.locator(this.addFeatureButton).click();
    }
  }

  /**
   * Delete all features
   * @param page {Page} Browser tab
   * @param productFeatures {ProductFeatures[]} Data to delete feature
   * @returns {Promise<void>}
   */
  async deleteFeatures(page: Page, productFeatures: ProductFeatures[]): Promise<void> {
    for (let i: number = 0; i < productFeatures.length; i++) {
      // Why tr:nth-child(2) : It's one-based selector and the first row is hidden ?
      await this.waitForSelectorAndClick(page, this.tableFeaturesBtnDelete(2));
      await this.waitForSelectorAndClick(page, this.confirmDeleteFeatureButton);
    }
  }

  /**
   * Click on "Manage Features"
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async clickonManageFeatures(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.manageFeaturesLink);
  }

  /**
   * Click on manage all files
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async clickOnManageAllFiles(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.manageAllFilesLink);
  }

  /**
   * Search file
   * @param page {Page} Browser tab
   * @param fileName {string} File name to search
   * @returns {Promise<string>}
   */
  async searchFile(page: Page, fileName: string): Promise<string> {
    await this.setValue(page, this.searchFileInput, fileName);
    await page.waitForTimeout(2000);

    return this.getTextContent(page, this.searchFileResult);
  }

  /**
   * Add new file
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on add file form
   * @returns {Promise<void>}
   */
  async addNewFile(page: Page, productData: ProductData): Promise<void> {
    for (let i: number = 0; i < productData.files.length; i++) {
      await this.waitForSelectorAndClick(page, this.addNewFileButton);

      await this.waitForVisibleSelector(page, this.createFileFrame);

      const newFileFrame: Frame | null = page.frame({name: 'modal-create-product-attachment-iframe'});
      expect(newFileFrame).to.not.eq(null);

      await this.setValue(newFileFrame!, this.fileNameInput, productData.files[i].fileName);
      await this.setValue(newFileFrame!, this.fileDescriptionInput, productData.files[i].description);
      await this.uploadFile(newFileFrame!, this.attachFileButton, productData.files[i].file);
      await newFileFrame!.locator(this.saveFileButton).click();
    }
  }

  /**
   * Delete all files
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to delete file
   * @returns {Promise<void>}
   */
  async deleteFiles(page: Page, productData: ProductData): Promise<void> {
    for (let i: number = 0; i < productData.files.length; i++) {
      await this.waitForSelectorAndClick(page, this.deleteFileIcon(i));
      await this.waitForSelectorAndClick(page, this.deleteFileConfirmButton);
    }
  }

  /**
   * Get no file attached message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getNoFileAttachedMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.noFileAttachedErrorAlert);
  }

  /**
   * Set condition
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set condition
   * @returns {Promise<void>}
   */
  async setCondition(page: Page, productData: ProductData): Promise<void> {
    await this.setChecked(page, this.displayCondition(productData.displayCondition ? 1 : 0));
    await this.selectByVisibleText(page, this.productConditionSelect, productData.condition);
  }

  /**
   * Add new customization
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to add customization
   * @returns {Promise<void>}
   */
  async addNewCustomizations(page: Page, productData: ProductData): Promise<void> {
    for (let i: number = 0; i < productData.customizations.length; i++) {
      await this.waitForSelectorAndClick(page, this.addNewCustomizationButton);

      await this.setValue(page, this.customizationNameInput(i), productData.customizations[i].label);
      await this.selectByVisibleText(page, this.customizationTypeSelect(i), productData.customizations[i].type);
      await this.setChecked(page, this.customizationRequiredButton(i, productData.customizations[i].required ? 1 : 0));
    }
  }

  /**
   * Delete all customizations
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to delete customizations
   * @returns {Promise<void>}
   */
  async deleteCustomizations(page: Page, productData: ProductData): Promise<void> {
    for (let i: number = 0; i < productData.customizations.length; i++) {
      await this.waitForSelectorAndClick(page, this.deleteCustomizationIcon(i));
      await this.waitForSelectorAndClick(page, this.confirmDeleteCustomizationButton);
    }
  }

  /**
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'condition':
        return page
          .locator(this.productConditionSelect)
          .evaluate((el: HTMLSelectElement) => el.value);
      case 'mpn':
        return this.getAttributeContent(page, this.productMPNInput, 'value');
      case 'reference':
        return this.getAttributeContent(page, this.productReferenceInput, 'value');
      case 'upc':
        return this.getAttributeContent(page, this.productUPCInput, 'value');
      case 'ean13':
        return this.getAttributeContent(page, this.productEAN13Input, 'value');
      case 'isbn':
        return this.getAttributeContent(page, this.productISBNInput, 'value');
      case 'show_condition':
        return (await this.isChecked(page, this.displayCondition(1))) ? '1' : '0';
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new DetailsTab();
