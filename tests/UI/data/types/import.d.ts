type ImportAddress = {
  id: number
  alias: string
  active: number
  email: string
  customerID: number
  manufacturer: string
  supplier: string
  company: string
  lastname: string
  firstname: string
  address1: string
  address2: string
  zipCode: string
  city: string
  country: string
  state: string
  other: string
  phone: string
  mobilePhone: string
  vatNumber: string
  dni: string
};

type ImportBrand = {
  id: number
  active: number
  name: string
  description: string
  shortDescription: string
  metaTitle: string
  metaKeywords: string[]
  metaDescription: string
  imageURL: string
};

type ImportCategory = {
  id: number
  active: number
  name: string
  parent_category: string
  root_category: string
  description: string
};

type ImportCombination = {
  id: number
  reference: string
  attribute: string
  value: string
};

type ImportCreator = {
  entity: string
  header: ImportHeaderItem[]
  records: ImportAddress[]|ImportBrand[]|ImportCategory[]|ImportCombination[]|ImportCustomer[]|ImportProduct[]
}

type ImportCustomer = {
  id: number
  active: number
  title: number
  email: string
  password: string
  birthdate: string
  lastName: string
  firstName: string
  newsletter: number
  optIn: number
  registrationDate: string
  groups: string
  defaultGroup: string
};

type ImportHeaderItem = {
  id: string
  title: string
}

type ImportProduct = {
  id: number
  active: number
  name: string
  categories: string
  price_TEXC: string
  tax_rule_id: string
  cost_price: string
  on_sale: string
  discount_amount: string
  discount_percent: string
  discount_from: string
  discount_to: string
  reference: string
  supplier_reference: string
  supplier: string
  brand: string
  ean13: string
  upc: string
  value: string
  mpn: string
  width: string
  height: string
  depth: string
  weight: string
  delivery_time_in_stock: string
  delivery_time_out_of_stock: string
  quantity: number
};

export {
  ImportAddress,
  ImportBrand,
  ImportCategory,
  ImportCombination,
  ImportCreator,
  ImportCustomer,
  ImportHeaderItem,
  ImportProduct,
};
