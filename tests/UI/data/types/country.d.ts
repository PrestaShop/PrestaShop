type CountryCreator = {
  id?: number
  name?: string
  isoCode?: string
  callPrefix?: string
  currency?: string
  zone?: string
  needZipCode?: boolean
  zipCodeFormat?: string
  active?: boolean
  containsStates?: boolean
  needIdentificationNumber?: boolean
  displayTaxNumber?: boolean
};

export default CountryCreator;
