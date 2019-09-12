var CommonClient = require('./common_client');

class AttributeAndFeature extends CommonClient {

  clickOnAction(groupActionSelector, actionSelector, action = 'edit') {
    if (action === 'delete') {
      return this.client
        .waitForExistAndClick(groupActionSelector)
        .waitForExistAndClick(actionSelector)
        .alertAccept()
    } else {
      return this.client
        .waitForExistAndClick(groupActionSelector)
        .waitForExistAndClick(actionSelector)
    }
  }

  checkDeleted(selector) {
    return this.client
      .pause(3000)
      .then(() => this.client.isExisting(selector))
      .then((value) => expect(value).to.be.false);
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
