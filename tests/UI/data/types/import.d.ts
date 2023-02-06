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

type ImportCreator = {
  entity: string
  header: ImportHeaderItem[]
  records: ImportAddress[]|ImportBrand[]
}

type ImportHeaderItem = {
  id: string
  title: string
}

export {
  ImportAddress,
  ImportBrand,
  ImportCreator,
  ImportHeaderItem,
};
