import ImageTypeData from '@data/faker/imageType';

export default {
  first: new ImageTypeData({
    id: 1,
    name: 'cart_default',
    width: 125,
    height: 125,
    productsStatus: true,
    categoriesStatus: false,
    manufacturersStatus: false,
    suppliersStatus: false,
    storesStatus: false,
  }),
  second: new ImageTypeData({
    id: 2,
    name: 'small_default',
    width: 98,
    height: 98,
    productsStatus: true,
    categoriesStatus: true,
    manufacturersStatus: true,
    suppliersStatus: true,
    storesStatus: false,
  }),
};
