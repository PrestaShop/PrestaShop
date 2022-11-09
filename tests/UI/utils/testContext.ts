import {Context} from 'mocha';
import addContext from 'mochawesome/addContext';

/**
 * @module TestContextHelper
 * @description Helper for mochawesome test context
 */
export default {
  /**
   *
   * @param testObj {context} Mocha step context
   * @param title {string} Key of the context to add
   * @param value {string} Specific context value for the step
   * @param baseContext {?string} File contest based on file location
   * @return {Promise<void>}
   */
  async addContextItem(
    testObj: Context,
    title: string,
    value: string,
    baseContext: string | undefined = undefined,
  ): Promise<void> {
    addContext(
      testObj,
      {
        title,
        value: baseContext === undefined ? value : `${baseContext}_${value}`,
      },
    );

    // Throw an error in step to not execute the rest of it
    if (global.GENERATE_FAILED_STEPS) {
      throw Error('This error is thrown to just generate a report with failed steps');
    }
  },
};
