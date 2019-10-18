require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Pages extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Pages';
    // Header links
    this.addNewPageCategoryLink = '#page-header-desc-configuration-add_cms_category[title=\'Add new page category\']';
    this.addNewPageLink = '#page-header-desc-configuration-add_cms_page[title=\'Add new page\']';

    // List of categories
    this.categpryGridPanel = '#cms_page_category_grid_panel';
    this.categoryGridTitle = `${this.categpryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#cms_page_category_grid';
    this.categoriesListTableRow = `${this.categoriesListForm} tbody tr:nth-child(%ROW)`;
    this.categoriesListTableColumn = `${this.categoriesListTableRow} td.column-%COLUMN`;
    this.categoriesListTableViewLink = `${this.categoriesListTableColumn} a[data-original-title='View']`;
    this.categoryListTableToggleDropDown = `${this.categoriesListTableColumn} a[data-toggle='dropdown']`;
    this.categoryListTableEditLink = `${this.categoriesListTableColumn} a[href*='edit']`;
    this.categoryListTableDeleteLink = `${this.categoriesListTableColumn} a[data-method="DELETE"]`;

    this.backToListButton = `${this.categpryGridPanel} div.card-footer a`;
    // Filters in categories table
    this.categoryFilterInput = `${this.categoriesListForm} #cms_page_category_%FILTERBY`;
    this.categoryFilterSearchButton = `${this.categoriesListForm} button[name='cms_page_category[actions][search]']`;
    this.categoryfilterResetButton = `${this.categoriesListForm} button[name='cms_page_category[actions][reset]']`;
    // List of pages
    this.pagesGridPanel = '#cms_page_grid_panel';
    this.pagesGridTitle = `${this.pagesGridPanel} h3.card-header-title`;
    this.pagesListForm = '#cms_page_grid';
    this.pagesListTableRow = `${this.pagesListForm} tbody tr:nth-child(%ROW)`;
    this.pagesListTableColumn = `${this.pagesListTableRow} td.column-%COLUMN`;
    this.pageListTableToggleDropDown = `${this.pagesListTableColumn} a[data-toggle='dropdown']`;
    this.pagesListTableEditLink = `${this.pagesListTableColumn} a[href*='edit']`;
    this.pagesListTableDeleteLink = `${this.pagesListTableColumn} a[data-method="DELETE"]`;
    // Filters in pages table
    this.pageFilterInput = `${this.pagesListForm} #cms_page_%FILTERBY`;
    this.pageFilterSearchButton = `${this.pagesListForm} button[name='cms_page[actions][search]']`;
    this.pagefilterResetButton = `${this.pagesListForm} button[name='cms_page[actions][reset]']`;
  }

  /*
 Methods
  */
  /**
   * Reset input filters
   * @return {Promise<void>}
   */
  async resetFilter(selector) {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(selector),
    ]);
  }

  /**
   * Filter list of page categories
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterPageCategories(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.categoryFilterInput.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.categoryFilterInput.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.categoryFilterSearchButton),
    ]);
  }

  /**
   * View Page categories in list
   * @param row, row in table
   * @return {Promise<void>}
   */
  async viewCategory(row) {
    if (await this.elementVisible(
      this.categoriesListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions'), 100)) {
      await Promise.all([
        this.page.click(this.categoriesListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
        this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      ]);
    } else {
      await Promise.all([
        this.page.click(`${this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', 'name')} a`),
        this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      ]);
    }
  }

  /**
   * Filter list of page categories
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterPages(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.pageFilterInput.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.pageFilterInput.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.pageFilterSearchButton),
    ]);
  }

  /**
   * Go to Edit Page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditPagePage(row) {
    // Click on edit
    await Promise.all([
      this.page.click(this.pagesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * Go to Edit Category page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.categoryListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.categoryListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on edit
    await Promise.all([
      this.page.click(this.categoryListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * Delete Page
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deletePage(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.pageListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.pageListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.pagesListTableDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.dialogListener(),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete Category
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteCategory(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.categoryListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.categoryListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.categoryListTableDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.dialogListener(),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
