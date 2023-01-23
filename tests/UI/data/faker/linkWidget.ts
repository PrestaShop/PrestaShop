import Hooks from '@data/demo/hooks';
import HookData from '@data/faker/hook';
import {LinkWidgetCreator, LinkWidgetPage} from '@data/types/linkWidget';

import {faker} from '@faker-js/faker';

/**
 * @class
 */
export default class LinkWidgetData {
  public readonly name: string;

  public readonly frName: string;

  public readonly hook: HookData;

  public readonly contentPages: string[];

  public readonly productsPages: string[];

  public readonly staticPages: string[];

  public readonly customPages: LinkWidgetPage[];

  /**
   * Constructor for class HookData
   * @param valueToCreate {HookCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: LinkWidgetCreator = {}) {
    /** @type {string} */
    this.name = valueToCreate.name || faker.word.noun();

    /** @type {string} */
    this.frName = valueToCreate.frName || this.name;

    /** @type {HookData} */
    this.hook = valueToCreate.hook || faker.helpers.arrayElement([Hooks.displayFooter]);

    /** @type {string[]} */
    this.contentPages = valueToCreate.contentPages || [];

    /** @type {string[]} */
    this.productsPages = valueToCreate.productsPages || [];

    /** @type {string[]} */
    this.staticPages = valueToCreate.staticPages || [];

    /** @type {LinkWidgetPage[]} */
    this.customPages = valueToCreate.customPages || [];
  }
}
