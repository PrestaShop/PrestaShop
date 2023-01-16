type ProductCombinationColorSize = {
  color: string|null,
  size: string|null
};
type ProductCombinationDimension = {
  dimension: string|null
};

type Product = {
  name: string
  type: string
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

type ProductDetailsBasic = {
  name: string
  price: number
  shortDescription: string
  description: string
  shipping?: string
  subtotal?: number
}

type ProductDetails = ProductCombinationColorSize & ProductDetailsBasic;

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
  ProductDetailsBasic,
  ProductReview,
};
