require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Product extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Products •';
    this.productDeletedSuccessfulMessage = 'Product successfully deleted.';
    this.productMultiDeletedSuccessfulMessage = 'Product(s) successfully deleted.';
    this.productDeactivatedSuccessfulMessage = 'Product successfully deactivated.';
    this.productActivatedSuccessfulMessage = 'Product successfully activated.';

    // Selectors
    // List of products
    this.productListForm = '#product_catalog_list';
    this.productTable = `${this.productListForm} table`;
    this.productRow = `${this.productTable} tbody tr`;
    this.productListfooterRow = `${this.productListForm} div.row:nth-of-type(3)`;
    this.productNumberBloc = `${this.productListfooterRow} label.col-form-label`;
    this.dropdownToggleButton = row => `${this.productRow}:nth-of-type(${row}) button.dropdown-toggle`;
    this.dropdownMenu = row => `${this.productRow}:nth-of-type(${row}) div.dropdown-menu`;
    this.dropdownMenuDeleteLink = row => `${this.dropdownMenu(row)} a.product-edit[onclick*='delete']`;
    this.productRowEditLink = row => `${this.productRow}:nth-of-type(${row}) a.tooltip-link.product-edit`;
    this.selectAllBulkCheckboxLabel = '#catalog-actions div.md-checkbox label';
    this.productBulkMenuButton = '#product_bulk_menu:not([disabled])';
    this.productBulkDropdownMenu = 'div.bulk-catalog div.dropdown-menu.show';
    this.productBulkDeleteLink = `${this.productBulkDropdownMenu} a[onclick*='delete_all']`;
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
    this.productsListTableColumnPriceTTC = row => `${this.productsListTableRow(row)} td:nth-child(8)`;
    this.productsListTableColumnQuantity = row => `${this.productsListTableRow(row)} td.product-sav-quantity`;
    this.productsListTableColumnStatus = row => `${this.productsListTableRow(row)} td:nth-child(10)`;
    this.productsListTableColumnStatusEnabled = row => `${this.productsListTableColumnStatus(row)} .action-enabled`;
    this.productsListTableColumnStatusDisabled = row => `${this.productsListTableColumnStatus(row)} .action-disabled`;
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
  }

  /*
  Methods
   */
  /**
   * Filter products Min - Max
   * @param min
   * @param max
   * @return {Promise<void>}
   */
  async filterIDProducts(min, max) {
    await this.page.type(this.productFilterIDMinInput, min.toString());
    await this.page.type(this.productFilterIDMaxInput, max.toString());
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Product ID
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductIDFromList(row) {
    return this.getNumberFromText(this.productsListTableColumnID(row));
  }

  /**
   * Get Product Name
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductNameFromList(row) {
    return this.getTextContent(this.productsListTableColumnName(row));
  }

  /**
   * Get Product Reference
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductReferenceFromList(row) {
    return this.getTextContent(this.productsListTableColumnReference(row));
  }

  /**
   * Get Product Category
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductCategoryFromList(row) {
    return this.getTextContent(this.productsListTableColumnCategory(row));
  }

  /**
   * Filter price Min - Max
   * @param min
   * @param max
   * @return {Promise<void>}
   */
  async filterPriceProducts(min, max) {
    await this.page.type(this.productFilterPriceMinInput, min.toString());
    await this.page.type(this.productFilterPriceMaxInput, max.toString());
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Product Price
   * @param {int} row
   * @param {boolean} withTaxes
   * @return Float
   */
  async getProductPriceFromList(row, withTaxes) {
    const selector = withTaxes ? this.productsListTableColumnPriceTTC : this.productsListTableColumnPrice;
    const text = await this.getTextContent(selector(row));
    const price = /\d+(\.\d+)?/g.exec(text).toString();
    return parseFloat(price);
  }

  /**
   * Filter Quantity Min - Max
   * @param min
   * @param max
   * @return {Promise<void>}
   */
  async filterQuantityProducts(min, max) {
    await this.page.type(this.productFilterQuantityMinInput, min.toString());
    await this.page.type(this.productFilterQuantityMaxInput, max.toString());
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Product Quantity
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductQuantityFromList(row) {
    return this.getNumberFromText(this.productsListTableColumnQuantity(row));
  }

  /**
   * Get Product Status
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductStatusFromList(row) {
    return this.getTextContent(this.productsListTableColumnStatus(row));
  }

  /**
   * Filter products
   * @param filterBy
   * @param value
   * @param filterType
   * @return {Promise<void>}
   */
  async filterProducts(filterBy, value = '', filterType = 'input') {
    switch (filterType) {
      case 'input':
        switch (filterBy) {
          case 'id_product':
            await this.filterIDProducts(value.min, value.max);
            break;
          case 'price':
            await this.filterPriceProducts(value.min, value.max);
            break;
          case 'sav_quantity':
            await this.filterQuantityProducts(value.min, value.max);
            break;
          default:
            await this.page.type(this.productFilterInput(filterBy), value);
        }
        break;
      case 'select':
        await this.selectByVisibleText(this.productFilterSelect(filterBy),
          value ? 'Active' : 'Inactive',
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Text Column
   * @param columnName
   * @param row
   * @return {Promise<float|string>}
   */
  async getTextColumn(columnName, row) {
    switch (columnName) {
      case 'id_product':
        return this.getProductIDFromList(row);
      case 'name':
        return this.getProductNameFromList(row);
      case 'reference':
        return this.getProductReferenceFromList(row);
      case 'name_category':
        return this.getProductCategoryFromList(row);
      case 'price':
        return this.getProductPriceFromList(row);
      case 'sav_quantity':
        return this.getProductQuantityFromList(row);
      case 'active':
        return this.getProductStatusFromList(row);
      default:
      // Do nothing
    }
    throw new Error(`${columnName} was not found as column`);
  }

  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfProductsFromList();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(column, i);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Get number of products displayed in list
   * @return integer
   */
  async getNumberOfProductsFromList() {
    const found = await this.elementVisible(this.paginationNextLink, 1000);
    // In case we filter products and there is only one page, link next from pagination does not appear
    if (!found) return (await this.page.$$(this.productRow)).length;

    const footerText = await this.getTextContent(this.productNumberBloc);
    const numberOfProduct = /\d+/g.exec(footerText.match(/out of ([0-9]+)/)).toString();
    return parseInt(numberOfProduct, 10);
  }

  /**
   * Get number of products displayed on the page
   * @return integer
   */
  async getNumberOfProductsOnPage() {
    return (await this.page.$$(this.productRow)).length;
  }

  /**
   * Reset input filters
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    await this.waitForVisibleSelector(this.filterSearchButton, 2000);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfProductsFromList();
  }

  /**
   * Filter by Category from Dropdown
   * @param value
   * @return {Promise<void>}
   */
  async filterProductsByCategory(value = 'home') {
    // Click and wait to be open
    await this.page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(`${this.filterByCategoriesButton}[aria-expanded='true']`);
    // Click on expand button
    await this.page.click(this.filterByCategoriesExpandButton);
    // Choose category to filter with
    const found = await this.page.evaluate(async (allCategoriesSelector, val) => {
      const allCategories = [...await document.querySelectorAll(allCategoriesSelector)];
      const category = await allCategories.find(el => el.textContent.includes(val));
      if (category === undefined) return false;
      await category.querySelector('input').click();
      return true;
    }, this.filterByCategoriesCategoryLabel, value);
    if (!found) throw new Error(`${value} not found as a category`);
    await this.page.waitForNavigation({waitUntil: 'networkidle0'});
  }

  /**
   * Reset DropDown Filter Category
   * @return {Promise<void>}
   */
  async resetFilterCategory() {
    // Click and wait to be open
    await this.page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(`${this.filterByCategoriesButton}[aria-expanded='true']`);
    await Promise.all([
      this.waitForVisibleSelector(`${this.filterByCategoriesButton}[aria-expanded='false']`),
      this.clickAndWaitForNavigation(this.filterByCategoriesUnselectButton),
    ]);
  }

  /**
   * GOTO form Add Product
   * @return {Promise<void>}
   */
  async goToAddProductPage() {
    await this.clickAndWaitForNavigation(this.addProductButton);
  }

  /**
   * GOTO edit product page from row
   * @param row
   * @returns {Promise<void>}
   */
  async goToEditProductPage(row) {
    await this.clickAndWaitForNavigation(this.productRowEditLink(row));
  }

  /**
   * Delete product with dropdown Menu
   * @param productData
   * @return {Promise<textContent>}
   */
  async deleteProduct(productData) {
    // Filter By reference first
    await this.filterProducts('reference', productData.reference);
    // Then delete first product and only product shown
    await Promise.all([
      this.waitForVisibleSelector(`${this.dropdownToggleButton(1)}[aria-expanded='true']`),
      this.page.click(this.dropdownToggleButton(1)),
    ]);
    await Promise.all([
      this.waitForVisibleSelector(this.catalogDeletionModalDialog),
      this.page.click(this.dropdownMenuDeleteLink(1)),
    ]);
    await this.clickAndWaitForNavigation(this.modalDialogDeleteNowButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete All products with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteAllProductsWithBulkActions() {
    // Then delete first product and only product shown
    await Promise.all([
      this.waitForVisibleSelector(this.productBulkMenuButton),
      this.page.click(this.selectAllBulkCheckboxLabel),
    ]);
    await Promise.all([
      this.waitForVisibleSelector(`${this.productBulkMenuButton}[aria-expanded='true']`),
      this.page.click(this.productBulkMenuButton),
    ]);
    await Promise.all([
      this.waitForVisibleSelector(this.catalogDeletionModalDialog),
      this.page.click(this.productBulkDeleteLink),
    ]);
    await this.clickAndWaitForNavigation(this.modalDialogDeleteNowButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get Value of column Displayed
   * @param row, row in table
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row) {
    return this.elementVisible(this.productsListTableColumnStatusEnabled(row), 100);
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, valueWanted = true) {
    await this.waitForVisibleSelector(this.productsListTableColumnStatus(row), 2000);
    const actualValue = await this.getToggleColumnValue(row);
    if (actualValue !== valueWanted) {
      await this.clickAndWaitForNavigation(this.productsListTableColumnStatus(row));
      return true;
    }
    return false;
  }

  /**
   * Go to product page
   * @param row
   * @returns {Promise<void>}
   */
  async goToProductPage(row = 1) {
    await this.waitForVisibleSelector(this.productsListTableColumnName(row));
    await this.clickAndWaitForNavigation(this.productsListTableColumnName(row));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 500) && i < 2) {
      await this.page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }
};
