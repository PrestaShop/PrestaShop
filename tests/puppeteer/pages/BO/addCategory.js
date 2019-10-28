require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCategory extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Add new';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#category_name_1';
    this.displayed = 'label[for=\'category_active_%ID\']';
    this.descriptionIframe = '#category_description_1_ifr';
    this.categoryCoverImage = '#category_cover_image';
    this.metaTitleInput = '#category_meta_title_1';
    this.metaDescriptionTextarea = '#category_meta_description_1';
    this.selectAllGroupAccessCheckbox = '.choice-table .table-bordered label .md-checkbox-control';
    this.saveCategoryButton = 'div.card-footer button';
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
    if (categoryData.displayed) await this.page.click(this.displayed.replace('%ID', '1'));
    else await this.page.click(this.displayed.replace('%ID', '0'));
    await this.setValueOnTinymceInput(this.descriptionIframe, categoryData.description);
    await this.generateAndUploadImage(this.categoryCoverImage, `${categoryData.name}.jpg`);
    await this.setValue(this.metaTitleInput, categoryData.metaTitle);
    await this.setValue(this.metaDescriptionTextarea, categoryData.metaDescription);
    await this.page.click(this.selectAllGroupAccessCheckbox);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
      this.page.click(this.saveCategoryButton),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
