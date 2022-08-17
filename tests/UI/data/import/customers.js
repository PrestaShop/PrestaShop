const {faker} = require('@faker-js/faker');
const {groupAccess} = require('@data/demo/groupAccess');

const groups = Object.values(groupAccess).map(group => group.name);

const records = [];

function createRecord() {
  for (let i = 0; i < 10; i++) {
    records.push({
      id: i + 3,
      active: faker.datatype.number({min: 0, max: 1}),
      title: faker.datatype.number({min: 1, max: 2}),
      email: `test.${this.lastName}@prestashop.com`,
      password: faker.internet.password(),
      birthdate: faker.date.between('1950-01-01', '2000-12-31').toISOString().slice(0, 10),
      lastName: faker.name.lastName(),
      firstName: faker.name.firstName(),
      newsletter: faker.datatype.number({min: 0, max: 1}),
      optIn: faker.datatype.number({min: 0, max: 1}),
      registrationDate: faker.date.past(2).toISOString().slice(0, 10),
      groups: faker.helpers.arrayElement(groups),
      defaultGroup: faker.helpers.arrayElement(groups),
    });
  }
  return records;
}

module.exports = {
  Data: {
    entity: 'Customers',
    header: [
      {id: 'id', title: 'Customer ID'},
      {id: 'active', title: 'Active (0/1)'},
      {id: 'title', title: 'Titles ID (Mr = 1, Ms = 2, else 0)'},
      {id: 'email', title: 'Email*'},
      {id: 'password', title: 'Password*'},
      {id: 'birthdate', title: 'Birthday (yyyy-mm-dd)'},
      {id: 'lastName', title: 'Last Name*'},
      {id: 'firstName', title: 'First Name*'},
      {id: 'newsletter', title: 'Newsletter (0/1)'},
      {id: 'optIn', title: 'Opt-in (0/1)'},
      {id: 'registrationDate', title: 'Registration date (yyyy-mm-dd)'},
      {id: 'groups', title: 'Groups(x,y,z...)'},
      {id: 'defaultGroup', title: 'Default group ID'},
    ],
    records: createRecord(),
  },
};
