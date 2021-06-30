const faker = require('faker');

/**
 * Create new file to use on creation form on file page on BO
 * @class
 */
class FileData {
  /**
   * Constructor for class FileData
   * @param fileToCreate {Object} Could be used to force the value of some members
   */
  constructor(fileToCreate = {}) {
    /** @member {string} Name of the file on the list */
    this.name = fileToCreate.name || faker.system.fileName().substring(0, 32);

    /** @member {string} French name of the file */
    this.frName = fileToCreate.frName || this.name;

    /** @member {string} Description of the file */
    this.description = fileToCreate.description || faker.lorem.sentence();

    /** @member {string} French description of the file */
    this.frDescription = fileToCreate.frDescription || this.description;

    /** @member {string} Name of the file for filepath */
    this.filename = `${this.name}.text`;
  }
}

module.exports = FileData;
