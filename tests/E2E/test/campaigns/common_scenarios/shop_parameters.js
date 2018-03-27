let promise = Promise.resolve();
module.exports = {
  clickOnMenuLinksAndCheckElement: function (client, mainMenu, subMenu, pageSelector, describe1 = "", describe2 = "", pause = 0, tabMenu = "") {
    var page = describe2 === "" ? describe1 : describe2;
    if (mainMenu === "") {
      test('should click on "' + describe1 + '" menu', () => client.waitForExistAndClick(subMenu));
    } else {
      test('should click on "' + describe1 + '" menu', () => {
        return promise
          .then(() => client.waitForExist(mainMenu, 90000))
          .then(() => client.moveToObject(mainMenu))
          .then(() => client.waitForVisibleAndClick(subMenu))
      });
    }
    if (tabMenu !== "") {
      test('should click on "' + describe2 + '" tab', () => client.waitForExistAndClick(tabMenu));
    }
    test('should check that the "' + page + '" page is well opened', () => client.isExisting(pageSelector, pause));
  }
};
