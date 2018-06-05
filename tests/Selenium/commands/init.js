var fixtures = require('../fixtures');
var fs = require('fs');

module.exports = function initializePrestaShopBrowserCommands(browser) {

  /**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
