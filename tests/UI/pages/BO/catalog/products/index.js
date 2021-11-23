require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Products page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Products extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on products page
   */
  constructor() {
    super();

    this.pageTitle = 'Products •';
    this.productDeletedSuccessfulMessage = 'Product successfully deleted.';
    this.productMultiDeletedSuccessfulMessage = 'Product(s) successfully deleted.';
    this.productDeactivatedSuccessfulMessage = 'Product successfully deactivated.';
    this.productActivatedSuccessfulMessage = 'Product successfully activated.';
    this.productMultiActivatedSuccessfulMessage = 'Product(s) successfully activated.';
    this.productMultiDeactivatedSuccessfulMessage = 'Product(s) successfully deactivated.';

    // Selectors
    // List of products
    this.productListForm = '#product_catalog_list';
    this.productTable = `${this.productListForm} table`;
    this.productRow = `${this.productTable} tbody tr`;
    this.productListfooterRow = `${this.productListForm} div.pagination-block`;
    this.productNumberBloc = `${this.productListfooterRow} label.col-form-label`;
    this.dropdownToggleButton = row => `${this.productRow}:nth-of-type(${row}) button.dropdown-toggle`;
    this.dropdownMenu = row => `${this.productRow}:nth-of-type(${row}) div.dropdown-menu`;
    this.dropdownMenuDeleteLink = row => `${this.dropdownMenu(row)} a.product-edit[onclick*='delete']`;
    this.dropdownMenuPreviewLink = row => `${this.dropdownMenu(row)} a.product-edit:not([onclick])`;
    this.dropdownMenuDuplicateLink = row => `${this.dropdownMenu(row)} a.product-edit[onclick*='duplicate']`;
    this.productRowEditLink = row => `${this.productRow}:nth-of-type(${row}) a.tooltip-link.product-edit`;
    this.selectAllBulkCheckboxLabel = `${this.productListForm} .column-filters .md-checkbox label`;
    this.productBulkMenuButton = '#product_bulk_menu:not([disabled])';
    this.productBulkMenuButtonState = state => `${this.productBulkMenuButton}[aria-expanded='${state}']`;
    this.productBulkDropdownMenu = 'div.bulk-catalog div.dropdown-menu.show';
    this.productBulkDeleteLink = `${this.productBulkDropdownMenu} a[onclick*='delete_all']`;
    this.productBulkEnableLink = `${this.productBulkDropdownMenu} a[onclick*='activate_all']`;
    this.productBulkDisableLink = `${this.productBulkDropdownMenu} a[onclick*='deactivate_all']`;

    // Filters input
    this.productFilterIDMinInput = `${this.productListForm} #filter_column_id_product_min`;
    this.productFilterIDMaxInput = `${this.productListForm} #filter_column_id_product_max`;
    this.productFilterInput = filterBy => `${this.productListForm} input[name='filter_column_${filterBy}']`;
    this.productFilterSelect = filterBy => `${this.productListForm} select[name='filter_column_${filterBy}']`;
    this.productFilterPriceMinInput = `${this.productListForm} #filter_column_price_min`;
    this.productFilterPriceMaxInput = `${this.productListForm} #filter_column_price_max`;
    this.productFilterQuantityMinInput = `${this.productListForm} #filter_column_sav_quantity_min`;
    this.productFilterQuantityMaxInput = `${this.productListForm} #filter_column_sav_quantity_max`;
    this.filterSearchButton = `${this.productListForm} button[name='products_filter_submit']`;
    this.filterResetButton = `${this.productListForm} button[name='products_filter_reset']`;

    // Products list
    this.productsListTableRow = row => `${this.productRow}:nth-child(${row})`;
    this.productsListTableColumnID = row => `${this.productsListTableRow(row)}[data-product-id]`;
    this.productsListTableColumnName = row => `${this.productsListTableRow(row)} td:nth-child(4) a`;
    this.productsListTableColumnReference = row => `${this.productsListTableRow(row)} td:nth-child(5)`;
    this.productsListTableColumnCategory = row => `${this.productsListTableRow(row)} td:nth-child(6)`;
    this.productsListTableColumnPrice = row => `${this.productsListTableRow(row)} td:nth-child(7)`;
    this.productsListTableColumnPriceATI = row => `${this.productsListTableRow(row)} td:nth-child(8)`;
    this.productsListTableColumnQuantity = row => `${this.productsListTableRow(row)} td.product-sav-quantity`;
    this.productsListTableColumnStatus = row => `${this.productsListTableRow(row)} td:nth-child(10) .ps-switch`;
    this.productsListTableColumnStatusInput = row => `${this.productsListTableColumnStatus(row)} input`;

    // Filter Category
    this.treeCategoriesBloc = '#tree-categories';
    this.filterByCategoriesButton = '#product_catalog_category_tree_filter button';
    this.filterByCategoriesExpandButton = `${this.treeCategoriesBloc} a#product_catalog_category_tree_filter_expand`;
    this.filterByCategoriesUnselectButton = `${this.treeCategoriesBloc} a#product_catalog_category_tree_filter_reset`;
    this.filterByCategoriesCategoryLabel = `${this.treeCategoriesBloc} label.category-label`;

    // HEADER buttons
    this.addProductButton = '#page-header-desc-configuration-add';

    // pagination
    this.paginationNextLink = '.page-item.next:not(.disabled) #pagination_next_url';

    // Modal Dialog
    this.catalogDeletionModalDialog = '#catalog_deletion_modal div.modal-dialog';
    this.modalDialogDeleteNowButton = `${this.catalogDeletionModalDialog} button[value='confirm']`;

    // Sort Selectors
    this.tableHead = `${this.productTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.productListForm} .col-form-label`;
    this.paginationNextLink = `${this.productListForm} #pagination_next_url`;
    this.paginationPreviousLink = `${this.productListForm} [aria-label='Previous']`;
  }

  /*
  Methods
   */
  /**
   * Filter products Min - Max
   * @param page {Page} Browser tab
   * @param idMin {number} Value of id min to set on filter input
   * @param idMax {number} Value of id max to set on filter input
   * @return {Promise<void>}
   */
  async filterIDProducts(page, idMin, idMax) {
    await page.type(this.productFilterIDMinInput, idMin.toString());
    await page.type(this.productFilterIDMaxInput, idMax.toString());
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get Product ID
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductIDFromList(page, row) {
    return this.getNumberFromText(page, this.productsListTableColumnID(row));
  }

  /**
   * Get Product Name
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductNameFromList(page, row) {
    return this.getTextContent(page, this.productsListTableColumnName(row));
  }

  /**
   * Get Product Reference
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductReferenceFromList(page, row) {
    return this.getTextContent(page, this.productsListTableColumnReference(row));
  }

  /**
   * Get Product Category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductCategoryFromList(page, row) {
    return this.getTextContent(page, this.productsListTableColumnCategory(row));
  }

  /**
   * Filter price Min - Max
   * @param page {Page} Browser tab
   * @param priceMin {number} Value of min price to set on filter input
   * @param priceMax {number} Value of max price to set on filter input
   * @return {Promise<void>}
   */
  async filterPriceProducts(page, priceMin, priceMax) {
    await page.type(this.productFilterPriceMinInput, priceMin.toString());
    await page.type(this.productFilterPriceMaxInput, priceMax.toString());
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get Product Price
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param withTaxes {boolean} True if we need to get product price with tax, false if not
   * @returns {Promise<number>}
   */
  async getProductPriceFromList(page, row, withTaxes) {
    const selector = withTaxes ? this.productsListTableColumnPriceATI : this.productsListTableColumnPrice;
    const text = await this.getTextContent(page, selector(row));
    const price = /\d+(\.\d+)?/g.exec(text).toString();

    return parseFloat(price);
  }

  /**
   * Filter Quantity Min - Max
   * @param page {Page} Browser tab
   * @param quantityMin {number} Value of quantity min to set on input
   * @param quantityMax {number} Value of quantity max to set on input
   * @return {Promise<void>}
   */
  async filterQuantityProducts(page, quantityMin, quantityMax) {
    await page.type(this.productFilterQuantityMinInput, quantityMin.toString());
    await page.type(this.productFilterQuantityMaxInput, quantityMax.toString());
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get Product Quantity
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductQuantityFromList(page, row) {
    return this.getNumberFromText(page, this.productsListTableColumnQuantity(row));
  }

  /**
   * Get Product Status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async getProductStatusFromList(page, row) {
    const inputValue = await this.getAttributeContent(
      page,
      `${this.productsListTableColumnStatusInput(row)}[checked]`,
      'value',
    );

    return inputValue !== '0';
  }

  /**
   * Filter products
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {{min: number, max:number}|string} Value to put on filter
   * @param filterType {string} Input or select to choose method of filter
   * @return {Promise<void>}
   */
  async filterProducts(page, filterBy, value = '', filterType = 'input') {
    switch (filterType) {
      case 'input':
        switch (filterBy) {
          case 'id_product':
            await this.filterIDProducts(page, value.min, value.max);
            break;
          case 'price':
            await this.filterPriceProducts(page, value.min, value.max);
            break;
          case 'sav_quantity':
            await this.filterQuantityProducts(page, value.min, value.max);
            break;
          default:
            await page.type(this.productFilterInput(filterBy), value);
        }
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.productFilterSelect(filterBy),
          value ? 'Active' : 'Inactive',
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get text content
   * @param row {number} Row on table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page, columnName, row) {
    switch (columnName) {
      case 'id_product':
        return this.getProductIDFromList(page, row);
      case 'name':
        return this.getProductNameFromList(page, row);
      case 'reference':
        return this.getProductReferenceFromList(page, row);
      case 'name_category':
        return this.getProductCategoryFromList(page, row);
      case 'price':
        return this.getProductPriceFromList(page, row, false);
      case 'sav_quantity':
        return this.getProductQuantityFromList(page, row);
      case 'active':
        return this.getProductStatusFromList(page, row);
      default:
      // Do nothing
    }
    throw new Error(`${columnName} was not found as column`);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows text content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfProductsFromList(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, column, i);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Get number of products displayed in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsFromList(page) {
    const found = await this.elementVisible(page, this.paginationNextLink, 1000);

    // In case we filter products and there is only one page, link next from pagination does not appear
    if (!found) {
      return (await page.$$(this.productRow)).length;
    }

    const footerText = await this.getTextContent(page, this.productNumberBloc);
    const numberOfProduct = /\d+/g.exec(footerText.match(/out of ([0-9]+)/)).toString();

    return parseInt(numberOfProduct, 10);
  }

  /**
   * Get number of products displayed on the page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsOnPage(page) {
    return (await page.$$(this.productRow)).length;
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);

    return this.getNumberOfProductsFromList(page);
  }

  /**
   * Filter by Category from Dropdown
   * @param page {Page} Browser tab
   * @param categoryName {string} Value of category name to set on filter input
   * @return {Promise<void>}
   */
  async filterProductsByCategory(page, categoryName = 'home') {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Click on expand button
    await page.click(this.filterByCategoriesExpandButton);

    // Choose category to filter with
    const args = {allCategoriesSelector: this.filterByCategoriesCategoryLabel, val: categoryName};
    const found = await page.evaluate(async (args) => {
      /* eslint-env browser */
      const allCategories = [...await document.querySelectorAll(args.allCategoriesSelector)];
      const category = await allCategories.find(el => el.textContent.includes(args.val));

      if (category === undefined) {
        return false;
      }
      await category.querySelector('input').click();
      return true;
    }, args);

    if (!found) {
      throw new Error(`${categoryName} not found as a category`);
    }
    await page.waitForNavigation();
  }

  /**
   * Reset dropDown Filter Category
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilterCategory(page) {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Unselect all categories
    await this.clickAndWaitForNavigation(page, this.filterByCategoriesUnselectButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='false']`);
  }

  /**
   * Go to form Add Product
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddProductPage(page) {
    await this.clickAndWaitForNavigation(page, this.addProductButton);
  }

  /**
   * GOTO edit product page from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditProductPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.productRowEditLink(row));
  }

  /**
   * Open row dropdown for a product
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async openProductDropdown(page, row) {
    await Promise.all([
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
      page.click(this.dropdownToggleButton(row)),
    ]);
  }

  /**
   * Preview product from list
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<Page>}
   */
  async previewProduct(page, row) {
    // Open dropdown
    await this.openProductDropdown(page, row);

    // Open product in a new tab
    return this.openLinkWithTargetBlank(page, this.dropdownMenuPreviewLink(row));
  }

  /**
   * Duplicate product
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async duplicateProduct(page, row) {
    // Open dropdown
    await this.openProductDropdown(page, row);

    // Duplicate product and go to add product page
    await this.clickAndWaitForNavigation(page, this.dropdownMenuDuplicateLink(row));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete product with dropdown Menu
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set to filter product
   * @returns {Promise<string>}
   */
  async deleteProduct(page, productData) {
    // Filter By reference first
    await this.filterProducts(page, 'reference', productData.reference);

    // Then delete first product and only product shown
    await Promise.all([
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(1)}[aria-expanded='true']`),
      page.click(this.dropdownToggleButton(1)),
    ]);

    await Promise.all([
      this.waitForVisibleSelector(page, this.catalogDeletionModalDialog),
      page.click(this.dropdownMenuDeleteLink(1)),
    ]);

    await this.clickAndWaitForNavigation(page, this.modalDialogDeleteNowButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Select all products
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async selectAllProducts(page) {
    await Promise.all([
      this.waitForVisibleSelector(page, this.productBulkMenuButton),
      page.$eval(this.selectAllBulkCheckboxLabel, el => el.click()),
    ]);
  }

  /**
   * Delete all products with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteAllProductsWithBulkActions(page) {
    await this.selectAllProducts(page);

    await Promise.all([
      this.waitForVisibleSelector(page, this.productBulkMenuButtonState('true')),
      page.click(this.productBulkMenuButton),
    ]);

    await Promise.all([
      this.waitForVisibleSelector(page, this.catalogDeletionModalDialog),
      page.click(this.productBulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.modalDialogDeleteNowButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Bulk set status
   * @param page {Page} Browser tab
   * @param status {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<string>}
   */
  async bulkSetStatus(page, status) {
    await this.selectAllProducts(page);

    await Promise.all([
      this.waitForVisibleSelector(page, this.productBulkMenuButtonState('true')),
      page.click(this.productBulkMenuButton),
    ]);

    await this.clickAndWaitForNavigation(page, status ? this.productBulkEnableLink : this.productBulkDisableLink);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable product status, false if not
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setProductStatus(page, row, valueWanted = true) {
    const actualValue = await this.getProductStatusFromList(page, row);

    if (actualValue !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.productsListTableColumnStatus(row));
      return true;
    }

    return false;
  }

  /**
   * Go to product page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToProductPage(page, row = 1) {
    await this.waitForVisibleSelector(page, this.productsListTableColumnName(row));
    await this.clickAndWaitForNavigation(page, this.productsListTableColumnName(row));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 2000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

module.exports = new Products();
