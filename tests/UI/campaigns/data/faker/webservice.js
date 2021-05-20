const faker = require('faker');

/**
 * Create new webservice to use on webservice form on BO
 * @class
 */
class WebserviceData {
  /**
   * Constructor for class WebserviceData
   * @param webserviceToCreate {Object} Could be used to force the value of some members
   */
  constructor(webserviceToCreate = {}) {
    /** @member {string} Key of the webservice */
    this.key = webserviceToCreate.key || faker.random.uuid().substring(0, 32);

    /** @member {string} Key description of the webservice */
    this.keyDescription = webserviceToCreate.keyDescription || faker.lorem.sentence();

    /** @member {boolean} Status of the webservice */
    this.status = webserviceToCreate.status === undefined ? true : webserviceToCreate.status;
  }
}

module.exports = WebserviceData;
