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


  client.addCommand('waitForExistAndClick', function (selector , timeout=90000) {
    return client
      .waitForExist(selector , timeout)
      .click(selector)
  });

  client.addCommand('waitAndSetValue', function (selector, value, timeout=90000) {
    return client
      .waitForExist(selector , timeout)
      .setValue(selector, value)
  });

  client.addCommand('scrollTo', function (selector, margin = 150) {
      return client
        .getLocation(selector, 'y')
        .then((location) => client.scroll(0, location - margin));
  });

  client.addCommand('waitForVisibleAndClick', function (selector, timeout=90000) {
    return client
      .waitForVisible(selector, timeout)
      .click(selector)
  });


  client.addCommand('waitAndSelectByValue', function (selector, value, timeout=60000) {
    return client
      .waitForExist(selector, timeout)
      .selectByValue(selector,value)
  });




  client.addCommand('signInBO', function (selector) {
    this.selector = globals.selector;
    return client
      .url('http://' + URL + '/admin-dev')
      .waitAndSetValue(selector.BO.AccessPage.login_input, 'demo@prestashop.com')
      .waitAndSetValue(selector.BO.AccessPage.password_input, 'prestashop_demo')
      .waitForExistAndClick(selector.BO.AccessPage.login_button)
      .waitForExist(selector.BO.Common.menu, 90000)
  });

  client.addCommand('signInFO', function (selector) {
    return client
      .waitForExistAndClick(selector.FO.AccessPage.sign_in_button)
      .waitAndSetValue(selector.FO.AccessPage.login_input, 'pub@prestashop.com')
      .waitAndSetValue(selector.FO.AccessPage.password_input, '123456789')
      .waitForExistAndClick(selector.FO.AccessPage.login_button)
      .waitForExistAndClick(selector.FO.AccessPage.logo_home_page)
  });

  client.addCommand('signOutBO', function (cb) {
    this.selector = globals.selector;
    return client
      .deleteCookie()
      .call(cb);
  });

  client.addCommand('signOutFO', function (cb) {
    this.selector = globals.selector;
    return client
      .waitForExistAndClick(selector.FO.AccessPage.sign_out_button)
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
