type ProductCombination = {
  color: string|null,
  size: string|null
};

type Product = {
  type: string,
  productHasCombinations: boolean
  coverImage: string
  thumbImage: string
};

type ProductAttributes = {
  size: string
  color: string
  quantity: number
  totalTaxInc?: number
}

type ProductDetails = ProductAttributes & {
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
  ProductAttributes,
  ProductCombination,
  ProductDetails,
  ProductReview,
};
