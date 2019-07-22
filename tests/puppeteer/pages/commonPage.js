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
   * Wait for selector and click
   * @param selector, element to check
   * @return boolean, true if text exist, false if not
   */
  async waitForSelectorAndClick(selector, timeout = 10) {
    await this.page.waitForSelector(selector, {visible: true, timeout});
    await this.page.click(selector);
  }

  /**
   * Check text value
   * @param selector, element to check
   * @param textToCheckWith, text to check with
   * @param parameter, parameter to use
   * @return boolean, true when the result of parameter(textToCheckWith) is true, false if not
   */
  async checkTextValue(selector, textToCheckWith, parameter = 'equal') {
    await this.page.waitForSelector(selector);
    switch (parameter) {
      case 'equal':
        await this.page.$eval(selector, el => el.innerText)
          .then(text => expect(text.replace(/\s+/g, ' ').trim()).to.equal(textToCheckWith));
        break;
      case 'contain':
        await this.page.$eval(selector, el => el.innerText)
          .then(text => expect(text).to.contain(textToCheckWith));
        break;
      default:
      // do nothing
    }
  }

  /**
   * Check attribute value
   * @param selector, element to check
   * @param attribute, attribute to test
   * @param textToCheckWith, text to check with
   * @return boolean, true if text in attribute is equal to textToCheckWith, false if not
   */
  async checkAttributeValue(selector, attribute, textToCheckWith) {
    await this.page.waitForSelector(selector);
    const value = await this.page.$eval(selector, (el, attribute) => el
      .getAttribute(attribute), attribute);
    expect(value).to.be.equal(textToCheckWith);
  }
};
