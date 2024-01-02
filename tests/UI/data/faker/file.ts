import FileCreator from '@data/types/file';

import {faker} from '@faker-js/faker';

/**
 * Create new file to use on creation form on file page on BO
 * @class
 */
export default class FileData {
  public readonly name: string;

  public readonly frName: string;

  public readonly description: string;

  public readonly frDescription: string;

  public readonly filename: string;

  /**
   * Constructor for class FileData
   * @param fileToCreate {FileCreator} Could be used to force the value of some members
   */
  constructor(fileToCreate: FileCreator = {}) {
    /** @type {string} Name of the file on the list */
    this.name = fileToCreate.name || faker.system.fileName().substring(0, 32);

    /** @type {string} French name of the file */
    this.frName = fileToCreate.frName || this.name;

    /** @type {string} Description of the file */
    this.description = fileToCreate.description || faker.lorem.sentence();

    /** @type {string} French description of the file */
    this.frDescription = fileToCreate.frDescription || this.description;

    /** @type {string} Name of the file for the filepath */
    this.filename = fileToCreate.filename || `${this.name}.txt`;
  }
}
