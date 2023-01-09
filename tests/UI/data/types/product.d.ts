type ProductAttribute = {
  name: string
  value: string
};

type ProductAttributes = {
  name: string
  values: string[]
};

type ProductCreator = {
  id?: number
  name?: string
  nameFR?: string
  defaultImage?: string|null
  coverImage?: string|null
  thumbImage?: string|null
  thumbImageFR?: string|null
  category?: string
  type?: string
  status?: boolean
  summary?: string
  description?: string
  reference?: string
  quantity?: number
  tax?: number
  price?: number
  retailPrice?: number
  finalPrice?: number
  priceTaxExcluded?: number
  productHasCombinations?: boolean
  attributes?: ProductAttributes[]
  pack?: ProductPackItem[]
  taxRule?: string
  ecoTax?: number
  specificPrice?: ProductSpecificPrice
  minimumQuantity?: number
  stockLocation?: string
  lowStockLevel?: number
  labelWhenInStock?: string
  labelWhenOutOfStock?: string
  behaviourOutOfStock?: string
  customization?: ProductCustomization
  downloadFile?: boolean
  fileName?: string
  allowedDownload?: number
  weight?: number
  combinations?: ProductCombination[]
};

type ProductCombination = {
  name: string
  price: number
};

type ProductCustomization = {
  label: string
  type: string
  required: boolean
};

type ProductDetails = {
  name: string
  price: number
  summary: string
  description: string
  shipping?: string
  subtotal?: number
};

type ProductPackItem = {
  reference: string
  quantity: number
};

type ProductReviewCreator = {
  reviewTitle?: string;
  reviewContent?: string;
  reviewRating?: number;
};

type ProductSpecificPrice = {
  attributes: string|null
  discount: number
  startingAt: number
  reductionType: string
};

export {
  ProductAttribute,
  ProductAttributes,
  ProductCombination,
  ProductCreator,
  ProductCustomization,
  ProductDetails,
  ProductPackItem,
  ProductReviewCreator,
  ProductSpecificPrice,
};
