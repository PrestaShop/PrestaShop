// Import FO pages
import {Product} from '@pages/FO/classic/product';

/**
 * @class
 * @extends FOBasePage
 */
class ProductPage extends Product {
  /**
   * @constructs
   * Setting up texts and selectors to use on checkout page
   */
  constructor() {
    super('hummingbird');

    this.proceedToCheckoutButton = '#blockcart-modal div.cart-footer-actions a';
    this.productName = '#content-wrapper h1.product__name';
    this.shortDescription = 'div.product__description-short';
    this.productFlags = '#product-images  ul.product-flags';
    this.customizedTextarea = (row: number) => `.product-customization__item:nth-child(${row}) .product-message`;
    this.customizationBlock = 'div.product__col section.product-customization';
    this.customizationsMessage = (row: number) => `.product-customization__item:nth-child(${row}) div.card-body div:nth-child(2)`;

    // Product prices block
    this.productPricesBlock = 'div.product__prices';
    this.productPrice = `${this.productPricesBlock} .product__current-price`;
  }
}

export default new ProductPage();
