const faker = require('faker');

const {Attributes} = require('@data/demo/attributes');

const attributesNames = Object.values(Attributes).map(attribute => attribute.name);

const attributeTypes = ['Drop-down list', 'Radio buttons', 'Color or texture'];

module.exports = {
  Attribute: class Attribute {
    constructor(attributeToCreate = {}) {
      this.name = attributeToCreate.name || faker.lorem.word();
      this.publicName = attributeToCreate.publicName || this.name;
      this.url = attributeToCreate.url || this.name;// .replaceAll(' ', '-').trim();
      this.metaTitle = attributeToCreate.metaTitle || faker.lorem.word();
      this.indexable = attributeToCreate.indexable === undefined ? true : attributeToCreate.indexable;
      this.attributeType = attributeToCreate.attributeType || faker.random.arrayElement(attributeTypes);
    }
  },
  Value: class Value {
    constructor(valueToCreate = {}) {
      this.attributeName = valueToCreate.attributeName || faker.random.arrayElement(attributesNames);
      this.value = valueToCreate.value || faker.commerce.productMaterial();
      this.url = valueToCreate.url || this.value;// .replaceAll(' ', '-').trim();
      this.metaTitle = valueToCreate.metaTitle || faker.lorem.word();
      this.color = valueToCreate.color || faker.internet.color();
      this.textureFileName = valueToCreate.textureFileName || faker.system.commonFileName('txt');
    }
  },
};
