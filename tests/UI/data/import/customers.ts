import Groups from '@data/demo/groups';
import type GroupData from '@data/faker/group';
import ImportData from '@data/faker/import';
import type {ImportCustomer} from '@data/types/import';

import {faker} from '@faker-js/faker';

const groups: string[] = Object.values(Groups).map((group: GroupData) => group.name);

const records: ImportCustomer[] = [];

function createRecord(): ImportCustomer[] {
  for (let i: number = 0; i < 10; i++) {
    const lastName = faker.name.lastName();
    records.push({
      id: i + 3,
      active: faker.datatype.number({min: 0, max: 1}),
      title: faker.datatype.number({min: 1, max: 2}),
      email: `test.${lastName}@prestashop.com`,
      password: faker.internet.password(),
      birthdate: faker.date.between('1950-01-01', '2000-12-31').toISOString().slice(0, 10),
      lastName,
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

export default new ImportData({
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
});
