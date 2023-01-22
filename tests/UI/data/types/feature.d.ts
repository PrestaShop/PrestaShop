import type FeatureValueData from '@data/faker/featureValue';

type FeatureCreator = {
  id?: number
  position?: number
  name?: string
  url?: string
  metaTitle?: string
  indexable?: boolean
  values?: FeatureValueData[];
};

type FeatureValueCreator = {
  id?: number
  featureName?: string;
  value?: string;
  url?: string;
  metaTitle?: string;
};

export {
  FeatureCreator,
  FeatureValueCreator,
};
