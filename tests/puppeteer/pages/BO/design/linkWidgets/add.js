require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddLinkWidget extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Link Widget •';

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
    this.customBlockForm = '#form_link_block_custom';
    this.formGroupRowDiv = id => `${this.customBlockForm} > div.form-group.row:nth-of-type(${id})`;
    this.deleteCustomUrlButton = id => `${this.formGroupRowDiv(id)} button.remove_custom_url`;
    this.addCustomBlockButton = 'button[data-collection-id=\'form_link_block_custom\']';
    this.saveButton = '.card-footer button';
  }

  /* Methods */
  /**
   * Change input name language
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguage(lang) {
    await Promise.all([
      this.page.click(this.changeNamelangButton),
      this.waitForVisibleSelector(`${this.changeNamelangButton}[aria-expanded='false']`),
    ]);
    await Promise.all([
      this.page.click(this.changeNameLangSpan(lang)),
      this.waitForVisibleSelector(`${this.changeNamelangButton}[aria-expanded='true']`),
    ]);
  }

  /**
   * Select content pages
   * @param contentPages
   * @return {Promise<void>}
   */
  async selectContentPages(contentPages) {
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
      await this.page.$eval(selector, el => el.click());
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Select product pages
   * @param productPages
   * @return {Promise<void>}
   */
  async selectProductPages(productPages) {
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
      await this.page.$eval(selector, el => el.click());
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Select static pages
   * @param staticPages
   * @return {Promise<void>}
   */
  async selectStaticPages(staticPages) {
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
      await this.page.$eval(selector, el => el.click());
    }
    /* eslint-enable no-restricted-syntax */
  }

  /**
   * Add custom pages
   * @param customPages
   * @return {Promise<void>}
   */
  async addCustomPages(customPages) {
    for (let i = 1; i <= customPages.length; i++) {
      // Set english title and url
      await this.changeLanguage('en');
      await this.setValue(this.customTitleInput(i, 1), customPages[i - 1].name);
      await this.setValue(this.customUrlInput(i, 1), customPages[i - 1].url);
      // Set french title and url
      await this.changeLanguage('fr');
      await this.setValue(this.customTitleInput(i, 2), customPages[i - 1].name);
      await this.setValue(this.customUrlInput(i, 2), customPages[i - 1].url);
      // Add another custom page block
      await this.page.click(this.addCustomBlockButton);
    }
  }

  /**
   * Add linkWidget
   * @param linkWidgetData
   * @return {Promise<*|string>}
   */
  async addLinkWidget(linkWidgetData) {
    // Set name in languages
    await this.changeLanguage('en');
    await this.setValue(this.nameInput(1), linkWidgetData.name);
    await this.changeLanguage('fr');
    await this.setValue(this.nameInput(2), linkWidgetData.frName);
    // Choose hook
    await this.selectByVisibleText(this.hookSelect, linkWidgetData.hook);
    // select content pages
    await this.selectContentPages(linkWidgetData.contentPages);
    // select product pages
    await this.selectProductPages(linkWidgetData.productsPages);
    // select static pages
    await this.selectStaticPages(linkWidgetData.staticPages);
    // Add custom pages
    await this.addCustomPages(linkWidgetData.customPages);
    // Save
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
