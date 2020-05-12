require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCategory extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Add new';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#category_name_1';
    this.displayed = id => `label[for='category_active_${id}']`;
    this.descriptionIframe = '#category_description_1_ifr';
    this.categoryCoverImage = '#category_cover_image';
    this.metaTitleInput = '#category_meta_title_1';
    this.metaDescriptionTextarea = '#category_meta_description_1';
    this.selectAllGroupAccessCheckbox = '.choice-table .table-bordered label .md-checkbox-control';
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
   * @param categoryData
   * @return {Promise<textContent>}
   */
  async createEditCategory(categoryData) {
    await this.setValue(this.nameInput, categoryData.name);
    await this.page.click(this.displayed(categoryData.displayed ? 1 : 0));
    await this.setValueOnTinymceInput(this.descriptionIframe, categoryData.description);
    await this.generateAndUploadImage(this.categoryCoverImage, `${categoryData.name}.jpg`);
    await this.setValue(this.metaTitleInput, categoryData.metaTitle);
    await this.setValue(this.metaDescriptionTextarea, categoryData.metaDescription);
    await this.page.click(this.selectAllGroupAccessCheckbox);
    // Save Category
    await this.clickAndWaitForNavigation(this.saveCategoryButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Edit home category
   * @param categoryData
   * @returns {Promise<string>}
   */
  async editHomeCategory(categoryData) {
    await this.setValue(this.rootCategoryNameInput, categoryData.name);
    await this.page.click(this.rootCategoryDisplayed(categoryData.displayed ? 1 : 0));
    await this.setValueOnTinymceInput(this.rootCategoryDescriptionIframe, categoryData.description);
    await this.generateAndUploadImage(this.rootCategoryCoverImage, `${categoryData.name}.jpg`);
    await this.setValue(this.rootCategoryMetaTitleInput, categoryData.metaTitle);
    await this.setValue(this.rootCategoryMetaDescriptionTextarea, categoryData.metaDescription);
    await this.page.click(this.selectAllGroupAccessCheckbox);
    // Save Category
    await this.clickAndWaitForNavigation(this.saveCategoryButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
