module.exports = class Page {

  constructor(page) {
    this.page = page;
  }

  async getPageTitle() {
    return await this.page.title();
  }

  async goTo(URL) {
    await this.page.goto(URL);
  }
};
