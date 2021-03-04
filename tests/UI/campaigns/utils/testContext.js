const addContext = require('mochawesome/addContext');

module.exports = {
  /**
   *
   * @param testObj, mocha step object
   * @param title, context title
   * @param value, value to add
   * @param baseContext, context based on file location
   * @return {Promise<void>}
   */
  async addContextItem(testObj, title, value, baseContext = undefined) {
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
