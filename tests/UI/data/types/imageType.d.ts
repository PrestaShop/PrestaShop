type ImageTypeCreator = {
  id?: number
  name?: string
  width?: number
  height?: number
  productsStatus?: boolean
  categoriesStatus?: boolean
  manufacturersStatus?: boolean
  suppliersStatus?: boolean
  storesStatus?: boolean
};

type ImageTypeRegenerationSpecific = 'categories'|'manufacturers'|'suppliers'|'products'|'stores';
type ImageTypeRegeneration = 'categories'|'manufacturers'|'suppliers'|'products'|'stores'|'all';

export {
  ImageTypeCreator,
  ImageTypeRegeneration,
  ImageTypeRegenerationSpecific,
};
