import type AttributeValueData from '@data/faker/attributeValue';

type AttributeCreator = {
  id?: number
  position?: number
  name?: string
  publicName?: string
  url?: string
  metaTitle?: string
  indexable?: boolean
  displayed?: boolean
  attributeType?: string
  values?: AttributeValueData[]
};

type AttributeValueCreator = {
  id?: number
  position?: number
  attributeName?: string;
  value?: string;
  url?: string;
  metaTitle?: string;
  color?: string;
  textureFileName?: string;
};

export {
  AttributeCreator,
  AttributeValueCreator,
};
