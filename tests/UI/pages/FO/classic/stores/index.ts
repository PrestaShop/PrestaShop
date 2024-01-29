import FOBasePage from '@pages/FO/classic/FObasePage';

import type {Page} from 'playwright';

/**
 * Stores page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class StoresPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly storeBlock: (idStore: number) => string;

  public readonly storeImage: (idStore: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on stores page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Stores';

    this.storeBlock = (idStore: number) => `article#store-${idStore}`;
    this.storeImage = (idStore:number) => `${this.storeBlock(idStore)} div.store-picture`;
  }

  /**
   * Returns the URL of the main image of a store
   * @param page {Page} Browser tab
   * @param idStore {number} ID of a store
   * @returns {Promise<string|null>}
   */
  async getStoreImageMain(page: Page, idStore: number): Promise<string|null> {
    return this.getAttributeContent(page, `${this.storeImage(idStore)} source`, 'srcset');
  }
}

const storesPage = new StoresPage();
export {storesPage, StoresPage};
