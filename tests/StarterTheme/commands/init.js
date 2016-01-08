var fixtures = require('../fixtures');

module.exports = function initializePrestaShopBrowserCommands (browser) {

  /**
   * Go to a URL, but follow prestashop's debug redirects like:
   *
   * [Debug] This page has moved
   * Please use the following URL instead: [actual url]
   */
  browser.addCommand('urlWithPrestaShopRedirect', function urlWithPrestaShopRedirect (url) {
    return browser
      .url(url)
      .getText('body')
      .then(function (text) {
        var needle = '[Debug] This page has moved';
        if (text.substr(0, needle.length) === needle) {
          return browser.click('a');
        }
      })
    ;
  });

  browser.addCommand('loginDefaultCustomer', function loginDefaultCustomer (params) {
    const customer = Object.assign({}, fixtures.customer, params);

    return browser
      .url(fixtures.urls.login)
      .setValue('.login-form input[name=email]', customer.email)
      .setValue('.login-form input[name=password]', customer.password)
      .submitForm('.login-form form')
    ;
  });

  browser.addCommand('logout', function logout () {
    return browser
      .url()
      .then(function (initialURL) {
        return browser
          .url('/')
          .click('#header a.logout')
          .url(initialURL)
        ;
      })
    ;
  });

  /**
   * Visit a product page.
   */
  browser.addCommand('productPage', function productPage (productId) {
    return browser.urlWithPrestaShopRedirect('/?controller=product&id_product=' + productId);
  });
};
