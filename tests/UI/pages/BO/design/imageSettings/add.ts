import BOBasePage from '@pages/BO/BObasePage';

import type ImageTypeData from '@data/faker/imageType';

import type {Page} from 'playwright';

/**
 * Add image type page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddImageType extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: (name: string) => string;

  private readonly imageTypeForm: string;

  private readonly nameInput: string;

  private readonly widthInput: string;

  private readonly heightInput: string;

  private readonly productsToggle: (toggle: number) => string;

  private readonly categoriesToggle: (toggle: number) => string;

  private readonly manufacturersToggle: (toggle: number) => string;

  private readonly suppliersToggle: (toggle: number) => string;

  private readonly storesToggle: (toggle: number) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add image type page
   */
  constructor() {
    super();

    this.pageTitleCreate = `Add new • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (name: string) => `Edit: ${name} • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.imageTypeForm = 'form[name="image_type"]';
    this.nameInput = `${this.imageTypeForm} #image_type_name`;
    this.widthInput = `${this.imageTypeForm} #image_type_width`;
    this.heightInput = `${this.imageTypeForm} #image_type_height`;
    this.productsToggle = (toggle: number) => `${this.imageTypeForm} #image_type_products_${toggle}`;
    this.categoriesToggle = (toggle: number) => `${this.imageTypeForm} #image_type_categories_${toggle}`;
    this.manufacturersToggle = (toggle: number) => `${this.imageTypeForm} #image_type_manufacturers_${toggle}`;
    this.suppliersToggle = (toggle: number) => `${this.imageTypeForm} #image_type_suppliers_${toggle}`;
    this.storesToggle = (toggle: number) => `${this.imageTypeForm} #image_type_stores_${toggle}`;
    this.saveButton = `${this.imageTypeForm} #save-button`;
  }

  /* Methods */

  /**
   * Fill image type form in create or edit page and save
   * @param page {Page} Browser tab
   * @param imageTypeData {ImageTypeData} Data to set on new/edit image type form
   * @return {Promise<string>}
   */
  async createEditImageType(page: Page, imageTypeData: ImageTypeData): Promise<string> {
    await this.setValue(page, this.nameInput, imageTypeData.name);
    await this.setValue(page, this.widthInput, imageTypeData.width.toString());
    await this.setValue(page, this.heightInput, imageTypeData.height.toString());

    // Set status for image type
    await this.setChecked(page, this.productsToggle(imageTypeData.productsStatus ? 1 : 0));
    await this.setChecked(page, this.categoriesToggle(imageTypeData.categoriesStatus ? 1 : 0));
    await this.setChecked(page, this.manufacturersToggle(imageTypeData.manufacturersStatus ? 1 : 0));
    await this.setChecked(page, this.suppliersToggle(imageTypeData.suppliersStatus ? 1 : 0));
    await this.setChecked(page, this.storesToggle(imageTypeData.storesStatus ? 1 : 0));

    // Save image type
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddImageType();
