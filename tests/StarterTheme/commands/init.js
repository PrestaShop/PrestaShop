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

  /**
   * Visit a product page.
   */
  browser.addCommand('productPage', function productPage (productId) {
    return browser.urlWithPrestaShopRedirect('/?controller=product&id_product=' + productId);
  });
};
