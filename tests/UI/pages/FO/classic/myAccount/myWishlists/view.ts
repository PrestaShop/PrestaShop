import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Vouchers page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class WishlistPage extends FOBasePage {
  public readonly messageSuccessfullyRemoved: string;

  private readonly headerTitle: string;

  private readonly productList: string;

  private readonly productListItem: string;

  private readonly productListItemNth: (nth: number) => string;

  private readonly productListItemNthTitle: (nth: number) => string;

  private readonly productListItemNthCombinations: (nth: number) => string;

  private readonly productListItemNthUnavailable: (nth: number) => string;

  private readonly productListItemNthBtnAddToCart: (nth: number) => string;

  private readonly productListItemNthBtnDelete: (nth: number) => string;

  private readonly modalDelete: string;

  private readonly modalDeleteBtnRemove: string;

  private readonly toastText: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on vouchers page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    // Message
    this.messageSuccessfullyRemoved = 'Product successfully removed';

    // Selectors
    this.headerTitle = '#content-wrapper h1';
    this.productList = '.wishlist-products-list';
    this.productListItem = `${this.productList} .wishlist-products-item`;
    this.productListItemNth = (nth: number) => `${this.productListItem}:nth-child(${nth})`;
    this.productListItemNthTitle = (nth: number) => `${this.productListItemNth(nth)} .wishlist-product-title`;
    this.productListItemNthCombinations = (nth: number) => `${this.productListItemNth(nth)} .wishlist-product-combinations-text`;
    this.productListItemNthUnavailable = (nth: number) => `${this.productListItemNth(nth)} .wishlist-product-availability`;
    this.productListItemNthBtnAddToCart = (nth: number) => `${this.productListItemNth(nth)} .wishlist-product-addtocart`;
    this.productListItemNthBtnDelete = (nth: number) => `${this.productListItemNth(nth)} .wishlist-button-add`;

    // Modal "Delete"
    this.modalDelete = '.wishlist-delete .wishlist-modal.show';
    this.modalDeleteBtnRemove = `${this.modalDelete} div.modal-footer button.btn-primary`;

    // Toast
    this.toastText = '.wishlist-toast .wishlist-toast-text';
  }

  /*
  Methods
   */
  /**
   * @override
   * Get the page title from the main section
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.headerTitle);
  }

  /**
   * Returns the number of product
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async countProducts(page: Page): Promise<number> {
    return page.locator(this.productListItem).count();
  }

  /**
   * Returns the name of a specific product
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<string>}
   */
  async getProductName(page: Page, nth: number): Promise<string> {
    return this.getTextContent(page, this.productListItemNthTitle(nth));
  }

  /**
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<string|null>}
   */
  private async getProductCombinationValue(page: Page, nth: number, key: string): Promise<string|null> {
    const text = await this.getTextContent(page, this.productListItemNthCombinations(nth));

    const regexResult: RegExpExecArray[] = [...text.matchAll(/([A-Za-z]+)\s:\s([A-Za-z0-9]+)/g)];

    // eslint-disable-next-line no-restricted-syntax
    for (const regexResultItem of regexResult) {
      if (regexResultItem[1].toLowerCase() === key) {
        return regexResultItem[2];
      }
    }

    return null;
  }

  /**
   * Returns the quantity of a specific product
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<string>}
   */
  async getProductQuantity(page: Page, nth: number): Promise<number> {
    const result: string|null = await this.getProductCombinationValue(page, nth, 'quantity');

    return result ? parseInt(result, 10) : 0;
  }

  /**
   * Returns the value of an attribute of a specific product
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @param attribute {string} Attribute name
   * @returns {Promise<string|null>}
   */
  async getProductAttribute(page: Page, nth: number, attribute: string): Promise<string|null> {
    return this.getProductCombinationValue(page, nth, attribute.toLowerCase());
  }

  /**
   * Returns if the product has a label Out-of-Stock
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<boolean>}
   */
  async isProductOutOfStock(page: Page, nth: number): Promise<boolean> {
    return (await this.elementVisible(page, this.productListItemNthUnavailable(nth), 3000))
      && (await this.getTextContent(page, this.productListItemNthUnavailable(nth)) === 'block Out-of-Stock');
  }

  /**
   * Returns if the product has a label Last items in stock
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<boolean>}
   */
  async isProductLastItemsInStock(page: Page, nth: number): Promise<boolean> {
    return (await this.elementVisible(page, this.productListItemNthUnavailable(nth), 3000))
      && (await this.getTextContent(page, this.productListItemNthUnavailable(nth)) === 'warning Last items in stock');
  }

  /**
   * Returns if the product has the button "Add to cart" disabled
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<boolean>}
   */
  async hasButtonAddToCartDisabled(page: Page, nth: number): Promise<boolean> {
    return page.locator(this.productListItemNthBtnAddToCart(nth)).isDisabled();
  }

  /**
   * Remove the nth Product of the wishlist
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<string>}
   */
  async removeProduct(page: Page, nth: number): Promise<string> {
    await page.locator(this.productListItemNthBtnDelete(nth)).click();

    // Wait for the modal
    await this.elementVisible(page, this.modalDelete, 3000);
    // Click on the first wishlist
    await page.locator(this.modalDeleteBtnRemove).click();
    // Wait for the toast
    await this.elementVisible(page, this.toastText, 3000);

    return this.getTextContent(page, this.toastText);
  }
}

export default new WishlistPage();
