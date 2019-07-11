module.exports = class Page {

  constructor(page) {
  }

  async getTitle() {
    return this.page.title();
  }

};
