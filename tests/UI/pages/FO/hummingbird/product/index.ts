// Import FO pages
import {Product} from '@pages/FO/classic/product';
import type {Page} from 'playwright';

/**
 * @class
 * @extends FOBasePage
 */
class ProductPage extends Product {
  /**
   * @constructs
   * Setting up texts and selectors to use on checkout page
   */
  private readonly carouselControlProductModal: (direction: string) => string;

  private readonly productImageRow: (row: number) => string;

  constructor() {
    super('hummingbird');

    this.warningMessage = '#js-toast-container div.bg-danger  div.toast-body';
    this.productRowQuantityUpDownButton = (direction: string) => `div.product-actions__quantity button.js-${direction}-button`;
    this.proceedToCheckoutButton = '#blockcart-modal div.cart-footer-actions a';
    this.productCoverImg = '#product-images div.carousel-item.active';
    this.scrollBoxImages = (direction: string) => `#product-images button.carousel-control-${direction}`;
    this.productCoverImgProductModal = '#product-images-modal div.carousel-item.active picture img';
    this.carouselControlProductModal = (direction: string) => `#product-images-modal button.carousel-control-${direction}`;
    this.productImageRow = (row: number) => `#content-wrapper div.thumbnails__container li:nth-child(${row})`;
    this.thumbImg = (row: number) => `#content-wrapper div.thumbnails__container li:nth-child(${row}) picture img.js-thumb`;
    this.zoomIcon = '#product-images div.carousel-item.active i.zoom-in';
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

  /**
   * Get the position in the slide of the cover image
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getCoverImage(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, this.productCoverImg, 'data-bs-slide-to');
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param imageRow {number} Row of the image
   * @returns {Promise<string>}
   */
  async selectThumbImage(page: Page, imageRow: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.thumbImg(imageRow));
    await this.waitForVisibleSelector(page, `${this.productImageRow(imageRow)}.active`);
    await page.waitForTimeout(2000);

    return this.getAttributeContent(page, this.productCoverImg, 'data-bs-slide-to');
  }

  /**
   * Click on arrow next/previous in product modal
   * @param page {Page} Browser tab
   * @param direction {string} Direction Next/Prev
   * @returns {Promise<string>}
   */
  async clickOnArrowNextPrevInProductModal(page: Page, direction: string): Promise<string> {
    await page.locator(this.carouselControlProductModal(direction)).click();

    return this.getAttributeContent(page, this.productCoverImgProductModal, 'src');
  }
}

export default new ProductPage();
