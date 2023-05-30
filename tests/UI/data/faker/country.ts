import Currencies from '@data/demo/currencies';
import Zones from '@data/demo/zones';
import CurrencyData from '@data/faker/currency';
import ZoneData from '@data/faker/zone';
import CountryCreator from '@data/types/country';

import {faker} from '@faker-js/faker';

const zones: string[] = Object.values(Zones).map((zone: ZoneData) => zone.name);
const currencies: string[] = Object.values(Currencies).map((currency: CurrencyData) => currency.name);

/**
 * Create new country to use on creation form on country page on BO
 * @class
 */
export default class CountryData {
  public readonly id: number;

  public readonly name: string;

  public readonly isoCode: string;

  public readonly callPrefix: string;

  public readonly currency: string;

  public readonly zone: string;

  public readonly needZipCode: boolean;

  public readonly zipCodeFormat: string;

  public readonly active: boolean;

  public readonly containsStates: boolean;

  public readonly needIdentificationNumber: boolean;

  public readonly displayTaxNumber: boolean;

  /**
   * Constructor for class CountryData
   * @param countryToCreate {CountryCreator} Could be used to force the value of some members
   */
  constructor(countryToCreate: CountryCreator = {}) {
    /** @type {number} ID of the country */
    this.id = countryToCreate.id || 0;

    /** @type {string} Name of the country */
    this.name = countryToCreate.name || `test${faker.address.country()}`;

    /** @type {string} Country iso code */
    this.isoCode = countryToCreate.isoCode || faker.address.countryCode();

    /** @type {string} Country call Prefix */
    this.callPrefix = countryToCreate.callPrefix || '0';

    /** @type {string} Currency used in the country */
    this.currency = countryToCreate.currency || faker.helpers.arrayElement(currencies);

    /** @type {string} In which zone the country belongs */
    this.zone = countryToCreate.zone || faker.helpers.arrayElement(zones);

    /** @type {boolean} True if the country used zip codes */
    this.needZipCode = countryToCreate.needZipCode === undefined ? false : countryToCreate.needZipCode;

    /** @type {string} Format of the zip code if used */
    this.zipCodeFormat = countryToCreate.zipCodeFormat || '';

    /** @type {string} Status of the country */
    this.active = countryToCreate.active === undefined ? false : countryToCreate.active;

    /** @type {boolean} True if the country have states */
    this.containsStates = countryToCreate.containsStates === undefined ? false : countryToCreate.containsStates;

    /** @type {boolean} True if need identification number for the country */
    this.needIdentificationNumber = countryToCreate.needIdentificationNumber === undefined
      ? false : countryToCreate.needIdentificationNumber;

    /** @type {boolean} True to display tax number when located on the country */
    this.displayTaxNumber = countryToCreate.displayTaxNumber === undefined ? false : countryToCreate.displayTaxNumber;
  }
}
