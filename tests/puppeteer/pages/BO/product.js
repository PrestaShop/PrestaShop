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
    // Filters input
    this.productFilterInput = `${this.productListForm} input[name='filter_column_%FilterBy']`;
    this.filterSearchButton = `${this.productListForm} button[name='products_filter_submit']`;
    this.filterResetButton = `${this.productListForm} button[name='products_filter_reset']`;
    // Filter Category
    this.treeCategoriesBloc = `#tree-categories`;
    this.filterByCategoriesButton = `#product_catalog_category_tree_filter button`;
    this.filterByCategoriesExpandButton = `${this.treeCategoriesBloc} a#product_catalog_category_tree_filter_expand`;
    this.filterByCategoriesUnselectButton = `${this.treeCategoriesBloc} a#product_catalog_category_tree_filter_reset`;
    this.filterByCategoriesCategoryLabel = `${this.treeCategoriesBloc} label.category-label`;
    // HEADER buttons
    this.addProductButton = `#page-header-desc-configuration-add`;
  }

  /*
  Methods
   */
};
