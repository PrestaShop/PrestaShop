require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddCategory extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Add new';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#category_name_1';
    this.displayed = id => `label[for='category_active_${id}']`;
    this.descriptionIframe = '#category_description_1_ifr';
    this.categoryCoverImage = '#category_cover_image';
    this.metaTitleInput = '#category_meta_title_1';
    this.metaDescriptionTextarea = '#category_meta_description_1';
    this.selectAllGroupAccessCheckbox = '.choice-table .table-bordered label';
    this.saveCategoryButton = 'div.card-footer button';
    // Selectors fo root category
    this.rootCategoryNameInput = '#root_category_name_1';
    this.rootCategoryDisplayed = id => `label[for='root_category_active_${id}']`;
    this.rootCategoryDescriptionIframe = '#root_category_description_1_ifr';
    this.rootCategoryCoverImage = '#root_category_cover_image';
    this.rootCategoryMetaTitleInput = '#root_category_meta_title_1';
    this.rootCategoryMetaDescriptionTextarea = '#root_category_meta_description_1';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit category
   * @param page
   * @param categoryData
   * @returns {Promise<string>}
   */
  async createEditCategory(page, categoryData) {
    await this.setValue(page, this.nameInput, categoryData.name);
    await page.click(this.displayed(categoryData.displayed ? 1 : 0));
    await this.setValueOnTinymceInput(page, this.descriptionIframe, categoryData.description);
    await this.uploadFile(page, this.categoryCoverImage, `${categoryData.name}.jpg`);
    await this.setValue(page, this.metaTitleInput, categoryData.metaTitle);
    await this.setValue(page, this.metaDescriptionTextarea, categoryData.metaDescription);
    await page.click(this.selectAllGroupAccessCheckbox);
    // Save Category
    await this.clickAndWaitForNavigation(page, this.saveCategoryButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Edit home category
   * @param page
   * @param categoryData
   * @returns {Promise<string>}
   */
  async editHomeCategory(page, categoryData) {
    await this.setValue(page, this.rootCategoryNameInput, categoryData.name);
    await page.click(this.rootCategoryDisplayed(categoryData.displayed ? 1 : 0));
    await this.setValueOnTinymceInput(page, this.rootCategoryDescriptionIframe, categoryData.description);
    await this.uploadFile(page, this.rootCategoryCoverImage, `${categoryData.name}.jpg`);
    await this.setValue(page, this.rootCategoryMetaTitleInput, categoryData.metaTitle);
    await this.setValue(page, this.rootCategoryMetaDescriptionTextarea, categoryData.metaDescription);
    await page.click(this.selectAllGroupAccessCheckbox);
    // Save Category
    await this.clickAndWaitForNavigation(page, this.saveCategoryButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddCategory();
