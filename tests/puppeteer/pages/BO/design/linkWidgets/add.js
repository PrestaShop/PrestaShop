require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddLinkWidget extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Link Widget •';

    // Selectors
    this.changeNamelangButton = '#form_link_block_block_name';
    this.changeNameLangSpan = 'div.dropdown-menu span[data-locale=\'%LANG\']';
    this.nameInput = '#form_link_block_block_name_%ID';
    this.hookSelect = '#form_link_block_id_hook';
    this.cmsPagesCheckbox = '#form_link_block_cms_%ID + i';
    this.cmsPagesCheckbox = '#form_link_block_cms_%ID + i';
    this.productsPagesCheckbox = '#form_link_block_product_%ID + i';
    this.staticContentCheckbox = '#form_link_block_static_%ID + i';
    this.customTitleInput = '#form_link_block_custom_%POSITION_%ID_title';
    this.customUrlInput = '#form_link_block_custom_%POSITION_%ID_url';
    this.customBlockForm = '#form_link_block_custom';
    this.formGroupRowDiv = `${this.customBlockForm} > div.form-group.row:nth-of-type(%ID)`;
    this.deleteCustomUrlButton = `${this.formGroupRowDiv} button.remove_custom_url`;
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
      this.page.waitForSelector(`${this.changeNamelangButton}[aria-expanded='false']`, {visible: true}),
    ]);
    await Promise.all([
      this.page.click(this.changeNameLangSpan.replace('%LANG', lang)),
      this.page.waitForSelector(`${this.changeNamelangButton}[aria-expanded='true']`, {visible: true}),
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
      switch (contentPage) {
        case 'Delivery':
          await this.page.click(this.cmsPagesCheckbox.replace('%ID', 0));
          break;
        case 'Legal Notice':
          await this.page.click(this.cmsPagesCheckbox.replace('%ID', 1));
          break;
        case 'Terms and conditions of use':
          await this.page.click(this.cmsPagesCheckbox.replace('%ID', 2));
          break;
        case 'About us':
          await this.page.click(this.cmsPagesCheckbox.replace('%ID', 3));
          break;
        case 'Secure payment':
          await this.page.click(this.cmsPagesCheckbox.replace('%ID', 4));
          break;
        default:
          // Do nothing
      }
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
      switch (productPage) {
        case 'Prices drop':
          await this.page.click(this.productsPagesCheckbox.replace('%ID', 0));
          break;
        case 'New products':
          await this.page.click(this.productsPagesCheckbox.replace('%ID', 1));
          break;
        case 'Best sales':
          await this.page.click(this.productsPagesCheckbox.replace('%ID', 2));
          break;
        default:
        // Do nothing
      }
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
      switch (staticPage) {
        case 'Contact us':
          await this.page.click(this.staticContentCheckbox.replace('%ID', 0));
          break;
        case 'Sitemap':
          await this.page.click(this.staticContentCheckbox.replace('%ID', 1));
          break;
        case 'Stores':
          await this.page.click(this.staticContentCheckbox.replace('%ID', 2));
          break;
        case 'Login':
          await this.page.click(this.staticContentCheckbox.replace('%ID', 3));
          break;
        case 'My account':
          await this.page.click(this.staticContentCheckbox.replace('%ID', 4));
          break;
        default:
        // Do nothing
      }
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
      await this.setValue(
        this.customTitleInput.replace('%POSITION', i).replace('%ID', 1),
        customPages[i - 1].name,
      );
      await this.setValue(
        this.customUrlInput.replace('%POSITION', i).replace('%ID', 1),
        customPages[i - 1].url,
      );
      // Set french title and url
      await this.changeLanguage('fr');
      await this.setValue(
        this.customTitleInput.replace('%POSITION', i).replace('%ID', 2),
        customPages[i - 1].name,
      );
      await this.setValue(
        this.customUrlInput.replace('%POSITION', i).replace('%ID', 2),
        customPages[i - 1].url,
      );
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
    await this.setValue(this.nameInput.replace('%ID', 1), linkWidgetData.name);
    await this.changeLanguage('fr');
    await this.setValue(this.nameInput.replace('%ID', 2), linkWidgetData.frName);
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
