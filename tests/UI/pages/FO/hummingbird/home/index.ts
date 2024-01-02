import {Page} from 'playwright';
// Import FO Pages
import {HomePage} from '@pages/FO/home';

/**
 * Home page, contains functions that can be used on the page
 * @class
 * @extends HomePage
 */
class Home extends HomePage {
  private readonly productAddToWishlist: (number: number) => string;

  private readonly wishlistModal: string;

  private readonly wishlistModalTitle: string;

  private readonly addToCartIcon: (number: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor() {
    super('hummingbird');

    // Products list
    this.productArticle = (number: number) => `#content section.featured-products div.container div article:nth-child(${number})`;
    this.addToCartIcon = (number: number) => `${this.productArticle(number)} button[data-button-action='add-to-cart']`;
    this.productAddToWishlist = (number: number) => `${this.productArticle(number)} button.wishlist-button-add`;
    // Wishlist modal
    this.wishlistModal = '.wishlist-add-to .wishlist-modal.show';
    this.wishlistModalTitle = '.wishlist-modal.show p.modal-title';
  }

  /**
   * Add a product (based on its index) to the first wishlist
   * @param page {Page} Browser tab
   * @param idxProduct {number} Id of product
   * @returns Promise<string>
   */
  async clickOnAddToWishListLink(page: Page, idxProduct: number): Promise<string> {
    // Click on the heart
    await page.locator(this.productAddToWishlist(idxProduct)).click();
    // Wait for the modal
    await this.elementVisible(page, this.wishlistModal, 2000);

    return this.getTextContent(page, this.wishlistModalTitle);
  }

  /**
   * Is add to cart button visible
   * @param page {Page} Browser tab
   * @param id {number} Id of product
   * @returns Promise<boolean>
   */
  async isAddToCartButtonVisible(page: Page, id: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.addToCartIcon(id), 1000);
  }
}

export default new Home();
