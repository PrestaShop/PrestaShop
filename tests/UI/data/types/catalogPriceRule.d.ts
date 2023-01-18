type CatalogPriceRuleCreator = {
  name?: string
  currency?: string
  country?: string
  group?: string
  fromQuantity?: number
  fromDate?: string
  toDate?: string
  reductionType?: string
  reductionTax?: string
  reduction?: number
};

export default CatalogPriceRuleCreator;
