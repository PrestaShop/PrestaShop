import FOBasePage from '@pages/FO/classic/FObasePage';

/**
 * New products page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class NewProductsPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on new products page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'New products';
  }
}

const newProductsPage = new NewProductsPage();
export {newProductsPage, NewProductsPage};
