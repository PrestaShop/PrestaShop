# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-country-trigger
@restore-all-tables-before-feature
@discount-with-country-trigger
Feature: Add discount with country trigger on FO
  PrestaShop allows discounts with restricted products as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    Given country "France" with iso code "FR" exists
    And group "customer" named "Customer" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92

  Scenario: I create a zone and a carrier for europe
    Given there is a zone "europe" named "Europe"
    And I create carrier "nice_carrier" with specified properties:
      | name             | Nice carrier                       |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | active           | true                               |
      | group_access     | customer                           |
      | delay[en-US]     | Shipping delay                     |
      | delay[fr-FR]     | Délai de livraison                 |
      | shippingHandling | false                              |
      | isFree           | false                              |
      | shippingMethod   | price                              |
      | zones            | europe                             |
      | rangeBehavior    | highest_range                      |
    And I set ranges for carrier "nice_carrier" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | europe  | 0          | 100      | 7           |

  Scenario: I create a customer in france
    Given I create customer "testFrenchCustomer" with following details:
      | firstName | testFirstName               |
      | lastName  | testLastName                |
      | email     | test.davidsonas@invertus.eu |
      | password  | secret                      |
    When I add new address to customer "testFrenchCustomer" with following details:
      | Address alias | test-customer-address       |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Valence                     |
      | Country       | France                      |
      | Postal code   | 26000                       |
    Then customer "testFrenchCustomer" should have address "test-customer-address" with following details:
      | Address alias | test-customer-address       |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Valence                     |
      | Country       | France                      |
      | Postal code   | 26000                       |

  Scenario: First create products and the discount that will be used in following scenarios
    Given I add product "product1" with following information:
      | name[en-US] | eastern european tracksuit |
      | type        | standard                   |
    And I update product "product1" stock with following information:
      | delta_quantity | 123 |
    And I enable product "product1"
    And I update product "product1" with following values:
      | price | 5.00 |
    When I create a "free_shipping" discount "discount_with_country_trigger" with following properties:
      | name[en-US] | Promotion                  |
      | name[fr-FR] | Promotion                  |
      | code        | CART_LEVEL_COUNTRY_TRIGGER |
      | active      | true                       |
    Then discount "discount_with_country_trigger" should have the following properties:
      | name[en-US] | Promotion                  |
      | name[fr-FR] | Promotion                  |
      | type        | free_shipping              |
      | code        | CART_LEVEL_COUNTRY_TRIGGER |
      | active      | true                       |
    When I update discount "discount_with_country_trigger" with conditions based on countries "France"
    Then discount "discount_with_country_trigger" should have the following properties:
      | minimum_product_quantity | 0 |
    Then discount "discount_with_country_trigger" should have the following properties:
      | name[en-US]              | Promotion     |
      | name[fr-FR]              | Promotion     |
      | type                     | free_shipping |
      | minimum_product_quantity | 0             |

  Scenario: Reduction with country trigger on FO
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    When I select carrier "nice_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "nice_carrier" as a carrier
    When I add 1 product "eastern european tracksuit" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$12.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $5.00  |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $12.00 |
    When I use a voucher "discount_with_country_trigger" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And cart "dummy_cart" total with tax included should be '$12.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $5.00  |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $12.00 |
    When I assign customer "testCustomer" to cart "dummy_cart"
    When I select "FR" address as delivery and invoice address for customer "testFrenchCustomer" in cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$12.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $5.00  |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $12.00 |
    When I use a voucher "discount_with_country_trigger" on the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$5.00'
    And my cart "dummy_cart" should have the following details:
      | total_products | $5.00  |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $5.00  |
    And my cart "dummy_cart" should have the following details, without hiding auto discounts:
      | total_products | $5.00  |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $5.00  |
