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
    this.productRow = `${this.productListForm} table tbody tr`;
    this.productListfooterRow = `${this.productListForm} div.row:nth-of-type(3)`;
    this.productNumberBloc = `${this.productListfooterRow} label.col-form-label`;
    this.dropdownToggleButton = `${this.productRow}:nth-of-type(%ROW) button.dropdown-toggle`;
    this.dropdownMenu = `${this.productRow}:nth-of-type(%ROW) div.dropdown-menu`;
    this.dropdownMenuDeleteLink = `${this.dropdownMenu} a.product-edit[onclick*='delete']`;
    this.selectAllBulkCheckboxLabel = '#catalog-actions div.md-checkbox label';
    this.productBulkMenuButton = '#product_bulk_menu:not([disabled])';
    this.productBulkDropdownMenu = 'div.bulk-catalog div.dropdown-menu.show';
    this.productBulkDeleteLink = `${this.productBulkDropdownMenu} a[onclick*='delete_all']`;
    // Filters input
    this.productFilterIDMinInput = `${this.productListForm} #filter_column_id_product_min`;
    this.productFilterIDMaxInput = `${this.productListForm} #filter_column_id_product_max`;
    this.productFilterInput = `${this.productListForm} input[name='filter_column_%FILTERBY']`;
    this.productFilterSelect = `${this.productListForm} select[name='filter_column_%FILTERBY']`;
    this.productFilterPriceMinInput = `${this.productListForm} #filter_column_price_min`;
    this.productFilterPriceMaxInput = `${this.productListForm} #filter_column_price_max`;
    this.productFilterQuantityMinInput = `${this.productListForm} #filter_column_sav_quantity_min`;
    this.productFilterQuantityMaxInput = `${this.productListForm} #filter_column_sav_quantity_max`;
    this.filterSearchButton = `${this.productListForm} button[name='products_filter_submit']`;
    this.filterResetButton = `${this.productListForm} button[name='products_filter_reset']`;
    // Products list
    this.productsListTableRow = `${this.productRow}:nth-child(%ROW)`;
    this.productsListTableColumnID = `${this.productsListTableRow}[data-product-id]`;
    this.productsListTableColumnName = `${this.productsListTableRow} td:nth-child(4) a`;
    this.productsListTableColumnReference = `${this.productsListTableRow} td:nth-child(5)`;
    this.productsListTableColumnCategory = `${this.productsListTableRow} td:nth-child(6)`;
    this.productsListTableColumnPrice = `${this.productsListTableRow} td:nth-child(7)`;
    this.productsListTableColumnQuantity = `${this.productsListTableRow} td.product-sav-quantity`;
    this.productsListTableColumnStatus = `${this.productsListTableRow} td:nth-child(10)`;
    this.productsListTableColumnStatusEnabled = `${this.productsListTableColumnStatus} .action-enabled`;
    this.productsListTableColumnStatusDisabled = `${this.productsListTableColumnStatus} .action-disabled`;
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
    return this.getNumberFromText(this.productsListTableColumnID.replace('%ROW', row));
  }

  /**
   * Get Product Name
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductNameFromList(row) {
    return this.getTextContent(this.productsListTableColumnName.replace('%ROW', row));
  }

  /**
   * Get Product Reference
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductReferenceFromList(row) {
    return this.getTextContent(this.productsListTableColumnReference.replace('%ROW', row));
  }

  /**
   * Get Product Category
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductCategoryFromList(row) {
    return this.getTextContent(this.productsListTableColumnCategory.replace('%ROW', row));
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
   * @param row
   * @return Float
   */
  async getProductPriceFromList(row) {
    const text = await this.getTextContent(this.productsListTableColumnPrice.replace('%ROW', row));
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
    return this.getNumberFromText(this.productsListTableColumnQuantity.replace('%ROW', row));
  }

  /**
   * Get Product Status
   * @param row
   * @return {Promise<textContent>}
   */
  async getProductStatusFromList(row) {
    return this.getTextContent(this.productsListTableColumnStatus.replace('%ROW', row));
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
          case 'product_id':
            await this.filterIDProducts(value.min, value.max);
            break;
          case 'price':
            await this.filterPriceProducts(value.min, value.max);
            break;
          case 'quantity':
            await this.filterQuantityProducts(value.min, value.max);
            break;
          default:
            await this.page.type(this.productFilterInput.replace('%FILTERBY', filterBy), value);
        }
        break;
      case 'select':
        await this.selectByVisibleText(this.productFilterSelect.replace('%FILTERBY', filterBy),
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
   * @return {Promise<void>, Float}
   */
  async getTextColumn(columnName, row) {
    switch (columnName) {
      case 'product_id':
        return this.getProductIDFromList(row);
      case 'name':
        return this.getProductNameFromList(row);
      case 'reference':
        return this.getProductReferenceFromList(row);
      case 'name_category':
        return this.getProductCategoryFromList(row);
      case 'price':
        return this.getProductPriceFromList(row);
      case 'quantity':
        return this.getProductQuantityFromList(row);
      case 'active':
        return this.getProductStatusFromList(row);
      default:
      // Do nothing
    }
    throw new Error(`${columnName} was not found as column`);
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
   * Reset input filters
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
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
    await this.page.waitForSelector(`${this.filterByCategoriesButton}[aria-expanded='true']`);
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
    await this.page.waitForSelector(`${this.filterByCategoriesButton}[aria-expanded='true']`);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(`${this.filterByCategoriesButton}[aria-expanded='false']`),
      this.page.click(this.filterByCategoriesUnselectButton),
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
   * Delete product with dropdown Menu
   * @param productData
   * @return {Promise<textContent>}
   */
  async deleteProduct(productData) {
    // Filter By reference first
    await this.filterProducts('reference', productData.reference);
    // Then delete first product and only product shown
    await Promise.all([
      this.page.waitForSelector(`${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', '1')),
      this.page.click(this.dropdownToggleButton.replace('%ROW', '1')),
    ]);
    await Promise.all([
      this.page.waitForSelector(this.catalogDeletionModalDialog, {visible: true}),
      this.page.click(this.dropdownMenuDeleteLink.replace('%ROW', '1')),
    ]);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
      this.page.click(this.modalDialogDeleteNowButton),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete All products with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteAllProductsWithBulkActions() {
    // Then delete first product and only product shown
    await Promise.all([
      this.page.waitForSelector(this.productBulkMenuButton, {visible: true}),
      this.page.click(this.selectAllBulkCheckboxLabel.replace('%ROW', '1')),
    ]);
    await Promise.all([
      this.page.waitForSelector(`${this.productBulkMenuButton}[aria-expanded='true']`, {visible: true}),
      this.page.click(this.productBulkMenuButton.replace('%ROW', '1')),
    ]);
    await Promise.all([
      this.page.waitForSelector(this.catalogDeletionModalDialog, {visible: true}),
      this.page.click(this.productBulkDeleteLink.replace('%ROW', '1')),
    ]);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
      this.page.click(this.modalDialogDeleteNowButton),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get Value of column Displayed
   * @param row, row in table
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row) {
    return this.elementVisible(
      this.productsListTableColumnStatusEnabled.replace('%ROW', row),
      100,
    );
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      this.page.click(this.productsListTableColumnStatus.replace('%ROW', row));
      if (valueWanted) {
        await this.page.waitForSelector(this.productsListTableColumnStatusEnabled.replace('%ROW', row));
      } else {
        await this.page.waitForSelector(
          this.productsListTableColumnStatusDisabled.replace('%ROW', row),
        );
      }
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
    await Promise.all([
      this.page.waitForSelector(this.productsListTableColumnName.replace('%ROW', row), {visible: true}),
      this.page.click(this.productsListTableColumnName.replace('%ROW', row)),
    ]);
  }
};
