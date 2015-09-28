/* global describe, it, browser */

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
  return q.all(_.map(variant, checkGroupHasValue));
}

function chooseValueInGroup (valueId, groupId) {
  var groupSelector = '#group_' + groupId;
  return browser.getTagName(groupSelector)
    .then(function (tagName) {
      if (tagName === 'select') {
        return browser.selectByValue(groupSelector, valueId);
      } else {
        return browser.click(groupSelector + ' [value="' + valueId + '"]');
      }
    })
  ;
}

function chooseVariant (variant) {
  return q.all(_.map(variant, chooseValueInGroup)).then(function () {
    return browser.click('.product-variants .product-refresh');
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
        .then(function () {
          return chooseVariant(variant);
        })
        .then(checkVariantIsSelected.bind(undefined, variant))
      ;
    });
  });
});
