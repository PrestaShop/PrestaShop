import BOBasePage from '@pages/BO/BObasePage';

/**
 * Stats page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Stats extends BOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on stats page
   */
  constructor() {
    super();

    this.pageTitle = `Stats • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
  }

  /*
  Methods
   */
}

export default new Stats();
