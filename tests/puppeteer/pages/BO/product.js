// Using chai
const {expect} = require('chai');
// Importing page
const BOBasePage = require('../BO/BObasePage');

module.exports = class Product extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Products •';
    this.productDeletedSuccessfulMessage = 'Product successfully deleted.';

    // Selectors
    // List of products
    this.productListForm = '#product_catalog_list';
    this.productRow = `${this.productListForm} table tbody tr`;
    this.productListfooterRow = `${this.productListForm} div.row:nth-of-type(3)`;
    this.productNumberBloc = `${this.productListfooterRow} label.col-form-label`;
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
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.filterSearchButton),
    ]);
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
    return parseFloat(numberOfProduct);
  }

  /**
   * Reset input filters
   * @return {Promise<void>}
   */
  async resetFilter() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.filterResetButton),
    ]);
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
    const found = await this.page.evaluate(async (allCategoriesSelector, value) => {
      const allCategories = [...await document.querySelectorAll(allCategoriesSelector)];
      const category = await allCategories.find(el => el.textContent.includes(value));
      if (category === undefined) return false;
      await category.querySelector('input').click();
      return true;
    }, this.filterByCategoriesCategoryLabel, value);
    await expect(found, `${value} not found as a category`).to.be.true;
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
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.addProductButton),
    ]);
  }
};
