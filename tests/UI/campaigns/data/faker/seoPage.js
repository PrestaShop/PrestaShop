const faker = require('faker');

/**
 * Class representing seo page data
 * @class
 */
class SeoPage {
  constructor(seoPageToCreate) {
    this.page = seoPageToCreate.page;
    this.title = seoPageToCreate.title || faker.lorem.word();
    this.frTitle = seoPageToCreate.frTitle || this.title;
    this.metaDescription = seoPageToCreate.metaDescription || faker.lorem.sentence();
    this.frMetaDescription = seoPageToCreate.frMetaDescription || this.metaDescription;
    this.metaKeywords = seoPageToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];
    this.frMetaKeywords = seoPageToCreate.frMetaKeywords || this.metaKeywords;
    this.friendlyUrl = seoPageToCreate.friendlyUrl || this.page.replace(new RegExp(' ', 'g'), '-');
    this.frFriendlyUrl = seoPageToCreate.frFriendlyUrl || this.friendlyUrl;
  }
}
module.exports = SeoPage;
