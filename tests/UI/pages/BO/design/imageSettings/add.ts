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

  public readonly pageTitleEdit: string;

  private readonly imageTypeForm: string;

  private readonly nameInput: string;

  private readonly widthInput: string;

  private readonly heightInput: string;

  private readonly productsToggle: (toggle: string) => string;

  private readonly categoriesToggle: (toggle: string) => string;

  private readonly manufacturersToggle: (toggle: string) => string;

  private readonly suppliersToggle: (toggle: string) => string;

  private readonly storesToggle: (toggle: string) => string;

  private readonly saveButton: string;

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
    this.productsToggle = (toggle: string) => `${this.imageTypeForm} #products_${toggle}`;
    this.categoriesToggle = (toggle: string) => `${this.imageTypeForm} #categories_${toggle}`;
    this.manufacturersToggle = (toggle: string) => `${this.imageTypeForm} #manufacturers_${toggle}`;
    this.suppliersToggle = (toggle: string) => `${this.imageTypeForm} #suppliers_${toggle}`;
    this.storesToggle = (toggle: string) => `${this.imageTypeForm} #stores_${toggle}`;
    this.saveButton = '#image_type_form_submit_btn';
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
    await this.setChecked(page, this.productsToggle(imageTypeData.productsStatus ? 'on' : 'off'));
    await this.setChecked(page, this.categoriesToggle(imageTypeData.categoriesStatus ? 'on' : 'off'));
    await this.setChecked(page, this.manufacturersToggle(imageTypeData.manufacturersStatus ? 'on' : 'off'));
    await this.setChecked(page, this.suppliersToggle(imageTypeData.suppliersStatus ? 'on' : 'off'));
    await this.setChecked(page, this.storesToggle(imageTypeData.storesStatus ? 'on' : 'off'));

    // Save image type
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddImageType();
