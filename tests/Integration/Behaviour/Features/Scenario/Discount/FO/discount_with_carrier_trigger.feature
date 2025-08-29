# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-carrier-trigger
@restore-all-tables-before-feature
@discount-with-carrier-trigger
Feature: Add discount with carrier trigger on FO
  PrestaShop allows discounts with restricted carriers as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And group "customer" named "Customer" exists
    And there is a zone "north_america" named "North America"
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
      | zones            | north_america                      |
      | rangeBehavior    | highest_range                      |
    And I set ranges for carrier "nice_carrier" with specified properties for all shops:
      | id_zone       | range_from | range_to | range_price |
      | north_america | 0          | 100      | 15          |

  Scenario: Reduction with carrier trigger on FO (with discount code)
    # Create discount with free shipping when nice carrier is used (with code)
    When I create a "free_shipping" discount "discount_with_nice_carrier" with following properties:
      | name[en-US] | Nice carrier offers shipping    |
      | name[fr-FR] | Nice carrier offre la livraison |
      | code        | FREE_SHIPPING_NICE_CARRIER      |
      | active      | true                            |
    When I update discount "discount_with_nice_carrier" with conditions based on carriers "nice_carrier"
    Then discount "discount_with_nice_carrier" should have the following properties:
      | name[en-US] | Nice carrier offers shipping    |
      | name[fr-FR] | Nice carrier offre la livraison |
      | type        | free_shipping                   |
      | carriers    | nice_carrier                    |
      | code        | FREE_SHIPPING_NICE_CARRIER      |
    Given I create an empty cart "dummy_cart" for customer "testCustomer"
    # This mug is worth 11.90$ without tax
    When I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And cart "dummy_cart" total with tax included should be '$30.80'
    And my cart "dummy_cart" should have the following details:
      | total_products | $23.80 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $30.80 |
    # We try to apply the discount with nice carrier it cannot work as it is not selected, the cart is not modified
    When I use a voucher "discount_with_nice_carrier" on the cart "dummy_cart"
    Then I should get cart rule validation error
    And my cart "dummy_cart" should have the following details:
      | total_products | $23.80 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $30.80 |
    # Now I update the carrier, the shipping cost is updated
    When I select carrier "nice_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "nice_carrier" as a carrier
    Then my cart "dummy_cart" should have the following details:
      | total_products | $23.80 |
      | shipping       | $15.00 |
      | total_discount | $0.00  |
      | total          | $38.80 |
    # Now we try again the voucher this time it works
    When I use a voucher "discount_with_nice_carrier" on the cart "dummy_cart"
    Then cart "dummy_cart" total with tax included should be '$23.80'
    And my cart "dummy_cart" should have the following details:
      | total_products | $23.80  |
      | shipping       | $15.00  |
      | total_discount | -$15.00 |
      | total          | $23.80  |
