const faker = require('faker');

module.exports = class Webservice {
  constructor(webserviceToCreate = {}) {
    this.keyDescription = webserviceToCreate.keyDescription || faker.lorem.sentence();
    this.status = webserviceToCreate.status === undefined ? true : webserviceToCreate.status;
  }
};
