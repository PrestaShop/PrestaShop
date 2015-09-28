/* global describe, it, browser */

var fixtures = require('../fixtures');

describe('The product page', function () {
  describe('of a product with variants', function () {
    it('should contain a variant selector', function () {
      return browser
        .productPage(fixtures.aProductWithVariants.id)
        .then(function () {
          return browser.element('.product-variants');
        })
      ;
    });
  });
});
