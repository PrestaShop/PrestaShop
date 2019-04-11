var CommonClient = require('./common_client');

class AttributeAndFeature extends CommonClient {

  async clickOnAction(groupActionSelector, actionSelector, action = 'edit') {
    if (action === 'delete') {
      await this.waitForExistAndClick(groupActionSelector);
      if(!global.alertAccept) await this.alertAccept();
      await this.waitForExistAndClick(actionSelector);
    } else {
      await this.waitForExistAndClick(groupActionSelector);
      await this.waitForExistAndClick(actionSelector)
    }
  }

  checkDeleted(selector) {

    return this
      .isNotExisting(selector)
      .then((value) => expect(value).to.be.true);
  }

  checkOneExistence(valueToCheckWith, column = 'id') {
    let occurence = 0;
    for (let i = 0; i < elementsTable.length; i++) {
      if (elementsTable[i] === valueToCheckWith) {
        occurence = occurence + 1;
      }
    }
    if (column === 'position') {
      expect(occurence, 'there is no records or more than one having position 2').to.be.equal(1);
    } else {
      expect(occurence, 'there is no records or more than one having id 3').to.be.equal(1);
    }
  }
}

module.exports = AttributeAndFeature;
