require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddImageType extends BOBasePage {
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
    this.productsToggle = toggle => `${this.imageTypeForm} label[for='products_${toggle}']`;
    this.categoriesToggle = toggle => `${this.imageTypeForm} label[for='categories_${toggle}']`;
    this.manufacturersToggle = toggle => `${this.imageTypeForm} label[for='manufacturers_${toggle}']`;
    this.suppliersToggle = toggle => `${this.imageTypeForm} label[for='suppliers_${toggle}']`;
    this.storesToggle = toggle => `${this.imageTypeForm} label[for='stores_${toggle}']`;
    this.saveButton = '#image_type_form_submit_btn';
  }

  /* Methods */

  /**
   * Fill image type form in create or edit page and save
   * @param page
   * @param imageTypeData
   * @return {Promise<string>}
   */
  async createEditImageType(page, imageTypeData) {
    await this.setValue(page, this.nameInput, imageTypeData.name);
    await this.setValue(page, this.widthInput, imageTypeData.width.toString());
    await this.setValue(page, this.heightInput, imageTypeData.height.toString());

    // Set status for image type
    await page.click(this.productsToggle(imageTypeData.productsStatus ? 'on' : 'off'));
    await page.click(this.categoriesToggle(imageTypeData.categoriesStatus ? 'on' : 'off'));
    await page.click(this.manufacturersToggle(imageTypeData.manufacturersStatus ? 'on' : 'off'));
    await page.click(this.suppliersToggle(imageTypeData.suppliersStatus ? 'on' : 'off'));
    await page.click(this.storesToggle(imageTypeData.storesStatus ? 'on' : 'off'));

    // Save image type
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddImageType();
