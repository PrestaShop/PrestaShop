import type QuickAccessCreator from '@data/types/quickAccess';

/**
 * Create new alias to use on QuickAccess creation form on search page on BO
 * @class
 */
export default class QuickAccessData {
  public readonly name: string;

  public readonly url: string;

  public readonly openNewWindow: boolean;

  /**
   * Constructor for class QuickAccessData
   * @param valueToCreate {QuickAccessCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: QuickAccessCreator) {
    this.name = valueToCreate.name;

    this.url = valueToCreate.url;

    this.openNewWindow = valueToCreate.openNewWindow;
  }
}
