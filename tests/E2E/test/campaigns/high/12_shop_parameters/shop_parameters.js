module.exports = {
  clickOnMenuLinksAndCheckElement: function (client, mainMenu, subMenu, pageSelector, describe1 = "", describe2 = "", pause = 0) {
    var page = describe2 === "" ? describe1 : describe2;
    if (mainMenu === "") {
      test('should click on "' + describe1 + '" menu', () => client.waitForExistAndClick(subMenu));
    } else {
      test('should click on "' + describe1 + '" menu', () => client.goToSubtabMenuPage(mainMenu, subMenu));
    }
    test('should check that the "' + page + '" page is well opened', () => client.isExisting(pageSelector, pause));
  }
};
