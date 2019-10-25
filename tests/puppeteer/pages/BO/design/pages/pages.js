require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Pages extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Pages';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    // Header links
    this.addNewPageCategoryLink = '#page-header-desc-configuration-add_cms_category[title=\'Add new page category\']';
    this.addNewPageLink = '#page-header-desc-configuration-add_cms_page[title=\'Add new page\']';

    // List of categories
    this.categoryGridPanel = '#cms_page_category_grid_panel';
    this.categoryGridTitle = `${this.categoryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#cms_page_category_grid';
    this.categoriesListTableRow = `${this.categoriesListForm} tbody tr:nth-child(%ROW)`;
    this.categoriesListTableColumn = `${this.categoriesListTableRow} td.column-%COLUMN`;
    this.categoriesListTableViewLink = `${this.categoriesListTableColumn} a[data-original-title='View']`;
    this.categoriesListTableToggleDropDown = `${this.categoriesListTableColumn} a[data-toggle='dropdown']`;
    this.categoriesListTableEditLink = `${this.categoriesListTableColumn} a[href*='edit']`;
    this.categoriesListTableDeleteLink = `${this.categoriesListTableColumn} a[data-method="DELETE"]`;
    this.categoriesListColumnValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-valid`;
    this.categoriesListColumnNotValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-not-valid`;

    this.backToListButton = `${this.categoryGridPanel} div.card-footer a`;
    // Filters in categories table
    this.categoryFilterInput = `${this.categoriesListForm} #cms_page_category_%FILTERBY`;
    this.categoryFilterSearchButton = `${this.categoriesListForm} button[name='cms_page_category[actions][search]']`;
    this.categoryfilterResetButton = `${this.categoriesListForm} button[name='cms_page_category[actions][reset]']`;
    // Bulk Actions
    this.categoriesSelectAllRowsLabel = `${this.categoriesListForm} .md-checkbox label`;
    this.categoriesBulkActionsToggleButton = `${this.categoriesListForm} button.dropdown-toggle`;
    this.categoriesBulkActionsEnableButton = `${this.categoriesListForm} 
    #cms_page_category_grid_bulk_action_enable_selection`;
    this.categoriesBulkActionsDisableButton = `${this.categoriesListForm} 
    #cms_page_category_grid_bulk_action_disable_selection`;
    this.categoriesBulkActionsDeleteButton = `${this.categoriesListForm} 
    #cms_page_category_grid_bulk_action_delete_bulk`;
    // List of pages
    this.pageGridPanel = '#cms_page_grid_panel';
    this.pageGridTitle = `${this.pageGridPanel} h3.card-header-title`;
    this.pagesListForm = '#cms_page_grid';
    this.pagesListTableRow = `${this.pagesListForm} tbody tr:nth-child(%ROW)`;
    this.pagesListTableColumn = `${this.pagesListTableRow} td.column-%COLUMN`;
    this.pageListTableToggleDropDown = `${this.pagesListTableColumn} a[data-toggle='dropdown']`;
    this.pagesListTableEditLink = `${this.pagesListTableColumn} a[href*='edit']`;
    this.pagesListTableDeleteLink = `${this.pagesListTableColumn} a[data-method="DELETE"]`;
    this.pagesListColumnValidIcon = `${this.pagesListTableColumn} i.grid-toggler-icon-valid`;
    this.pagesListColumnNotValidIcon = `${this.pagesListTableColumn} i.grid-toggler-icon-not-valid`;

    // Filters in pages table
    this.pageFilterInput = `${this.pagesListForm} #cms_page_%FILTERBY`;
    this.pageFilterSearchButton = `${this.pagesListForm} button[name='cms_page[actions][search]']`;
    this.pagefilterResetButton = `${this.pagesListForm} button[name='cms_page[actions][reset]']`;
    // Bulk Actions
    this.pagesSelectAllRowsLabel = `${this.pagesListForm} .md-checkbox label`;
    this.pagesBulkActionsToggleButton = `${this.pagesListForm} button.dropdown-toggle`;
    this.pagesBulkActionsEnableButton = `${this.pagesListForm} #cms_page_grid_bulk_action_enable_selection`;
    this.pagesBulkActionsDisableButton = `${this.pagesListForm} #cms_page_grid_bulk_action_disable_selection`;
    this.pagesBulkActionsDeleteButton = `${this.pagesListForm} #cms_page_grid_bulk_action_delete_bulk`;
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

  // Methods for categories
  /**
   * Filter list of categories
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCategories(filterType, filterBy, value = '') {
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
   * Get Value of column Displayed in Categories table
   * @param row, row in table
   * @param column, column to check
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValueCategory(row, column) {
    if (await this.elementVisible(
      this.categoriesListColumnValidIcon.replace('%ROW', row)
        .replace('%COLUMN', column), 100)) return true;
    return false;
  }

  /**
   * Quick edit toggle column value in Categories table
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValueCategory(row, column, valueWanted = true) {
    if (await this.getToggleColumnValueCategory(row, column) !== valueWanted) {
      this.page.click(this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
      if (valueWanted) {
        await this.page.waitForSelector(this.categoriesListColumnValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'));
      } else {
        await this.page.waitForSelector(this.categoriesListColumnNotValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'));
      }
      return true;
    }
    return false;
  }

  /**
   * Delete Category
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteCategory(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.categoriesListTableToggleDropDown
        .replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.categoriesListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.categoriesListTableDeleteLink
        .replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.dialogListener(),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable categories by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeCategoriesEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.categoriesSelectAllRowsLabel),
      this.page.waitForSelector(`${this.categoriesSelectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.categoriesBulkActionsToggleButton),
      this.page.waitForSelector(`${this.categoriesBulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(enable ? this.categoriesBulkActionsEnableButton : this.categoriesBulkActionsDisableButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Categories with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteCategoriesBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.click(this.categoriesSelectAllRowsLabel),
      this.page.waitForSelector(`${this.categoriesSelectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.categoriesBulkActionsToggleButton),
      this.page.waitForSelector(`${this.categoriesBulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.categoriesBulkActionsDeleteButton),
      this.dialogListener(),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Go to Edit Category page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.categoriesListTableToggleDropDown
        .replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.categoriesListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on edit
    this.clickAndWaitForNavigation(this.categoriesListTableEditLink
      .replace('%ROW', row).replace('%COLUMN', 'actions'));
  }

  // Methods for pages
  /**
   * Filter list of pages
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterPages(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.pageFilterInput.replace('%FILTERBY', filterBy), value.toString());
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
   * Get Value of column Displayed in Pages table
   * @param row, row in table
   * @param column, column to check
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValuePage(row, column) {
    if (await this.elementVisible(
      this.pagesListColumnValidIcon.replace('%ROW', row).replace('%COLUMN', column), 100)) return true;
    return false;
  }

  /**
   * Quick edit toggle column value in Pages table
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValuePage(row, column, valueWanted = true) {
    if (await this.getToggleColumnValuePage(row, column) !== valueWanted) {
      this.page.click(this.pagesListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
      if (valueWanted) {
        await this.page.waitForSelector(this.pagesListColumnValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'));
      } else {
        await this.page.waitForSelector(
          this.pagesListColumnNotValidIcon.replace('%ROW', 1).replace('%COLUMN', 'active'));
      }
      return true;
    }
    return false;
  }

  /**
   * Delete Page
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deletePage(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.pageListTableToggleDropDown
        .replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.pageListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.pagesListTableDeleteLink
        .replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.dialogListener(),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Pages with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deletePagesBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.click(this.pagesSelectAllRowsLabel),
      this.page.waitForSelector(`${this.pagesSelectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.pagesBulkActionsToggleButton),
      this.page.waitForSelector(`${this.pagesBulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.pagesBulkActionsDeleteButton),
      this.dialogListener(),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable pages by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changePagesEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.pagesSelectAllRowsLabel),
      this.page.waitForSelector(`${this.pagesSelectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.pagesBulkActionsToggleButton),
      this.page.waitForSelector(`${this.pagesBulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.pagesBulkActionsEnableButton
      : this.pagesBulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
