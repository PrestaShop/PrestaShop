var CommonClient = require('./common_client');

class Design extends CommonClient {

  getCategoryID(selector, pos) {
    if (global.isVisible) {
      return this.client
        .getText(selector.replace('%ID', pos))
        .then((text) => global.categoryID = text)
    } else {
      return this.client
        .getText(selector.replace('%ID', pos - 1))
        .then((text) => global.categoryID = text)
    }
  }
}

module.exports = Design;