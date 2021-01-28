const faker = require('faker');

const subject = ['Customer service', 'Webmaster'];

module.exports = class Contact {
  constructor(messageToCreate = {}) {
    this.subject = messageToCreate.subject || faker.random.arrayElement(subject);
    this.firstName = messageToCreate.firstName || faker.name.firstName();
    this.lastName = messageToCreate.lastName || faker.name.lastName();
    this.emailAddress = messageToCreate.emailAddress
      || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.fileName = faker.lorem.word();
    this.message = faker.lorem.sentence();
  }
};
