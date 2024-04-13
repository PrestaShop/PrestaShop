import {Page} from 'playwright';
// Import FO Pages
import {HomePage} from '@pages/FO/classic/home';

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

  private readonly productColor: (number: number, color: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor() {
    super('hummingbird');

    // Selectors of slider
    this.carouselSliderId = 'div.carousel.slide';
    this.carouselControlDirectionLink = (direction: string) => `${this.carouselSliderId} button.carousel-control-${direction}`;
    this.carouselSliderInnerList = `${this.carouselSliderId} div.carousel-inner`;
    this.carouselSliderInnerListItem = (position: number) => `${this.carouselSliderInnerList} li:nth-child(${position})`;
    this.carouselSliderURL = `${this.carouselSliderInnerList} li[aria-hidden='false'] a`;

    // Products list
    this.productArticle = (number: number) => `#content section.featured-products div.container div article:nth-child(${number})`;
    this.addToCartIcon = (number: number) => `${this.productArticle(number)} button[data-button-action='add-to-cart']`;
    this.productAddToWishlist = (number: number) => `${this.productArticle(number)} button.wishlist-button-add`;
    this.productColor = (number: number, color: string) => `${this.productArticle(number)} div.product-miniature-variants`
      + ` a[title='${color}']`;

    // Wishlist modal
    this.wishlistModal = '.wishlist-add-to .wishlist-modal.show';
    this.wishlistModalTitle = '.wishlist-modal.show p.modal-title';

    // Newsletter form
    this.newsletterFormField = '#footer div.email-subscription__content__right input[name="email"]';
    this.newsletterSubmitButton = '.email-subscription__content__inputs [name="submitNewsletter"][value="Subscribe"]';
    this.subscriptionAlertMessage = '#footer div.email-subscription__content__infos p.alert';

    // Products section
    this.productsBlockTitle = (blockName: number | string) => `#content section.${blockName} h2`;
    this.productsBlockDiv = (blockName: number | string) => `#content section.${blockName} div.products div.card`;
    this.allProductsBlockLink = (blockName: number | string) => `#content div.${blockName}-footer a`;
    this.productArticle = (number: number) => `#content section.featured-products article:nth-child(${number})`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} .product-miniature__quickview `
      + 'button';
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
   * @param nthProduct {number} nth of product
   * @returns Promise<boolean>
   */
  async isAddToCartButtonVisible(page: Page, nthProduct: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.addToCartIcon(nthProduct), 1000);
  }

  /**
   * Go to all products
   * @param page {Page} Browser tab
   * @param blockName {string} The block name in the page
   * @return {Promise<void>}
   */
  async goToAllProductsPage(page: Page, blockName: string = 'featured-products'): Promise<void> {
    await this.clickAndWaitForURL(page, this.allProductsBlockLink(blockName));
  }

  /**
   * Quick view product
   * @param page {Page} Browser tab
   * @param id {number} Product row in the list
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).hover();
    await this.waitForVisibleSelector(page, this.productQuickViewLink(id));
    await page.locator(this.productQuickViewLink(id)).click();
  }

  /**
   * Add product to cart
   * @param page {Page} Browser tab
   * @param nthProduct {number} Product row in the list
   */
  async addProductToCart(page: Page, nthProduct: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addToCartIcon(nthProduct));
  }

  /**
   * Select product color
   * @param page {Page} Browser tab
   * @param nthProduct {number} Product row in the list
   * @param color {string} Color to select
   * @return {Promise<void>}
   */
  async selectProductColor(page: Page, nthProduct: number, color: string) {
    await this.clickAndWaitForURL(page, this.productColor(nthProduct, color));
  }
}

export default new Home();
