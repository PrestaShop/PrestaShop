import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Vouchers page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyWishlistsPage extends FOBasePage {
  public readonly pageTitle: string;

  private readonly headerTitle: string;

  private readonly wishlistList: string;

  private readonly wishlistListItem: string;

  private readonly wishlistListItemNth: (nth: number) => string;

  private readonly wishlistListItemNthLink: (nth: number) => string;

  private readonly wishlistListItemNthTitle: (nth: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on vouchers page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'My wishlists';

    // Selectors
    this.headerTitle = '#content-wrapper h1';
    this.wishlistList = '.wishlist-list';
    this.wishlistListItem = `${this.wishlistList} .wishlist-list-item`;
    this.wishlistListItemNth = (nth: number) => `${this.wishlistListItem}:nth-child(${nth})`;
    this.wishlistListItemNthLink = (nth: number) => `${this.wishlistListItemNth(nth)} a.wishlist-list-item-link`;
    this.wishlistListItemNthTitle = (nth: number) => `${this.wishlistListItemNth(nth)} p.wishlist-list-item-title`;
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
   * Returns the number of wishlists
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async countWishlists(page: Page): Promise<number> {
    return page.locator(this.wishlistListItem).count();
  }

  /**
   * Click and go to a specific wishlist
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<number>}
   */
  async goToWishlistPage(page: Page, nth: number): Promise<void> {
    await page.locator(this.wishlistListItemNthLink(nth)).click();
  }

  /**
   * Returns the name of a specific wishlist
   * @param page {Page} Browser tab
   * @param nth {number} Nth of the wishlist
   * @returns {Promise<string>}
   */
  async getWishlistName(page: Page, nth: number): Promise<string> {
    const textContent = await this.getTextContent(page, this.wishlistListItemNthTitle(nth));

    return textContent
      .substring(
        0,
        textContent.search(/\(/),
      )
      .trim();
  }
}

const myWishlistsPage = new MyWishlistsPage();
export {myWishlistsPage, MyWishlistsPage};
