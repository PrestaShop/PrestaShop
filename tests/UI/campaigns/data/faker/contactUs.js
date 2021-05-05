const faker = require('faker');

const subject = ['Customer service', 'Webmaster'];

module.exports = class ContactUs {
  constructor(messageToCreate = {}) {
    this.subject = messageToCreate.subject || faker.random.arrayElement(subject);
    this.firstName = messageToCreate.firstName || faker.name.firstName();
    this.lastName = messageToCreate.lastName || faker.name.lastName();
    this.emailAddress = messageToCreate.emailAddress
      || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    this.reference = messageToCreate.reference;
    this.fileName = faker.lorem.word();
    this.message = faker.lorem.sentence().substring(0, 35).trim();
  }
};
