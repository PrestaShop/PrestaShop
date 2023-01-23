type TaxOptionCreator = {
  enabled?: boolean
  displayInShoppingCart?: boolean
  basedOn?: string
  useEcoTax?: boolean
  ecoTax?: string|null
};

export default TaxOptionCreator;
