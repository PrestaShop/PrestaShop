import ImportData from '@data/faker/import';
import type {ImportBrand} from '@data/types/import';

import {faker} from '@faker-js/faker';

const records: ImportBrand[] = [];

function createRecord(): ImportBrand[] {
  for (let i: number = 0; i < 10; i++) {
    const name = `todelete ${faker.company.name()}`;
    records.push({
      id: i + 2,
      active: faker.number.int({min: 0, max: 1}),
      name,
      description: faker.lorem.sentence(),
      shortDescription: faker.lorem.sentence(),
      metaTitle: name,
      metaKeywords: [faker.lorem.word(), faker.lorem.word()],
      metaDescription: faker.lorem.sentence(),
      imageURL: '',
    });
  }

  return records;
}

export default new ImportData({
  entity: 'Brands',
  header: [
    {id: 'id', title: 'ID'},
    {id: 'active', title: 'Active (0/1)'},
    {id: 'name', title: 'Name *'},
    {id: 'description', title: 'Description'},
    {id: 'shortDescription', title: 'Short description'},
    {id: 'metaTitle', title: 'Meta title'},
    {id: 'metaKeywords', title: 'Meta keywords'},
    {id: 'metaDescription', title: 'Meta description'},
    {id: 'imageURL', title: 'Image URL'},
  ],
  records: createRecord(),
});
