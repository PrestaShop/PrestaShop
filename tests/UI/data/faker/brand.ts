import BrandCreator from '@data/types/brand';

import {faker} from '@faker-js/faker';

/**
 * Create new brand to use in brand form on BO
 * @class
 */
export default class BrandData {
  public readonly id: number;

  public readonly name: string;

  public readonly logo: string;

  public readonly shortDescription: string;

  public readonly shortDescriptionFr: string;

  public readonly description: string;

  public readonly descriptionFr: string;

  public readonly metaTitle: string;

  public readonly metaTitleFr: string;

  public readonly metaDescription: string;

  public readonly metaDescriptionFr: string;

  public readonly metaKeywords: string[];

  public readonly metaKeywordsFr: string[];

  public readonly enabled: boolean;

  public addresses: number;

  public readonly products: number;

  /**
   * Constructor for class BrandData
   * @param brandToCreate {BrandCreator} Could be used to force the value of some members
   */
  constructor(brandToCreate: BrandCreator = {}) {
    /** @type {number} ID  of the brand */
    this.id = brandToCreate.id || 0;

    /** @type {string} Name of the brand */
    this.name = brandToCreate.name || faker.company.name();

    /** @type {string} Logo name of the brand */
    this.logo = brandToCreate.logo || `${this.name.replace(/[^\w\s]/gi, '')}.png`;

    /** @type {string} Short description of the brand */
    this.shortDescription = brandToCreate.shortDescription || faker.lorem.sentence();

    /** @type {string} French short description of the brand */
    this.shortDescriptionFr = brandToCreate.shortDescriptionFr || this.shortDescription;

    /** @type {string} Description of the brand */
    this.description = brandToCreate.description || faker.lorem.sentence();

    /** @type {string} French description of the brand */
    this.descriptionFr = brandToCreate.descriptionFr || this.description;

    /** @type {string} Meta title of the brand */
    this.metaTitle = brandToCreate.metaTitle || this.name;

    /** @type {string} French meta title of the brand */
    this.metaTitleFr = brandToCreate.metaTitleFr || this.metaTitle;

    /** @type {string} Meta description of the brand */
    this.metaDescription = brandToCreate.metaDescription || faker.lorem.sentence();

    /** @type {string} French meta description of the brand */
    this.metaDescriptionFr = brandToCreate.metaDescriptionFr || this.metaDescription;

    /** @type {Array<string>} Meta Keywords of the brand */
    this.metaKeywords = brandToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];

    /** @type {Array<string>} French meta keywords of the brand */
    this.metaKeywordsFr = brandToCreate.metaKeywordsFr || this.metaKeywords;

    /** @type {boolean} Status of the brand */
    this.enabled = brandToCreate.enabled === undefined ? true : brandToCreate.enabled;

    /** @type {number} How much addresses has the brand */
    this.addresses = brandToCreate.addresses || 0;

    /** @type {number} How much products has the brand */
    this.products = brandToCreate.products || 0;
  }
}
