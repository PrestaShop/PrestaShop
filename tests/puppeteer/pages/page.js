module.exports = class Page {

  constructor(page) {
  }

  async getPageTitle() {
    return global.page.title();
  }

  async goTo(URL) {
    await global.page.goto(URL);
  }

};
