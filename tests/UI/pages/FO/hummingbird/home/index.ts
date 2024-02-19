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

  public readonly blockCartModalSummary: string;

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

    // Block Cart Modal
    this.cartModalCheckoutLink = `${this.blockCartModalDiv} div.cart-footer-actions a`;
    this.continueShoppingButton = `${this.blockCartModalDiv} div.cart-footer-actions button`;

    // Wishlist modal
    this.wishlistModal = '.wishlist-add-to .wishlist-modal.show';
    this.wishlistModalTitle = '.wishlist-modal.show p.modal-title';

    // Newsletter form
    this.newsletterFormField = '#footer div.email-subscription__content__right input[name="email"]';
    this.newsletterSubmitButton = '.email-subscription__content__inputs [name="submitNewsletter"][value="Subscribe"]';
    this.subscriptionAlertMessage = '#footer div.email-subscription__content__infos p.alert';

    // Quick view modal
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} .product-miniature__quickview button`;
    this.blockCartModalCloseButton = `${this.blockCartModalDiv} button.btn-close`;

    // Block cart modal
    this.blockCartModalSummary = '.blockcart-modal__summery';
    this.cartModalProductsCountBlock = `${this.blockCartModalSummary} p`;
    this.cartModalSubtotalBlock = `${this.blockCartModalSummary} .product-subtotal .subtotals.value`;
    this.cartModalShippingBlock = `${this.blockCartModalSummary} .product-shipping .shipping.value`;
    this.cartModalProductTaxInclBlock = `${this.blockCartModalSummary} .product-total .value`;

    // Products section
    this.productsBlockTitle = (blockName: number | string) => `#content section.${blockName} h2`;
    this.productsBlockDiv = (blockName: number | string) => `#content section.${blockName} div.products div.card`;
    this.allProductsBlockLink = (blockName: number | string) => `#content div.${blockName}-footer a`;
    this.productArticle = (number: number) => `#content section.featured-products article:nth-child(${number})`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} .product-miniature__quickview `
      + 'button';
    this.blockCartModalCloseButton = `${this.blockCartModalDiv} button.btn-close`;

    // Quick view modal
    this.quickViewProductName = `${this.quickViewModalDiv} .h3`;
    this.quickViewRegularPrice = `${this.quickViewModalDiv} span.product__price-regular`;
    this.quickViewProductPrice = `${this.quickViewModalDiv} div.product__current-price`;
    this.quickViewDiscountPercentage = `${this.quickViewModalDiv} div.product__discount-percentage`;
    this.quickViewTaxShippingDeliveryLabel = `${this.quickViewModalDiv} div.product__tax-label`;
    this.quickViewCoverImage = `${this.quickViewModalDiv} #product-images img.img-fluid`;
    this.quickViewThumbImage = `${this.quickViewModalDiv} div.thumbnails__container img.img-fluid`;
    this.quickViewProductVariants = `${this.quickViewModalDiv} div.js-product-variants`;
    this.quickViewProductDimension = `${this.quickViewProductVariants} select#group_3`;
    this.quickViewCloseButton = `${this.quickViewModalDiv} button.btn-close`;
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

  /**
   * Go to all products
   * @param page {Page} Browser tab
   * @param blockName {string} The block name in the page
   * @return {Promise<void>}
   */
  async clickOnAllProductsButton(page: Page, blockName: string = 'featured-products'): Promise<void> {
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
   * Get product details from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<{thumbImage: string|null, price: number, taxShippingDeliveryLabel: string,
   * coverImage: string|null, name: string, shortDescription: string}>}
   */
  async getProductDetailsFromQuickViewModal(page: Page): Promise<{
    thumbImage: string | null,
    price: number,
    taxShippingDeliveryLabel: string,
    coverImage: string | null,
    name: string,
    shortDescription: string,
  }> {
    return {
      name: await this.getTextContent(page, this.quickViewProductName),
      price: parseFloat((await this.getTextContent(page, this.quickViewProductPrice)).replace('€', '')),
      taxShippingDeliveryLabel: await this.getTextContent(page, this.quickViewTaxShippingDeliveryLabel),
      shortDescription: await this.getTextContent(page, this.quickViewShortDescription),
      coverImage: await this.getAttributeContent(page, this.quickViewCoverImage, 'src'),
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'srcset'),
    };
  }

  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).first().hover();
    await this.waitForVisibleSelector(page, this.productQuickViewLink(id));
    await page.locator(this.productQuickViewLink(id)).first().click();
  }

  /**
   * Close block cart modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeBlockCartModal(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.blockCartModalCloseButton);

    return this.elementNotVisible(page, this.blockCartModalDiv, 1000);
  }
}

export default new Home();
