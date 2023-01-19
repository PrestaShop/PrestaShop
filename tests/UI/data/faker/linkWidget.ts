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
    this.name = valueToCreate.name || faker.word.noun();
    this.frName = valueToCreate.frName || this.name;
    this.hook = valueToCreate.hook || faker.helpers.arrayElement([Hooks.displayFooter]);
    this.contentPages = valueToCreate.contentPages || [];
    this.productsPages = valueToCreate.productsPages || [];
    this.staticPages = valueToCreate.staticPages || [];
    this.customPages = valueToCreate.customPages || [];
  }
}
