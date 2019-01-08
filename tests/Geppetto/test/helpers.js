const chai = require('chai');
chai.use(require('chai-string'));
global.expect = chai.expect;
require('./globals');

const takeScreenshot = async err => await this.client.screenshot('error').then(() => {
  throw err;
});

global.test = (name, instructions) => it(name, () => {
  return instructions().catch(takeScreenshot);
});

global.scenario = (name, tests, clientName, close = false) =>
  describe(name, () => {
    const PrestClient = require("./campaigns/clients/" + clientName);
    const client = new PrestClient();
    before(() => this.client = client);
    tests(client);
    if (close) {
      after(async () => {
        await this.client.stopTracing();
        await this.client.close();
      });
    }
  });
