const { getClient } = require('.././common.webdriverio');
const { selector } = require('.././globals.webdriverio.js');

class PrestashopClient {
    constructor() {
        this.client = getClient();
    }

    loginBO(){
        return this.client.signinBO();
    }

    loginFO(){
        return this.client.signinFO();
    }

    takeScreenshot() {
        return this.client.saveScreenshot(`screenshots/${this.client.desiredCapabilities.browserName}_exception_${global.date_time}.png`);
    }

    open() {
        return this.client.init().windowHandleSize({ width: 1280, height: 1024 });
    }

    close() {
        return this.client.end();
    }
}

module.exports = PrestashopClient;
