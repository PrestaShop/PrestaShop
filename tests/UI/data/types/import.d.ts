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
  records: ImportAddress[]|ImportBrand[]|ImportCategory[]|ImportCombination[]
}

type ImportHeaderItem = {
  id: string
  title: string
}

export {
  ImportAddress,
  ImportBrand,
  ImportCategory,
  ImportCombination,
  ImportCreator,
  ImportHeaderItem,
};
