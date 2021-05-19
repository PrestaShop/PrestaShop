const faker = require('faker');

const genders = ['Male', 'Female', 'Neutral'];

/**
 * Class representing title data
 * @class
 */
class Title {
  constructor(titleToCreate = {}) {
    // Title name should contain at most 20 characters
    this.name = titleToCreate.name || (faker.random.word()).substring(0, 19).trim();
    this.frName = titleToCreate.frName || this.name;
    this.gender = titleToCreate.gender || faker.random.arrayElement(genders);
    this.imageName = titleToCreate.imageName || faker.system.commonFileName('png');
    this.imageWidth = titleToCreate.imageWidth || 16;
    this.imageHeight = titleToCreate.imageHeight || 16;
  }
}
module.exports = Title;
