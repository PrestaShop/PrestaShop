require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddLinkWidget extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Link List •';

    // Selectors
    this.changeNamelangButton = '#form_link_block_block_name';
    this.changeNameLangSpan = lang => `div.dropdown-menu span[data-locale='${lang}']`;
    this.nameInput = id => `#form_link_block_block_name_${id}`;
    this.hookSelect = '#form_link_block_id_hook';
    this.cmsPagesCheckbox = id => `#form_link_block_cms_${id} + i`;
    this.productsPagesCheckbox = id => `#form_link_block_product_${id} + i`;
    this.staticContentCheckbox = id => `#form_link_block_static_${id} + i`;
    this.customTitleInput = (position, id) => `#form_link_block_custom_${position}_${id}_title`;
    this.customUrlInput = (position, id) => `#form_link_block_custom_${position}_${id}_url`;
    this.addCustomBlockButton = 'button[data-collection-id=\'form_link_block_custom\']';
    this.saveButton = '.card-footer button';
  }

  /* Methods */
  /**
   * Change input name language
   * @param page
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguage(page, lang) {
    await Promise.all([
      page.click(this.changeNamelangButton),
      this.waitForVisibleSelector(page, `${this.changeNamelangButton}[aria-expanded='false']`),
    ]);
    await Promise.all([
      page.click(this.changeNameLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.changeNamelangButton}[aria-expanded='true']`),
    ]);
  }

  /**
   * Select content pages
   * @param page
   * @param contentPages
   * @return {Promise<void>}
   */
  async selectContentPages(page, contentPages) {
    /* eslint-disable no-restricted-syntax */
    for (const contentPage of contentPages) {
      let selector;
      switch (contentPage) {
        case 'Delivery':
          selector = this.cmsPagesCheckbox(0);
          break;
        case 'Legal Notice':
          selector = this.cmsPagesCheckbox(1);
          break;
        case 'Terms and conditions of use':
          selector = this.cmsPagesCheckbox(2);
          break;
        case 'About us':
          selector = this.cmsPagesCheckbox(3);
          break;
        case 'Secure payment':
          selector = this.cmsPagesCheckbox(4);
          break;
        default:
          // Do nothing
      }
      await page.$eval(selector, el => el.click());
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Select product pages
   * @param page
   * @param productPages
   * @return {Promise<void>}
   */
  async selectProductPages(page, productPages) {
    /* eslint-disable no-restricted-syntax */
    for (const productPage of productPages) {
      let selector;
      switch (productPage) {
        case 'Prices drop':
          selector = this.productsPagesCheckbox(0);
          break;
        case 'New products':
          selector = this.productsPagesCheckbox(1);
          break;
        case 'Best sales':
          selector = this.productsPagesCheckbox(2);
          break;
        default:
        // Do nothing
      }
      await page.$eval(selector, el => el.click());
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Select static pages
   * @param page
   * @param staticPages
   * @return {Promise<void>}
   */
  async selectStaticPages(page, staticPages) {
    /* eslint-disable no-restricted-syntax */
    for (const staticPage of staticPages) {
      let selector;
      switch (staticPage) {
        case 'Contact us':
          selector = this.staticContentCheckbox(0);
          break;
        case 'Sitemap':
          selector = this.staticContentCheckbox(1);
          break;
        case 'Stores':
          selector = this.staticContentCheckbox(2);
          break;
        case 'Login':
          selector = this.staticContentCheckbox(3);
          break;
        case 'My account':
          selector = this.staticContentCheckbox(4);
          break;
        default:
        // Do nothing
      }
      await page.$eval(selector, el => el.click());
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Add custom pages
   * @param page
   * @param customPages
   * @return {Promise<void>}
   */
  async addCustomPages(page, customPages) {
    for (let i = 1; i <= customPages.length; i++) {
      // Set english title and url
      await this.changeLanguage(page, 'en');
      await this.setValue(page, this.customTitleInput(i, 1), customPages[i - 1].name);
      await this.setValue(page, this.customUrlInput(i, 1), customPages[i - 1].url);
      // Set french title and url
      await this.changeLanguage(page, 'fr');
      await this.setValue(page, this.customTitleInput(i, 2), customPages[i - 1].name);
      await this.setValue(page, this.customUrlInput(i, 2), customPages[i - 1].url);
      // Add another custom page block
      await page.click(this.addCustomBlockButton);
    }
  }

  /**
   * Add linkWidget
   * @param page
   * @param linkWidgetData
   * @return {Promise<string>}
   */
  async addLinkWidget(page, linkWidgetData) {
    // Set name in languages
    await this.changeLanguage(page, 'en');
    await this.setValue(page, this.nameInput(1), linkWidgetData.name);
    await this.changeLanguage(page, 'fr');
    await this.setValue(page, this.nameInput(2), linkWidgetData.frName);
    // Choose hook
    await this.selectByVisibleText(page, this.hookSelect, linkWidgetData.hook);
    // select content pages
    await this.selectContentPages(page, linkWidgetData.contentPages);
    // select product pages
    await this.selectProductPages(page, linkWidgetData.productsPages);
    // select static pages
    await this.selectStaticPages(page, linkWidgetData.staticPages);
    // Add custom pages
    await this.addCustomPages(page, linkWidgetData.customPages);
    // Save
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddLinkWidget();
