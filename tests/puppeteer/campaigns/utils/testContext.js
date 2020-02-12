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
  },
};
