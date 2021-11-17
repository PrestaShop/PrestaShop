require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add image type page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddImageType extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add image type page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Image Settings > Add new • ';
    this.pageTitleEdit = 'Image Settings > Edit:';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.imageTypeForm = '#image_type_form';
    this.nameInput = '#name';
    this.widthInput = '#width';
    this.heightInput = '#height';
    this.productsToggle = toggle => `${this.imageTypeForm} #products_${toggle}`;
    this.categoriesToggle = toggle => `${this.imageTypeForm} #categories_${toggle}`;
    this.manufacturersToggle = toggle => `${this.imageTypeForm} #manufacturers_${toggle}`;
    this.suppliersToggle = toggle => `${this.imageTypeForm} #suppliers_${toggle}`;
    this.storesToggle = toggle => `${this.imageTypeForm} #stores_${toggle}`;
    this.saveButton = '#image_type_form_submit_btn';
  }

  /* Methods */

  /**
   * Fill image type form in create or edit page and save
   * @param page {Page} Browser tab
   * @param imageTypeData {ImageTypeData} Data to set on new/edit image type form
   * @return {Promise<string>}
   */
  async createEditImageType(page, imageTypeData) {
    await this.setValue(page, this.nameInput, imageTypeData.name);
    await this.setValue(page, this.widthInput, imageTypeData.width.toString());
    await this.setValue(page, this.heightInput, imageTypeData.height.toString());

    // Set status for image type
    await page.check(this.productsToggle(imageTypeData.productsStatus ? 'on' : 'off'));
    await page.check(this.categoriesToggle(imageTypeData.categoriesStatus ? 'on' : 'off'));
    await page.check(this.manufacturersToggle(imageTypeData.manufacturersStatus ? 'on' : 'off'));
    await page.check(this.suppliersToggle(imageTypeData.suppliersStatus ? 'on' : 'off'));
    await page.check(this.storesToggle(imageTypeData.storesStatus ? 'on' : 'off'));

    // Save image type
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddImageType();
