import Countries from '@data/demo/countries';
import States from '@data/demo/states';
import type CountryData from '@data/faker/country';
import type StateData from '@data/faker/state';
import type AddressCreator from '@data/types/address';

import {faker} from '@faker-js/faker';

const countriesNames: string[] = Object.values(Countries).map((country: CountryData) => country.name);
const statesNames: string[] = Object.values(States).map((state: StateData) => state.name);

/**
 * Create new address to use in customer address form on BO and FO
 * @class
 */
export default class AddressData {
  public readonly id: number;

  public readonly name: string;

  public readonly firstName: string;

  public readonly lastName: string;

  public readonly email: string;

  public readonly dni: string;

  public readonly alias: string;

  public readonly company: string;

  public readonly vatNumber: string;

  public readonly address: string;

  public readonly secondAddress: string;

  public readonly postalCode: string;

  public readonly city: string;

  public readonly country: string;

  public readonly state: string;

  public readonly phone: string;

  public readonly other: string;

  /**
   * Constructor for class AddressData
   * @param addressToCreate {AddressCreator} Could be used to force the value of some members
   */
  constructor(addressToCreate: AddressCreator = {}) {
    /** @type {string} Tax identification number of the customer */
    this.id = addressToCreate.id || 0;

    /** @type {string} Address Name */
    this.name = addressToCreate.name || faker.word.noun();

    /** @type {string} Customer firstname */
    this.firstName = addressToCreate.firstName || faker.person.firstName();

    /** @type {string} Customer lastname */
    this.lastName = addressToCreate.lastName || faker.person.lastName();

    /** @type {string} Related customer email */
    this.email = addressToCreate.email || faker.internet.email(
      {
        firstName: this.firstName,
        lastName: this.lastName,
        provider: 'prestashop.com',
      },
    );

    /** @type {string} Tax identification number of the customer */
    this.dni = addressToCreate.dni || '';

    /** @type {string} Address alias or name */
    this.alias = addressToCreate.alias || faker.location.streetAddress();

    /** @type {string} Company name if it's a company address */
    this.company = (addressToCreate.company || faker.company.name()).substring(0, 63);

    /** @type {string} Tax identification number if it's a company */
    this.vatNumber = addressToCreate.vatNumber || '';

    /** @type {string} Address first line */
    this.address = addressToCreate.address || faker.location.streetAddress();

    /** @type {string} Address second line */
    this.secondAddress = addressToCreate.secondAddress || faker.location.secondaryAddress();

    /** @type {string} Address postal code (default to this format #####) */
    this.postalCode = addressToCreate.postalCode || faker.location.zipCode('#####');

    /** @type {string} Address city name */
    this.city = addressToCreate.city || faker.location.city();

    /** @type {string} Address country name */
    this.country = addressToCreate.country || faker.helpers.arrayElement(countriesNames);

    /** @type {string} Address state name */
    this.state = addressToCreate.state || faker.helpers.arrayElement(statesNames);

    /** @type {string} Phone number */
    this.phone = addressToCreate.phone || faker.phone.number('01########');

    /** @type {string} Other information to add on address */
    this.other = addressToCreate.other || '';
  }
}
