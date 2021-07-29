# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags legacy-product-type
@reset-database-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@legacy-product-type
Feature: Legacy products have consistent product type through dynamic checking (BO)
  As a BO user
  I need to be sure that "legacy" products (created with page v1) have a correct product type (used for v2)

  Background:
    Given language with iso code "en" is the default one
    And the current currency is "USD"
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists

  Scenario: I create a standard product using legacy methods, its product type should be standard
    Given there is a product in the catalog named "Standard Product" with a price of 15.0 and 100 items in stock
    Then there is a product "standard_product" with name "Standard Product"
    And product "standard_product" type should be standard
    And product "standard_product" persisted type should be undefined
    And product "standard_product" dynamic type should be standard

  Scenario: I create a product and add combinations using legacy methods, its product type should be combinations
    Given there is a product in the catalog named "Product With Combinations" with a price of 15.0 and 100 items in stock
    Then there is a product "product_with_combinations" with name "Product With Combinations"
    And product "product_with_combinations" type should be standard
    And product "product_with_combinations" persisted type should be undefined
    And product "Product With Combinations" has combinations with following details:
      | reference | quantity | attributes         |
      | whiteM    | 150      | Size:M;Color:White |
      | whiteL    | 150      | Size:L;Color:White |
    Then product "product_with_combinations" type should be combinations
    And product "product_with_combinations" persisted type should be combinations
    And product "product_with_combinations" dynamic type should be combinations

  Scenario: I create a virtual product, its product type should be virtual
    Given there is a product in the catalog named "Virtual Product" with a price of 15.0 and 100 items in stock
    Then there is a product "virtual_product" with name "Virtual Product"
    And product "virtual_product" type should be standard
    And product "virtual_product" persisted type should be undefined
    And product "virtual_product" dynamic type should be standard
    Given product "Virtual Product" is virtual
    Then product "virtual_product" type should be virtual
    And product "virtual_product" persisted type should be virtual
    And product "virtual_product" dynamic type should be virtual

  Scenario: I create a pack product, its product type should be pack
    Given there is a product in the catalog named "Pack Product" with a price of 15.0 and 100 items in stock
    And there is a product in the catalog named "Product in pack" with a price of 15.0 and 100 items in stock
    Then there is a product "pack_product" with name "Pack Product"
    And product "pack_product" type should be standard
    And product "pack_product" persisted type should be undefined
    And product "pack_product" dynamic type should be standard
    Given product "Pack Product" is a pack containing 10 items of product "Product in pack"
    Then product "Pack Product" is considered as a pack
    And product "pack_product" type should be pack
    And product "pack_product" persisted type should be pack
    And product "pack_product" dynamic type should be pack
