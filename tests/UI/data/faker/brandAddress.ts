import Countries from '@data/demo/countries';
import CountryData from '@data/faker/country';
import BrandAddressCreator from '@data/types/brandAddress';

import {faker} from '@faker-js/faker';

const countriesNames: string[] = Object.values(Countries).map((country: CountryData) => country.name);

/**
 * Create new brand address to use in brand address form on BO
 * @class
 */
export default class BrandAddressData {
  public readonly id: number;

  public readonly brandName: string;

  public readonly firstName: string;

  public readonly lastName: string;

  public readonly address: string;

  public readonly secondaryAddress: string;

  public readonly postalCode: string;

  public readonly city: string;

  public readonly country: string;

  public readonly homePhone: string;

  public readonly mobilePhone: string;

  public readonly other: string;

  /**
   * Constructor for class brandAddressData
   * @param brandAddressToCreate {BrandAddressCreator} Could be used to force the value of some members
   */
  constructor(brandAddressToCreate: BrandAddressCreator = {}) {
    /** @type {number} ID */
    this.id = brandAddressToCreate.id || 0;

    /** @type {string} Associated brand to the address */
    this.brandName = brandAddressToCreate.brandName || '--';

    /** @type {string} Linked address firstname */
    this.firstName = brandAddressToCreate.firstName || faker.person.firstName();

    /** @type {string} Linked address lastname */
    this.lastName = brandAddressToCreate.lastName || faker.person.lastName();

    /** @type {string} Address first line */
    this.address = brandAddressToCreate.address || faker.location.streetAddress();

    /** @type {string} Address second line */
    this.secondaryAddress = brandAddressToCreate.secondaryAddress || faker.location.secondaryAddress();

    /** @type {string} Address postal code (default to this format #####) */
    this.postalCode = brandAddressToCreate.postalCode || faker.location.zipCode();

    /** @type {string} Address city name */
    this.city = brandAddressToCreate.city || faker.location.city();

    /** @type {string} Address country name */
    this.country = brandAddressToCreate.country || faker.helpers.arrayElement(countriesNames);

    /** @type {string} Home phone number linked to the address */
    this.homePhone = brandAddressToCreate.homePhone || faker.phone.number('01########');

    /** @type {string} Mobile phone number linked to the address */
    this.mobilePhone = brandAddressToCreate.mobilePhone || faker.phone.number('06########');

    /** @type {string} Other information to add on address */
    this.other = brandAddressToCreate.other || '';
  }
}
