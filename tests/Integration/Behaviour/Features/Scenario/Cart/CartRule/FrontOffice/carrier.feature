# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags fo-cart-rule-carrier
@restore-all-tables-before-feature
@fo-cart-rule-carrier
Feature: Cart calculation with cart rules and different carriers
  As a customer
  I must see correct cart total price with different carriers when applying cart rules

  Background:
    Given I have an empty default cart
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 149.0 and 1000 items in stock
    And there is a product in the catalog named "product5" with a price of 151.0 and 1000 items in stock
    And there is a carrier named "carrier1"
    And there is a zone named "zone1"
    And there is a zone named "zone2"
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a country named "country2" and iso code "US" in zone "zone2"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is a state named "state2" with iso code "TEST-2" in country "country2" and zone "zone2"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is an address named "address2" with postcode "2" in state "state2"
    And there is a carrier named "carrier1"
    And there is a carrier named "carrier2"
    And there is a carrier named "carrier3"
    And there is a carrier named "carrier4"
    And carrier "carrier1" applies shipping fees of 3.1 in zone "zone1" for price between 0 and 10000
    And carrier "carrier1" applies shipping fees of 4.3 in zone "zone2" for price between 0 and 10000
    And carrier "carrier2" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    And carrier "carrier2" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    And carrier "carrier3" applies shipping fees of 5.7 in zone "zone1" for price between 0 and 10000
    And carrier "carrier3" applies shipping fees of 6.2 in zone "zone2" for price between 0 and 10000
    And carrier "carrier4" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 150
    And carrier "carrier4" applies shipping fees of 0.0 in zone "zone1" for price between 150 and 1000
    And shipping handling fees are set to 2.0
    And there is a cart rule "cartrule5" with following properties:
      | name[en-US]                      | cartrule5 |
      | total_quantity                   | 1000      |
      | quantity_per_user                | 1000      |
      | priority                         | 2         |
      | free_shipping                    | true      |
      | code                             | foo5      |
      | minimum_amount                   | 150       |
      | minimum_amount_currency          | usd       |
      | minimum_amount_tax_included      | false     |
      | minimum_amount_shipping_included | false     |
    And there is a cart rule "cartrule2" with following properties:
      | name[en-US]                  | cartrule2              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | free_shipping                | false                  |
      | code                         | cartrule2              |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |

  # Issue #9540 part one fixed by #12965
  Scenario: free carrier in price range, voucher in percent set the price bellow range
    And I add 1 item of product "product5" in my cart
    And I apply the voucher code "cartrule2"
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    And cart shipping fees should be 7.0
    And my cart total should be 82.5 tax included
    And my cart total using previous calculation method should be 82.5 tax included

  Scenario: free carrier in price range, voucher in amount set the price bellow range
    Given there is a cart rule "cartrule3" with following properties:
      | name[en-US]                  | cartrule3              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | free_shipping                | false                  |
      | code                         | foo2                   |
      | discount_amount              | 2                      |
      | discount_currency            | usd                    |
      | discount_includes_tax        | false                  |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    When I add 1 item of product "product5" in my cart
    And I apply the voucher code "foo2"
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    Then cart shipping fees should be 7.0
    And my cart total should be 156.0 tax included
    And my cart total using previous calculation method should be 156.0 tax included

  # Issue #12976 part two
  @restore-cart-rules-after-scenario
  Scenario: carrier fees not free, voucher without code set shipping fees free above amount, cart total is below
    Given there is a cart rule "cartrule4" with following properties:
      | name[en-US]                      | cartrule4 |
      | total_quantity                   | 1000      |
      | quantity_per_user                | 1000      |
      | priority                         | 2         |
      | free_shipping                    | true      |
      | minimum_amount                   | 150       |
      | minimum_amount_currency          | usd       |
      | minimum_amount_tax_included      | false     |
      | minimum_amount_shipping_included | false     |
    When I add 1 item of product "product4" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    Then cart shipping fees should be 7.0
    And my cart total should be 156.0 tax included
    And my cart total using previous calculation method should be 156.0 tax included

  Scenario: carrier fees not free, voucher with code set shipping fees free above amount, cart total is below
    When I add 1 item of product "product4" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    And I apply the voucher code "foo5"
    Then I should get cart rule validation error saying "The minimum amount to benefit from this promo code is $150"
    And cart shipping fees should be 7.0
    And my cart total should be 156.0 tax included
    And my cart total using previous calculation method should be 156.0 tax included

  @restore-cart-rules-after-scenario
  Scenario: carrier fees not free, voucher without code set shipping fees free above amount, cart total is below
    Given there is a cart rule "cartrule6" with following properties:
      | name[en-US]                      | cartrule6 |
      | total_quantity                   | 1000      |
      | quantity_per_user                | 1000      |
      | priority                         | 2         |
      | free_shipping                    | true      |
      | minimum_amount                   | 150       |
      | minimum_amount_currency          | usd       |
      | minimum_amount_tax_included      | false     |
      | minimum_amount_shipping_included | false     |
    When I add 1 item of product "product4" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    Then cart shipping fees should be 7.0
    And my cart total should be 156.0 tax included
    And my cart total using previous calculation method should be 156.0 tax included

  Scenario: carrier fees not free, voucher with code set shipping fees free above amount, cart total is above
    Given I add 1 item of product "product5" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    When I apply the voucher code "foo5"
    Then cart shipping fees should be 2.0
    And my cart total should be 151.0 tax included
    And my cart total using previous calculation method should be 151.0 tax included

  @restore-cart-rules-after-scenario
  Scenario: carrier fees not free, voucher without code set shipping fees free above amount, cart total is above
    Given there is a cart rule "cartrule6" with following properties:
      | name[en-US]                      | cartrule6 |
      | total_quantity                   | 1000      |
      | quantity_per_user                | 1000      |
      | priority                         | 2         |
      | free_shipping                    | true      |
      | minimum_amount                   | 150       |
      | minimum_amount_currency          | usd       |
      | minimum_amount_tax_included      | false     |
      | minimum_amount_shipping_included | false     |
    When I add 1 item of product "product5" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier4" in my cart
    Then cart shipping fees should be 2.0
    And my cart total should be 151.0 tax included
    And my cart total using previous calculation method should be 151.0 tax included

  Scenario: one product in cart, quantity 1, can apply only the cart rule which is restricted to selected carrier
    Given there is a cart rule "cartrule7" with following properties:
      | name[en-US]                  | cartrule7              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 1                      |
      | free_shipping                | false                  |
      | code                         | cartrule7              |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And cart rule "cartrule7" is restricted to carrier "carrier2"
    And there is a cart rule "cartrule8" with following properties:
      | name[en-US]                  | cartrule8              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 2                      |
      | free_shipping                | false                  |
      | code                         | cartrule8              |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | true                   |
      | discount_application_type    | order_without_shipping |
    And cart rule "cartrule8" is restricted to carrier "carrier1"
    When I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier2" in my cart
    Then cart shipping fees should be 7.7
    Then my cart total should be 27.5 tax included
    When I apply the voucher code "cartrule8"
    Then I should get cart rule validation error saying "You cannot use this voucher with this carrier"
    And I select carrier "carrier1" in my cart
    And I apply the voucher code "cartrule8"
    Then cart shipping fees should be 5.1
    And my cart total should be 15.0 tax included

  @restore-cart-rules-after-scenario
  Scenario: one product in cart, quantity 1, cart rule without code correctly (un)applied on corresponding carrier
    Given there is a cart rule "cartrule9" with following properties:
      | name[en-US]                  | cartrule9              |
      | total_quantity               | 1000                   |
      | quantity_per_user            | 1000                   |
      | priority                     | 3                      |
      | free_shipping                | false                  |
      | discount_percentage          | 55                     |
    And cart rule "cartrule9" is restricted to carrier "carrier3"
    And cart rule "cartrule5" is restricted to carrier "carrier2"
    And cart rule "cartrule2" is restricted to carrier "carrier1"
    When I add 1 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier2" in my cart
    Then cart rule count in my cart should be 0
    When I select carrier "carrier3" in my cart
    Then cart rule count in my cart should be 1
    When I select carrier "carrier2" in my cart
    Then cart rule count in my cart should be 0
