var CommonClient = require('../../../E2E/test/clients/common_client');
const exec = require('child_process').exec;

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

    clickAndWaitForDownload(selector) {
        return this.client
            .waitForVisibleAndClick(selector, 90000)
            .pause(150000)
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
}

module.exports = Installation;
