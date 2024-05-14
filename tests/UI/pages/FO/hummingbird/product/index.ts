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
    this.productAttributeSelect = (itemNumber: number) => `div.product__variants div.variant:nth-child(${itemNumber}) select`;
    this.productAttributeButton = (itemNumber: number) => `div.product__variants div.variant:nth-child(${itemNumber}) ul input`;
    this.deliveryInformationSpan = 'span.product__delivery__information';

    // Product prices block
    this.productPricesBlock = 'div.product__prices';
    this.productPrice = `${this.productPricesBlock} .product__current-price`;
    this.discountAmountSpan = `${this.productPricesBlock} .product__discount-amount`;
    this.discountPercentageSpan = `${this.productPricesBlock} .product__discount-percentage`;
    this.regularPrice = `${this.productPricesBlock} .product__price-regular`;

    // Product discount table
    this.discountTable = '.product__discounts__table';
    this.quantityDiscountValue = `${this.discountTable} td:nth-child(1)`;
    this.unitDiscountColumn = `${this.discountTable} th:nth-child(2)`;
    this.unitDiscountValue = `${this.discountTable} td:nth-child(2)`;
    this.savedValue = `${this.discountTable} td:nth-child(3)`;

    // Products in pack selectors
    this.productInPackImage = (productInList: number) => `${this.productInPackList(productInList)} div.product-pack__image img`;
    this.productInPackName = (productInList: number) => `${this.productInPackList(productInList)} p.product-pack__name`;
    this.productInPackPrice = (productInList: number) => `${this.productInPackList(productInList)} p.product-pack__price `
      + 'strong';
    this.productInPackQuantity = (productInList: number) => `${this.productInPackList(productInList)}`
      + ' p.product-pack__quantity';
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

  /**
   * Set quantity
   * @param page {Page} Browser tab
   * @param quantity {number|string} Quantity to set
   * @returns {Promise<void>}
   */
  async setQuantity(page: Page, quantity: number | string): Promise<void> {
    await this.setValue(page, this.productQuantity, quantity);
  }
}

export default new ProductPage();
