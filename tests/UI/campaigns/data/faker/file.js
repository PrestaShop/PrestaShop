const faker = require('faker');

module.exports = class File {
  constructor(fileToCreate = {}) {
    this.name = fileToCreate.name || faker.system.fileName().substring(0, 32);
    this.frName = fileToCreate.frName || this.name;
    this.description = fileToCreate.description || faker.lorem.sentence();
    this.frDescription = fileToCreate.frDescription || this.description;
    this.filename = `${this.name}.text`;
  }
};
