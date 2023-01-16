require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Products V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Products extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on products V2 page
   */
  constructor() {
    super();

    this.pageTitle = 'Products';
    this.alertDangerIDFilterValue = 'ID: Maximum value must be higher than minimum value.';
    this.alertDangerPriceFilterValue = 'Price (tax excl.): Maximum value must be higher than minimum value.';
    this.alertDangerQuantityFilterValue = 'Quantity: Maximum value must be higher than minimum value.';

    this.standardProductDescription = 'A physical product that needs to be shipped.';
    this.productWithCombinationsDescription = 'A product with different variations (size, color, etc.) from which '
      + 'customers can choose.';
    this.virtualProductDescription = 'An intangible product that doesn\'t require shipping. You can also add a '
      + 'downloadable file.';

    this.packOfProductsDescription = ' A collection of products from your catalog.';

    // Header selectors
    this.newProductButton = '#page-header-desc-configuration-add';

    // Products page selectors
    this.addNewProductButton = '#create_product_create';
    this.productGridPanel = '#product_grid_panel';
    this.productGridHeader = `${this.productGridPanel} div.js-grid-header`;
    this.headerTitle = `${this.productGridHeader} .card-header-title`;
    this.productGrid = '#product_grid';

    // Filter by categories
    this.filterByCategoryBlock = `${this.productGrid} form.d-inline-block`;
    this.filterByCategoriesButton = `${this.filterByCategoryBlock} div button.dropdown-toggle`;
    this.filterByCategoriesExpandButton = `${this.filterByCategoryBlock} div button.category_tree_filter_expand`;
    this.filterByCategoriesUnselectButton = `${this.filterByCategoryBlock} div button.category_tree_filter_reset.btn`;
    this.filterByCategoriesLabel = '#category_filter ul.category-tree li div.category-label';
    this.clearFilterButton = `${this.filterByCategoryBlock} button.btn-link.category_tree_filter_reset`;

    // Bulk actions selectors
    this.productBulkMenuButton = `${this.productGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDropDownMenu = 'div.dropdown-menu.show';
    this.bulkActionsdeleteSelectionLink = '#product_grid_bulk_action_bulk_delete_ajax';

    // Products table selectors
    this.productGridTable = '#product_grid_table';
    this.productTableFilterLine = `${this.productGridTable} tr.column-filters`;
    this.filterSearchButton = `${this.productTableFilterLine} button.grid-search-button`;
    this.filterResetButton = `${this.productTableFilterLine} button.js-reset-search`;
    this.selectAllProductsCheckbox = `${this.productTableFilterLine} td[data-type=bulk_action] div.md-checkbox`;

    // Filters input
    this.productFilterIDMinInput = '#product_id_product_min_field';
    this.productFilterIDMaxInput = '#product_id_product_max_field';
    this.productFilterNameInput = '#product_name';
    this.productFilterReferenceInput = '#product_reference';
    this.productFilterCategoryInput = '#product_category';
    this.productFilterPriceMinInput = '#product_final_price_tax_excluded_min_field';
    this.productFilterPriceMaxInput = '#product_final_price_tax_excluded_max_field';
    this.productFilterQuantityMinInput = '#product_quantity_min_field';
    this.productFilterQuantityMaxInput = '#product_quantity_max_field';
    this.productFilterSelectStatus = '#product_active';
    this.productFilterPositionInput = '#product_position';

    // Products list
    this.productRow = `${this.productGridTable} tbody tr`;
    this.productEmptyRow = `${this.productRow}.empty_row`;
    this.productsListTableRow = (row) => `${this.productRow}:nth-child(${row})`;
    this.productsListTableColumnID = (row) => `${this.productsListTableRow(row)} td.column-id_product`;
    this.productsListTableColumnName = (row) => `${this.productsListTableRow(row)} td.column-name a`;
    this.productsListTableColumnReference = (row) => `${this.productsListTableRow(row)} td.column-reference`;
    this.productsListTableColumnCategory = (row) => `${this.productsListTableRow(row)} td.column-category`;
    this.productsListTableColumnPriceTExc = (row) => `${this.productsListTableRow(row)} `
      + 'td.column-final_price_tax_excluded a';
    this.productsListTableColumnPriceATI = (row) => `${this.productsListTableRow(row)} `
      + 'td.column-price_tax_included';
    this.productsListTableColumnQuantity = (row) => `${this.productsListTableRow(row)} td.column-quantity a`;
    this.productsListTableColumnStatus = (row) => `${this.productsListTableRow(row)} td.column-active input`;
    this.productsListTableColumnPosition = (row) => `${this.productsListTableRow(row)} td.column-position`;
    this.productListTableDropDownList = (row) => `${this.productsListTableRow(row)} td.column-actions `
      + 'a.dropdown-toggle';
    this.productListTableDeleteButton = (row) => `${this.productsListTableRow(row)}`
      + ' td.column-actions a.grid-delete-row-link';

    // Modal create product selectors
    this.modalCreateProduct = '#modal-create-product';
    this.modalCreateProductLoader = `${this.modalCreateProduct} div.modal-iframe-loader`;
    this.productTypeChoices = '#create_product div.product-type-choices';
    this.productTypedescription = '#create_product div.product-type-description';
    this.productType = (type) => `${this.productTypeChoices} button.product-type-choice[data-value=${type}]`;

    // Modal dialog
    this.modalDialog = '#product-grid-confirm-modal .modal-dialog';
    this.modalDialogFooter = `${this.modalDialog} div.modal-footer`;
    this.modalDialogDeleteButton = `${this.modalDialogFooter} button.btn-confirm-submit`;

    // Modal delete products selectors
    this.modalBulkDeleteProducts = '#product-ajax-bulk_delete_ajax-confirm-modal';
    this.modalBulkdeleteProductsBody = `${this.modalBulkDeleteProducts} div.modal-body`;
    this.modalBulkdeleteProductsFooter = `${this.modalBulkDeleteProducts} div.modal-footer`;
    this.modalDialogBulkDeleteButton = `${this.modalBulkdeleteProductsFooter} button.btn-confirm-submit`;
    this.modalBulkDeleteProductsProgress = '#product-ajax-bulk_delete_ajax-progress-modal';
    this.modalBulkDeleteProductsProgressBody = `${this.modalBulkDeleteProductsProgress} div.modal-body`;
    this.modalBulkDeleteProductsProgressSuccessMessage = `${this.modalBulkDeleteProductsProgressBody}`
      + ' div.progress-message';
    this.modalBulkDeleteProductsProgressFooter = `${this.modalBulkDeleteProductsProgress} div.modal-footer`;
    this.modalBulkDeleteProductsProgressBarDone = '#modal_progressbar_done';
    this.modalBulkDeleteProductsCloseButton = `${this.modalBulkDeleteProductsProgressFooter} button.close-modal-button`;

    // Pagination
    this.paginationBlock = `${this.productGridPanel} div.pagination-block`;
    this.productsNumberLabel = `${this.paginationBlock} label.col-form-label`;
    this.paginationNextLink = '.page-item.next:not(.disabled) [data-role=next-page-link]';
  }

  /*
  Methods
   */

  /**
   * Click on new product button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnNewProductButton(page) {
    await this.waitForSelectorAndClick(page, this.newProductButton);

    return this.elementVisible(page, this.modalCreateProduct, 1000);
  }

  /**
   * Get product type description
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductDescription(page) {
    await this.waitForVisibleSelector(page, `${this.modalCreateProduct} iframe`);
    await this.waitForHiddenSelector(page, this.modalCreateProductLoader);

    const createProductFrame = await page.frame({url: /sell\/catalog\/products-v2\/create/gmi});

    return this.getTextContent(createProductFrame, this.productTypedescription);
  }

  /**
   * Get alert danger block content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertDangerBlockContent(page) {
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Choose product type
   * @param page {Page} Browser tab
   * @param productType {string} Product type to select
   * @returns {Promise<void>}
   */
  async selectProductType(page, productType) {
    await this.waitForVisibleSelector(page, `${this.modalCreateProduct} iframe`);
    await this.waitForHiddenSelector(page, this.modalCreateProductLoader);

    const createProductFrame = await page.frame({url: /sell\/catalog\/products-v2\/create/gmi});
    await this.waitForSelectorAndClick(createProductFrame, this.productType(productType));
  }

  /**
   * Choose product type
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnAddNewProduct(page) {
    const createProductFrame = await page.frame({url: /sell\/catalog\/products-v2\/create/gmi});

    await this.waitForSelectorAndClick(createProductFrame, this.addNewProductButton);
  }

  /**
   * Get number of products from header
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsFromHeader(page) {
    return this.getNumberFromText(page, this.headerTitle);
  }

  /**
   * Is reset button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isResetButtonVisible(page) {
    return this.elementVisible(page, this.filterResetButton, 1000);
  }

  /**
   * Go to product page
   * @param page {Page} Browser tab
   * @param row {number} Row in product table
   * @returns {Promise<void>}
   */
  async goToProductPage(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.productsListTableColumnName(row));
  }

  // Bulk delete products functions
  /**
   * Bulk select products
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async bulkSelectProducts(page) {
    await this.waitForSelectorAndClick(page, this.selectAllProductsCheckbox);

    return this.elementNotVisible(page, `${this.productBulkMenuButton}[disabled]`, 1000);
  }

  /**
   * Click on bulk delete products
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnBulkDeleteProducts(page) {
    await Promise.all([
      await this.waitForSelectorAndClick(page, this.productBulkMenuButton),
      await this.waitForVisibleSelector(page, this.bulkActionsDropDownMenu),
    ]);
    await this.waitForSelectorAndClick(page, this.bulkActionsdeleteSelectionLink);
    await this.waitForVisibleSelector(page, this.modalBulkDeleteProducts);

    return this.getTextContent(page, this.modalBulkdeleteProductsBody);
  }

  /**
   * Bulk delete products
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteProduct(page) {
    await this.waitForSelectorAndClick(page, this.modalDialogBulkDeleteButton);

    await this.waitForVisibleSelector(page, this.modalBulkDeleteProductsProgressBarDone);

    return this.getTextContent(page, this.modalBulkDeleteProductsProgressSuccessMessage);
  }

  /**
   * Close bulk delete progress modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeBulkDeleteProgressModal(page) {
    await this.clickAndWaitForNavigation(page, this.modalBulkDeleteProductsCloseButton);

    return this.elementNotVisible(page, this.modalBulkDeleteProductsProgress, 1000);
  }

  // Filter products table methods
  /**
   * Reset filter by category
   * @param page {Page} Browser tab
   * @param categoryName {string} Category name to filter by
   * @returns {Promise<void>}
   */
  async filterProductsByCategory(page, categoryName = 'Home') {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Click on expand button
    await page.click(this.filterByCategoriesExpandButton);

    // Choose category to filter with
    const args = {allCategoriesSelector: this.filterByCategoriesLabel, val: categoryName};
    // eslint-disable-next-line no-eval
    const fn = eval(`({
      async categoryClick(args) {
        /* eslint-env browser */
        const allCategories = [...await document.querySelectorAll(args.allCategoriesSelector)];
        const category = await allCategories.find((el) => el.textContent.includes(args.val));

        if (category === undefined) {
          return false;
        }
        await category.querySelector('input').click();
        return true;
      }
    })`);
    const found = await page.evaluate(fn.categoryClick, args);

    if (!found) {
      throw new Error(`${categoryName} not found as a category`);
    }
    await page.waitForNavigation('networkidle');
  }

  /**
   * Get filter by categories button name
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getFilterByCategoryButtonName(page) {
    return this.getTextContent(page, this.filterByCategoriesButton);
  }

  /**
   * Reset filter by category
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
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
   * Is clear filter link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isClearFilterLinkVisible(page) {
    return this.elementVisible(page, this.clearFilterButton, 2000);
  }

  /**
   * Click on clear filter link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnClearFilterLink(page) {
    await this.clickAndWaitForNavigation(page, this.clearFilterButton);
  }

  /**
   * Is position column visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isPositionColumnVisible(page) {
    return this.elementVisible(page, this.productFilterPositionInput);
  }

  /**
   * Filter products Min - Max
   * @param page {Page} Browser tab
   * @param idMin {number} Value of id min to set on filter input
   * @param idMax {number} Value of id max to set on filter input
   * @return {Promise<void>}
   */
  async filterProductsByID(page, idMin, idMax) {
    await page.type(this.productFilterIDMinInput, idMin.toString());
    await page.type(this.productFilterIDMaxInput, idMax.toString());
  }

  /**
   * Filter Quantity Min - Max
   * @param page {Page} Browser tab
   * @param quantityMin {number} Value of quantity min to set on input
   * @param quantityMax {number} Value of quantity max to set on input
   * @return {Promise<void>}
   */
  async filterProductsByQuantity(page, quantityMin, quantityMax) {
    await page.type(this.productFilterQuantityMinInput, quantityMin.toString());
    await page.type(this.productFilterQuantityMaxInput, quantityMax.toString());
  }

  /**
   * Filter price Min - Max
   * @param page {Page} Browser tab
   * @param priceMin {number} Value of min price to set on filter input
   * @param priceMax {number} Value of max price to set on filter input
   * @return {Promise<void>}
   */
  async filterProductsByPrice(page, priceMin, priceMax) {
    await page.type(this.productFilterPriceMinInput, priceMin.toString());
    await page.type(this.productFilterPriceMaxInput, priceMax.toString());
  }

  /**
   * Filter products
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {{min: number, max:number}|string|boolean|number} Value to put on filter
   * @param filterType {string} Input or select to choose method of filter
   * @return {Promise<void>}
   */
  async filterProducts(page, filterBy, value = '', filterType = 'input') {
    switch (filterType) {
      case 'input':
        switch (filterBy) {
          case 'id_product':
            await this.filterProductsByID(page, value.min, value.max);
            break;
          case 'product_name':
            await page.type(this.productFilterNameInput, value);
            break;
          case 'reference':
            await page.type(this.productFilterReferenceInput, value);
            break;
          case 'category':
            await page.type(this.productFilterCategoryInput, value);
            break;
          case 'price':
            await this.filterProductsByPrice(page, value.min, value.max);
            break;
          case 'quantity':
            await this.filterProductsByQuantity(page, value.min, value.max);
            break;
          case 'position':
            await this.setValue(page, this.productFilterPositionInput, value);
            break;
          default:
        }
        break;
      case 'select':
        await this.selectByVisibleText(page, this.productFilterSelectStatus, value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
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

    const footerText = await this.getTextContent(page, this.productsNumberLabel);
    const numberOfProduct = /\d+/g.exec(footerText.match(/out of ([0-9]+)/)).toString();

    return parseInt(numberOfProduct, 10);
  }

  /**
   * Get Product Price
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param withTaxes {boolean} True if we need to get product price with tax, false if not
   * @returns {Promise<number>}
   */
  async getProductPriceFromList(page, row, withTaxes) {
    const selector = withTaxes ? this.productsListTableColumnPriceATI : this.productsListTableColumnPriceTExc;
    const text = await this.getTextContent(page, selector(row));
    const price = /\d+(\.\d+)?/g.exec(text).toString();

    return parseFloat(price);
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
      `${this.productsListTableColumnStatus(row)}[checked]`,
      'value',
    );

    return inputValue !== '0';
  }

  /**
   * Get text column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get text content
   * @param row {number} Row on table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page, columnName, row = 1) {
    switch (columnName) {
      case 'id_product':
        return this.getNumberFromText(page, this.productsListTableColumnID(row));
      case 'product_name':
        return this.getTextContent(page, this.productsListTableColumnName(row));
      case 'reference':
        return this.getTextContent(page, this.productsListTableColumnReference(row));
      case 'category':
        return this.getTextContent(page, this.productsListTableColumnCategory(row));
      case 'price':
        return this.getProductPriceFromList(page, row, false);
      case 'quantity':
        return this.getNumberFromText(page, this.productsListTableColumnQuantity(row));
      case 'active':
        return this.getProductStatusFromList(page, row);
      case 'position':
        return this.getNumberFromText(page, this.productsListTableColumnPosition(row));
      default:
      // Do nothing
    }
    throw new Error(`${columnName} was not found as column`);
  }

  /**
   * Get text for empty table
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getTextForEmptyTable(page) {
    return this.getTextContent(page, this.productEmptyRow);
  }

  /**
   * Click on delete product button
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async clickOnDeleteProductButton(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.productListTableDropDownList(row));
    await this.waitForSelectorAndClick(page, this.productListTableDeleteButton(row));

    return this.elementVisible(page, this.modalDialog, 1000);
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async deleteProduct(page) {
    await this.waitForSelectorAndClick(page, this.modalDialogDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new Products();
