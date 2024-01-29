import FOBasePage from '@pages/FO/classic/FObasePage';

import type {Page} from 'playwright';

/**
 * Vouchers page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyWishlistsPage extends FOBasePage {
  public readonly pageTitle: string;

  private readonly headerTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on vouchers page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'My wishlists';

    // Selectors
    this.headerTitle = '#content-wrapper h1';
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
}

const myWishlistsPage = new MyWishlistsPage();
export {myWishlistsPage, MyWishlistsPage};
