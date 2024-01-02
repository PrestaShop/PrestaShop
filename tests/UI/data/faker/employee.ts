import Languages from '@data/demo/languages';
import Pages from '@data/demo/pages';
import Roles from '@data/demo/roles';
import LanguageData from '@data/faker/language';
import RoleData from '@data/faker/role';
import {EmployeeCreator} from '@data/types/employee';

import {faker} from '@faker-js/faker';

const profileNames: string[] = Object.values(Roles).map((profile: RoleData) => profile.name);
const languagesNames: string[] = Object.values(Languages).map((lang: LanguageData) => lang.name);

/**
 * Create new employee to use on creation form on employee page on BO
 * @class
 */
export default class EmployeeData {
  public readonly id: number;

  public firstName: string;

  public lastName: string;

  public email: string;

  public password: string;

  public defaultPage: string;

  public language: string;

  public readonly active: boolean;

  public readonly permissionProfile: string;

  public avatarFile: string|null;

  public enableGravatar: boolean;

  /**
   * Constructor for class EmployeeData
   * @param employeeToCreate {EmployeeCreator} Could be used to force the value of some members
   */
  constructor(employeeToCreate: EmployeeCreator = {}) {
    /** @type {number} Employee ID */
    this.id = employeeToCreate.id || 0;

    /** @type {string} Employee firstname */
    this.firstName = employeeToCreate.firstName || faker.person.firstName();

    /** @type {string} Employee lastname */
    this.lastName = employeeToCreate.lastName || faker.person.lastName().replace('\'', '');

    /** @type {string} Email of the employee */
    this.email = employeeToCreate.email || faker.internet.email(
      {
        firstName: this.firstName,
        lastName: this.lastName,
        provider: 'prestashop.com',
      },
    );

    /** @type {string} Password for the employee account */
    this.password = employeeToCreate.password || 'prestashop_demo';

    /** @type {string} Default page where employee should access after login */
    this.defaultPage = employeeToCreate.defaultPage || faker.helpers.arrayElement(Pages.Pages);

    /** @type {string} Default BO language for the employee */
    this.language = employeeToCreate.language
      || faker.helpers.arrayElement(languagesNames.slice(0, 2));

    /** @type {boolean} Status of the employee */
    this.active = employeeToCreate.active === undefined ? true : employeeToCreate.active;

    /** @type {string} Permission profile to set on the employee */
    this.permissionProfile = employeeToCreate.permissionProfile || faker.helpers.arrayElement(profileNames);

    /** @type {string|null} Path of the avatar of the employee */
    this.avatarFile = employeeToCreate.avatarFile || null;

    /** @type {boolean} Enable Gravatar */
    this.enableGravatar = employeeToCreate.enableGravatar === undefined ? false : employeeToCreate.enableGravatar;
  }
}
