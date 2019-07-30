module.exports = class CommonPage {
  constructor(page) {
    this.page = page;
  }

  async getPageTitle() {
    return this.page.title();
  }

  async goTo(URL) {
    await this.page.goto(URL);
  }

  /**
   * Get Text from element
   * @param selector, from where to get text
   * @return textContent
   */
  async getTextContent(selector) {
    return this.page.$eval(selector, el => el.textContent);
  }

  /**
   * Is checkBox have checked status
   * @param selector, checkbox to check
   * @return boolean, true if checked, false if not
   */
  async elementChecked(selector) {
    return this.page.$eval(selector, el => el.checked);
  }

  /**
   * Is element visible
   * @param selector, element to check
   * @return boolean, true if visible, false if not
   */
  async elementVisible(selector, timeout = 10) {
    try {
      await this.page.waitForSelector(selector, {visible: true, timeout});
      return true;
    } catch (error) {
      return false;
    }
  }

  /**
   * Open link in new Tab and get opened Page
   * @param currentPage, current page where to click on selector
   * @param selector, where to click
   * @return newPage, what was opened by the browser
   */
  async openLinkWithTargetBlank(currentPage, selector) {
    const pageTarget = await currentPage.target();
    await currentPage.click(selector);
    const newTarget = await global.browser.waitForTarget(target => target.opener() === pageTarget);
    this.page = await newTarget.page();
    await this.page.waitForSelector('body');
    return this.page;
  }

  async reloadPage() {
    await this.page.reload({waitUntil: 'networkidle0'});
  }
};
