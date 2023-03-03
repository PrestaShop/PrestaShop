import BOBasePage from '@pages/BO/BObasePage';

/**
 * View supplier page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewSupplier extends BOBasePage {
  private readonly productsGrid: string;

  private readonly productsGridHeader: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on view supplier page
   */
  constructor() {
    super();

    // Selectors
    this.productsGrid = '[data-role=products-card]';
    this.productsGridHeader = `${this.productsGrid} h3.card-header`;
  }

  /*
  Methods
   */
}

export default new ViewSupplier();
