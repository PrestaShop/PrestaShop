import SupplierData from '@data/faker/supplier';

export default {
  fashionSupplier: new SupplierData({
    id: 1,
    name: 'Fashion supplier',
    products: 17,
    enabled: true,
  }),
  accessoriesSupplier: new SupplierData({
    id: 2,
    name: 'Accessories supplier',
    products: 5,
    enabled: true,
  }),
};
