const {faker} = require('@faker-js/faker');

const records = [];

function createRecord() {
  for (let i = 0; i < 10; i++) {
    records.push({
      id: i + 2,
      active: faker.datatype.number({min: 0, max: 1}),
      name: `todelete ${faker.company.companyName()}`,
      description: faker.lorem.sentence(),
      shortDescription: faker.lorem.sentence(),
      metaTitle: this.name,
      metaKeywords: [faker.lorem.word(), faker.lorem.word()],
      metaDescription: faker.lorem.sentence(),
      imageURL: '',
    });
  }
  return records;
}

module.exports = {
  Data: {
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
  },
};
