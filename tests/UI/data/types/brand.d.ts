type BrandCreator = {
  id?: number
  name?: string
  logo?: string
  shortDescription?: string
  shortDescriptionFr?: string
  description?: string
  descriptionFr?: string
  metaTitle?: string
  metaTitleFr?: string
  metaDescription?: string
  metaDescriptionFr?: string
  metaKeywords?: string[]
  metaKeywordsFr?: string[]
  enabled?: boolean
  addresses?: number
  products?: number
};

export default BrandCreator;
