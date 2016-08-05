var fixtures = require('../fixtures');
var fs = require('fs');

module.exports = function initializePrestaShopBrowserCommands(browser) {

  /**
   * Go to a URL, but follow prestashop's debug redirects like:
   *
   * [Debug] This page has moved
   * Please use the following URL instead: [actual url]
   */
  browser.addCommand('urlWithPrestaShopRedirect', function urlWithPrestaShopRedirect(url) {
    return browser
      .url(url)
      .followRedirect()
    ;
  });

  browser.addCommand('followRedirect', function followRedirect() {
    return browser
      .getText('body')
      .then(function (text) {
        var needle = '[Debug] This page has moved';
        if (text.substr(0, needle.length) === needle) {
          return browser.click('a');
        }
      })
    ;
  });

  browser.addCommand('loginDefaultCustomer', function loginDefaultCustomer(params) {
    const customer = Object.assign({}, fixtures.customer, params);

    return browser
      .url(fixtures.urls.login)
      .setValue('.login-form input[name=email]', customer.email)
      .setValue('.login-form input[name=password]', customer.password)
      .submitForm('.login-form form')
    ;
  });

  browser.addCommand('logout', function logout() {
    return browser
      .deleteCookie()
      .refresh()
    ;
  });

  /**
   * Generate a dump of page and display the url
   */
  browser.addCommand('dump', function dump() {
    return browser
      .getUrl().then(function(url) {
        console.log(url);
      })
      .getSource().then(function(source) {
        var randomId = Math.floor((Math.random() * 1000) + 1);
        var randomFilename = `error-${randomId}.html`;

        fs.writeFile(`./errorDumps/${randomFilename}`, source, function(err) {
          if(err) {
            return console.log(err);
          }
          console.log(`Generated HTML file available at: /errors/${randomFilename}`);
        });
      });
  });

  /**
   * Visit a product page.
   */
  browser.addCommand('productPage', function productPage(productId) {
    return browser.urlWithPrestaShopRedirect('/?controller=product&id_product=' + productId);
  });
};
