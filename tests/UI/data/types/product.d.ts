type ProductCombinationColorSize = {
  color: string|null,
  size: string|null
};
type ProductCombinationDimension = {
  dimension: string|null
};

type Product = {
  type: string,
  productHasCombinations: boolean
  coverImage: string
  thumbImage: string
};

type ProductAttributesCommon = {
  quantity: number
  totalTaxInc?: number
}

type ProductAttributesColorSize = ProductCombinationColorSize & ProductAttributesCommon

type ProductAttributesDimension = ProductCombinationDimension & ProductAttributesCommon

type ProductDetails = ProductCombinationColorSize & {
  name: string,
  price: number
  shipping: string
  subtotal?: number
};

type ProductReview = {
  reviewTitle?: string;
  reviewContent?: string;
  reviewRating?: number;
}

export {
  Product,
  ProductAttributesColorSize,
  ProductAttributesDimension,
  ProductCombinationColorSize,
  ProductCombinationDimension,
  ProductDetails,
  ProductReview,
};
