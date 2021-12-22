const faker = require('faker');

const genders = ['Male', 'Female', 'Neutral'];

/**
 * Create new title to use on title form on BO
 * @class
 */
class TitleData {
  /**
   * Constructor for class TitleData
   * @param titleToCreate {Object} Could be used to force the value of some members
   */
  constructor(titleToCreate = {}) {
    // Title name should contain at most 20 characters
    /** @type {string} Name of the title */
    this.name = titleToCreate.name || (faker.random.word()).substring(0, 19).trim();

    /** @type {string} French name of the title */
    this.frName = titleToCreate.frName || this.name;

    /** @type {string} Gender type of the title */
    this.gender = titleToCreate.gender || faker.random.arrayElement(genders);

    /** @type {string} Name of the image to add to the title */
    this.imageName = titleToCreate.imageName || faker.system.commonFileName('png');

    /** @type {number} Width of the image */
    this.imageWidth = titleToCreate.imageWidth || 16;

    /** @type {number} Height of the image */
    this.imageHeight = titleToCreate.imageHeight || 16;
  }
}

module.exports = TitleData;
