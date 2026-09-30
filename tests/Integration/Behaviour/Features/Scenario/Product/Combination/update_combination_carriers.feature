# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-carriers
@restore-products-before-feature
@restore-currencies-after-feature
@clear-cache-before-feature
@product-combination
@update-combination-carriers
Feature: Update product combination carriers from Back Office (BO)
  As a BO user
  I need to be able to restrict the carriers of a combination, overriding the product ones

  Background:
    Given language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    And there is a currency named "currency1" with iso code "USD" and exchange rate of 1.0
    And currency "currency1" is the current one
    And I enable feature flag "combination_feature_values"
    And attribute group "Color" named "Color" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And I create carrier "carrier1" with specified properties:
      | name             | Carrier 1 |
      | isFree           | true      |
      | shippingHandling | false     |
    And I create carrier "carrier2" with specified properties:
      | name             | Carrier 2 |
      | isFree           | true      |
      | shippingHandling | false     |
    And I create carrier "carrier3" with specified properties:
      | name             | Carrier 3 |
      | isFree           | true      |
      | shippingHandling | false     |
    And I add product "shirt" with following information:
      | name[en-US] | Magic shirt  |
      | type        | combinations |
    And I generate combinations for product shirt using following attributes:
      | Color | [Blue,Red] |
    And product "shirt" should have following combinations:
      | id reference | combination name | reference | attributes   | impact on price | quantity | is default |
      | shirtBlue    | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
      | shirtRed     | Color - Red      |           | [Color:Red]  | 0               | 0        | false      |

  Scenario: Combination carriers override the product ones, a combination without carriers uses the product ones
    Given combination "shirtBlue" should have carriers "[]"
    When I assign product shirt with following carriers:
      | carrier1 |
    And I assign combination "shirtBlue" with following carriers:
      | carrier2 |
      | carrier3 |
    Then combination "shirtBlue" should have carriers "[carrier2,carrier3]"
    And combination "shirtRed" should have carriers "[]"
    And the carriers available for combination "shirtBlue" should be "[carrier2,carrier3]"
    And the carriers available for combination "shirtRed" should be "[carrier1]"
    When I remove all carriers from combination "shirtBlue"
    Then combination "shirtBlue" should have carriers "[]"
    And the carriers available for combination "shirtBlue" should be "[carrier1]"

  Scenario: Combination carriers are copied when the product is duplicated
    When I assign combination "shirtRed" with following carriers:
      | carrier3 |
    And I duplicate product shirt to a shirtCopy
    Then product "shirtCopy" should have following combinations:
      | id reference  | combination name | reference | attributes   | impact on price | quantity | is default |
      | shirtBlueCopy | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
      | shirtRedCopy  | Color - Red      |           | [Color:Red]  | 0               | 0        | false      |
    And combination "shirtRedCopy" should have carriers "[carrier3]"
    And combination "shirtBlueCopy" should have carriers "[]"
