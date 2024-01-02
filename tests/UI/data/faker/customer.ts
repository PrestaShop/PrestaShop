import Groups from '@data/demo/groups';
import Titles from '@data/demo/titles';
import type GroupData from '@data/faker/group';
import type TitleData from '@data/faker/title';
import type CustomerCreator from '@data/types/customer';

import {faker} from '@faker-js/faker';

const genders: string[] = Object.values(Titles).map((title: TitleData) => title.name);
const groups: string[] = Object.values(Groups).map((group: GroupData) => group.name);
const risksRating: string[] = ['None', 'Low', 'Medium', 'High'];

/**
 * Create new customer to use on creation form on customer page on BO and FO
 * @class
 */
export default class CustomerData {
  public readonly id: number;

  public readonly socialTitle: string;

  public readonly firstName: string;

  public readonly lastName: string;

  public email: string;

  public password: string;

  public readonly birthDate: Date;

  public readonly yearOfBirth: string;

  public readonly monthOfBirth: string;

  public readonly dayOfBirth: string;

  public readonly enabled: boolean;

  public readonly partnerOffers: boolean;

  public readonly newsletter: boolean;

  public readonly defaultCustomerGroup: string;

  public readonly company: string;

  public readonly allowedOutstandingAmount: number;

  public readonly riskRating: string;

  /**
   * Constructor for class CustomerData
   * @param customerToCreate {CustomerCreator} Could be used to force the value of some members
   */
  constructor(customerToCreate: CustomerCreator = {}) {
    /** @type {number} ID of the customer */
    this.id = customerToCreate.id || 0;

    /** @type {string} Social title of the customer (Mr, Mrs) */
    this.socialTitle = customerToCreate.socialTitle || faker.helpers.arrayElement(genders);

    /** @type {string} Firstname of the customer */
    this.firstName = customerToCreate.firstName || faker.person.firstName();

    /** @type {string} Lastname of the customer */
    this.lastName = customerToCreate.lastName || faker.person.lastName();

    /** @type {string} Email for the customer account */
    this.email = customerToCreate.email || faker.internet.email(
      {
        firstName: this.firstName,
        lastName: this.lastName,
        provider: 'prestashop.com',
      },
    );

    /** @type {string} Password for the customer account */
    this.password = customerToCreate.password === undefined ? faker.internet.password() : customerToCreate.password;

    /** @type {Date} Birthdate of the customer */
    this.birthDate = customerToCreate.birthDate || faker.date.between({from: '1950-01-01', to: '2000-12-31'});

    /** @type {string} Year of the birth 'yyyy' */
    this.yearOfBirth = customerToCreate.yearOfBirth || this.birthDate.getFullYear().toString();

    /** @type {string} Month of the birth 'mm' */
    this.monthOfBirth = customerToCreate.monthOfBirth || (`0${this.birthDate.getMonth() + 1}`).slice(-2);

    /** @type {string} Day of the birth 'dd'  */
    this.dayOfBirth = customerToCreate.dayOfBirth || (`0${this.birthDate.getDate()}`).slice(-2).toString();

    /** @type {boolean} Status of the customer */
    this.enabled = customerToCreate.enabled === undefined ? true : customerToCreate.enabled;

    /** @type {boolean} True to enable partner offers */
    this.partnerOffers = customerToCreate.partnerOffers === undefined ? true : customerToCreate.partnerOffers;

    /** @type {string} Default group for the customer */
    this.defaultCustomerGroup = customerToCreate.defaultCustomerGroup || faker.helpers.arrayElement(groups);

    /** @type {boolean} True to enable sending newsletter to the customer */
    this.newsletter = customerToCreate.newsletter === undefined ? false : customerToCreate.newsletter;

    /** @type {string} Company for the customer */
    this.company = customerToCreate.company || faker.company.name();

    /** @type {Number} Allowed outstanding amount for the customer */
    this.allowedOutstandingAmount = customerToCreate.allowedOutstandingAmount || faker.number.int({
      min: 0,
      max: 100,
    });

    /** @type {string} Risk rating for the customer */
    this.riskRating = customerToCreate.riskRating || faker.helpers.arrayElement(risksRating);
  }
}
