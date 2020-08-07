const faker = require('faker');

const genders = ['Male', 'Female', 'Neutral'];

module.exports = class Title {
  constructor(titleToCreate = {}) {
    this.name = titleToCreate.name || faker.random.word();
    this.frName = titleToCreate.frName || this.name;
    this.gender = titleToCreate.gender || faker.random.arrayElement(genders);
    this.imageName = titleToCreate.imageName || `${this.name}.png`;
    this.imageWidth = titleToCreate.imageWidth || 16;
    this.imageHeight = titleToCreate.imageHeight || 16;
  }
};
