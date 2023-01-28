import type SearchEngineCreator from '@data/types/searchEngine';

import {faker} from '@faker-js/faker';

/**
 * Create new search engine to use on search engine creation form on BO
 * @class
 */
export default class SearchEngineData {
  public readonly id: number;

  public readonly server: string;

  public readonly queryKey: string;

  /**
   * Constructor for class SearchEngineData
   * @param searchEngineToCreate {SearchEngineCreator} Could be used to force the value of some members
   */
  constructor(searchEngineToCreate: SearchEngineCreator = {}) {
    /** @type {number} ID of the engine */
    this.id = searchEngineToCreate.id || 0;

    /** @type {string} Server of the engine */
    this.server = searchEngineToCreate.server || `test_${faker.internet.domainWord()}`;

    /** @type {string} Key to use on the search */
    this.queryKey = searchEngineToCreate.queryKey || 'qTest_';
  }
}
