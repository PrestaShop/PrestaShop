require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Product extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Products •';
    this.productDeletedSuccessfulMessage = 'Product successfully deleted.';
    this.productMultiDeletedSuccessfulMessage = 'Product(s) successfully deleted.';

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
    this.productFilterInput = `${this.productListForm} input[name='filter_column_%FILTERBY']`;
    this.filterSearchButton = `${this.productListForm} button[name='products_filter_submit']`;
    this.filterResetButton = `${this.productListForm} button[name='products_filter_reset']`;
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
   * Filter products from inputs : Name, reference and Category
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterProducts(filterBy, value = '') {
    await this.page.type(this.productFilterInput.replace('%FILTERBY', filterBy), value);
    await this.clickAndWaitForNavigation(this.filterSearchButton);
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
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
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
};
