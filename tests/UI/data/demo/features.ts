import FeatureData from '@data/faker/feature';
import FeatureValueData from '@data/faker/featureValue';

export default {
  composition: new FeatureData({
    id: 1,
    position: 1,
    name: 'Composition',
    values: [
      // 0 : polyester
      new FeatureValueData({
        id: 1,
        value: 'Polyester',
      }),
      // 1 : wool
      new FeatureValueData({
        id: 2,
        value: 'Wool',
      }),
      // 2 : ceramic
      new FeatureValueData({
        id: 3,
        value: 'Ceramic',
      }),
      // 3 : cotton
      new FeatureValueData({
        id: 4,
        value: 'Cotton',
      }),
      // 4 : recycledCardboard
      new FeatureValueData({
        id: 5,
        value: 'Recycled cardboard',
      }),
      // 5 : mattPaper
      new FeatureValueData({
        id: 6,
        value: 'Matt paper',
      }),
    ],
  }),
  property: new FeatureData({
    id: 2,
    position: 2,
    name: 'Property',
    values: [],
  }),
};
