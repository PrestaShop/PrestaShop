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
}

module.exports = Installation;
