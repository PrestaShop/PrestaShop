/* global describe, it, browser, before */

var fixtures = require('../fixtures');
var _ = require('underscore');
var q = require('q');

function checkGroupHasValue (valueId, groupId) {
  var groupSelector = '#group_' + groupId;
  return browser.getAttribute(
    groupSelector + ' [selected], ' + groupSelector + ' [checked]',
    'value'
  ).then(function (selectedValueId) {
    selectedValueId.should.equal(valueId.toString());
  });
}

function checkVariantIsSelected (variant) {
  return _.map(variant, checkGroupHasValue);
}

function chooseValueInGroup (valueId, groupId) {
  var groupSelector = '#group_' + groupId;
  return browser.getTagName(groupSelector)
    .then(function (tagName) {
      if (tagName === 'select') {
        browser.selectByValue(groupSelector, valueId).then(() => {
          return browser.waitForVisible(groupSelector + ' [value="' + valueId + '"]');
        });
      } else {
        browser.click(groupSelector + ' [value="' + valueId + '"]').then(() => {
          return browser.waitForVisible(groupSelector + ' [value="' + valueId + '"]');
        });
      }
    })
  ;
}

function chooseVariant (variant) {
  return q.all(_.map(variant, chooseValueInGroup)).then(() => {
    const noJSButton = '.product-variants .product-refresh';
    return browser.isVisible(noJSButton).then(
      visible => {
        if (visible) {
          return browser.click(noJSButton);
        }
      }
    );
  });
}

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

    it('should show the default variant by default', function () {
      return browser
        .productPage(fixtures.aProductWithVariants.id)
        .then(checkVariantIsSelected.bind(
          undefined,
          fixtures.aProductWithVariants.defaultVariant
        ))
      ;
    });

    it('should allow selecting another variant', function () {
      var variant = fixtures.aProductWithVariants.anotherVariant;

      return browser
        .productPage(fixtures.aProductWithVariants.id)
        .then(chooseVariant.bind(undefined, variant))
        .then(checkVariantIsSelected.bind(undefined,variant))
      ;
    });
  });

  describe('of a customizable product', function () {

    before(function () {
      return browser
        .productPage(fixtures.aCustomizableProduct.id)
      ;
    });

    it('should display customization fields', function () {
      return browser.element('.product-customization');
    });

    it('should display the add to cart button disabled, because the product is not customized yet', function () {
        return !browser.isEnabled('form .add-to-cart');
    });

    it('should display the add to cart button enabled once the product is customized', function () {
      return browser
        .setValue('.product-customization textarea', 'a cool text')
        .click('[name="submitCustomizedData"]')
        .isEnabled('form .add-to-cart')
      ;
    });
  });
});
