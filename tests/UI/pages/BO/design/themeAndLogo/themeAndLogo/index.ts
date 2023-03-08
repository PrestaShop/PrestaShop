import themeAndLogoBasePage from '@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage';

/**
 * Theme & logo page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class ThemeAndLogo extends themeAndLogoBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();

    this.pageTitle = `Theme & Logo • ${global.INSTALL.SHOP_NAME}`;
  }
}

export default new ThemeAndLogo();
