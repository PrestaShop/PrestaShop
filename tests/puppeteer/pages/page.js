module.exports = class Page {

  constructor(page) {
  }

  async getTitle() {
    return this.page.title();
  }

  async goTo(URL) {
    await global.page.goto(URL);
  }

};
