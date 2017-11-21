'use strict';

var client;
var webdriverio = require('webdriverio');
var globals = require('./globals.webdriverio.js');

var options = {
  logLevel: 'silent',
  waitForTimeout: 30000,
  desiredCapabilities: {
    browserName: 'chrome',
  },
  port: 4444
};
if (typeof global.selenium_url !== 'undefined') {
  options.host = global.selenium_url;
}

var options2 = {
  logLevel: 'silent',
  waitForTimeout: 30000,
  desiredCapabilities: {
    browserName: 'chrome',
    'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
    username: process.env.SAUCE_USERNAME,
    access_key: process.env.SAUCE_ACCESS_KEY,
    screenResolution: "1680x1050",
    platform: "Windows 7",
  },
  port: 4445
};

function initCommands(client) {

  client.addCommand('localhost', function (cb) {
    this.selector = globals.selector;
    client
      .url('http://' + URL + 'install-dev')
      .call(cb);
  });

  client.addCommand('signinBO', function (cb) {
    this.selector = globals.selector;
    return client
      .url('http://' + URL + '/admin-dev')
      .waitForExist(this.selector.BO.AccessPage.login_input, 90000)
      .setValue(this.selector.BO.AccessPage.login_input, 'demo@prestashop.com')
      .setValue(this.selector.BO.AccessPage.password_input, 'prestashop_demo')
      .click(this.selector.BO.AccessPage.login_button)
      .waitForExist(this.selector.BO.AddProductPage.menu, 90000)
  });

  client.addCommand('onboarding', function (cb) {
    this.selector = globals.selector;
    return client
    if (this.client.isVisible(this.selector.BO.Onboarding.popup)) {
      this.client
        .click(this.selector.BO.Onboarding.popup_close_button)
        .pause(1000)
        .click(this.selector.BO.Onboarding.stop_button);
    };
    this.client.call(done);
  });

  client.addCommand('signinFO', function (cb) {
    this.selector = globals.selector;
    return client
      .url('http://' + URL)
      .waitForExist(this.selector.FO.AccessPage.sign_in_button, 90000)
      .click(this.selector.FO.AccessPage.sign_in_button)
      .waitForExist(this.selector.FO.AccessPage.login_input, 90000)
      .setValue(this.selector.FO.AccessPage.login_input, 'pub@prestashop.com')
      .setValue(this.selector.FO.AccessPage.password_input, '123456789')
      .click(this.selector.FO.AccessPage.login_button)
      .waitForExist(this.selector.FO.AccessPage.logo_home_page, 90000)
      .click(this.selector.FO.AccessPage.logo_home_page)
  });

  client.addCommand('signoutBO', function (cb) {
    this.selector = globals.selector;
    return client
      .deleteCookie()
      .call(cb);
  });

  client.addCommand('signoutFO', function (cb) {
    this.selector = globals.selector;
    return client
      .waitForExist(this.selector.FO.AccessPage.sign_out_button, 90000)
      .click(this.selector.FO.AccessPage.sign_out_button)
      .waitForExist(this.selector.FO.AccessPage.sign_in_button, 90000)
      .deleteCookie()
      .call(cb);
  });

}

module.exports = {
  getClient: function () {
    if (client) {
      return client;
    } else {
      if (typeof saucelabs !== 'undefined' && saucelabs != "None") {
        client = webdriverio
          .remote(options2)
          .init()
          .windowHandleMaximize()
      } else {
        client = webdriverio
          .remote(options)
          .windowHandleMaximize()
      }
      initCommands(client);
      return client;
    }
  },
  getCustomDate: function (numberOfDay) {
    var today = new Date();
    today.setDate(today.getDate() + numberOfDay);
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
      dd = '0' + dd;
    }

    if (mm < 10) {
      mm = '0' + mm;
    }

    today = yyyy + '-' + mm + '-' + dd;
    return today;
  },
  browser: function () {
    return options.desiredCapabilities.browserName
  }
};
