const addContext = require('mochawesome/addContext');

module.exports = {
  /**
   *
   * @param testObj, mocha step object
   * @param title, context title
   * @param value, value to add
   * @return {Promise<void>}
   */
  async addContextItem(testObj, title, value) {
    addContext(testObj, {title, value});
  },
};
