var CommonClient = require('../../../E2E/test/clients/common_client');
const exec = require('child_process').exec;
var path = require('path');

class Installation extends CommonClient {

  setNameInput(selector, data) {
    return this.client
      .waitForVisible(selector, 90000)
      .pause(2000)
      .setValue(selector, data)
  }

  goToTheNextPage(selector) {
    return this.client
      .pause(2000)
      .waitForVisibleAndClick(selector, 90000)
  }

  dataBaseCreation(selector) {
    return this.client
      .waitForVisible(selector, 90000)
      .pause(2000)
      .click(selector)
  }

  WaitForDownload(selector) {
    return this.client
      .pause(150000)
  }

  renameFolders(rcTarget) {
    const renameAdmin = exec(' mv ' + rcTarget + 'admin' + ' ' + rcTarget + 'admin-dev',
      (error, stdout, stderr) => {
        if (error !== null) {
          console.log(`exec error: ${error}`);
        }
      });
    const renameInstall = exec(' mv ' + rcTarget + 'install' + ' ' + rcTarget + 'install-dev',
      (error, stdout, stderr) => {
        if (error !== null) {
          console.log(`exec error: ${error}`);
        }
      });
    return this.client
      .pause(2000)
  }

  copyFileToAutoUpgrade(downloadsFolderPath, filename, rcTarget) {
    const child = exec(' cp ' + downloadsFolderPath + filename + ' ' + rcTarget,
      (error, stdout, stderr) => {
        if (error !== null) {
          console.log(`exec error: ${error}`);
        }
      });
    return this.client
      .pause(3000)
      .refresh();
  }

  getRCName(chaine) {
    global.filename = chaine.split('/')[chaine.split('/').length - 1];
    return this.client.pause(1000)
  }

  checkDefaultConfiguration(selector, textToCheckWith, type, pause = 0) {
    switch (type) {
      case "language":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect((text.substr(0, 2)).toLowerCase()).to.equal(textToCheckWith.toLowerCase()));
        break;
      case "country":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text.toLowerCase()).to.equal(textToCheckWith.toLowerCase()));
        break;
      case "currency":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => {
            let position = text.indexOf('(');
            expect(text.substr(position + 1, 3).toLowerCase()).to.deep.equal(textToCheckWith.toLowerCase())
          });
        break;
    }

  }
}

module.exports = Installation;
