// Import data
import StoreCreator from '@data/types/store';

import {faker} from '@faker-js/faker';

/**
 * Create new store to use on store creation form on BO
 * @class
 */
export default class StoreData {
  public id: number;

  public name: string;

  public address1: string;

  public address2: string;

  public postcode: string;

  public city: string;

  public state: string;

  public country: string;

  public latitude: string;

  public longitude: string;

  public phone: string;

  public fax: string;

  public registrationNumber: string;

  public email: string;

  public note: string;

  public status: boolean;

  public hours: string[];

  public picture: string|null;

  /**
   * Constructor for class StoreData
   * @param storeToCreate {StoreCreator} Could be used to force the value of some members
   */
  constructor(storeToCreate: StoreCreator = {}) {
    /** @type {number} Name of the store */
    this.id = storeToCreate.id || 0;

    /** @type {string} Name of the store */
    this.name = storeToCreate.name || faker.company.name();

    /** @type {string} First line address of the store */
    this.address1 = storeToCreate.address1 || faker.address.streetAddress();

    /** @type {string} Second line address of the store */
    this.address2 = storeToCreate.address2 || faker.address.secondaryAddress();

    /** @type {string} Postal code of the store */
    this.postcode = storeToCreate.postcode || faker.address.zipCode('#####');

    /** @type {string} City for the address of the store */
    this.city = storeToCreate.city || faker.address.city();

    /** @type {string} State for the address of the store */
    this.state = storeToCreate.state || faker.address.state();

    /** @type {string} Country of the address of the store */
    this.country = storeToCreate.country || 'France';

    /** @type {string} Latitude of the address of the store */
    this.latitude = storeToCreate.latitude || faker.address.latitude();

    /** @type {string} Longitude of the address of the store */
    this.longitude = storeToCreate.longitude || faker.address.longitude();

    /** @type {string} Phone number of the store (default format 01########) */
    this.phone = storeToCreate.phone || faker.phone.number('01########');

    /** @type {string} Fax number of the store default format 01########) */
    this.fax = storeToCreate.fax || faker.phone.number('01########');

    /** @type {string} Registration number of the store */
    this.registrationNumber = storeToCreate.registrationNumber || faker.finance.account();

    /** @type {string} Email to contact the store */
    this.email = storeToCreate.email || faker.internet.email();

    /** @type {string} Note to add information on the store */
    this.note = storeToCreate.note || faker.lorem.sentence();

    /** @type {boolean} Status of the store */
    this.status = storeToCreate.status === undefined ? true : storeToCreate.status;

    /** @type {Array<string>} Opening hours of the store */
    this.hours = storeToCreate.hours || new Array(7).fill('10:00 - 18:00', 0, 7);

    /** @type {string|null} Picture of the store */
    this.picture = storeToCreate.picture || null;
  }
}
