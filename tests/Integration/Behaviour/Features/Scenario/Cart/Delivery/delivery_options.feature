# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags delivery-options

@reset-database-before-feature
@delivery-options
@clear-cache-after-feature
Feature: Compute correct delivery options
  As a customer
  I should be provided relevant delivery options depending on my cart content and my customer profile

  Scenario: Use 2 carts rules, 1 free-shipping and one global, no carriers are available
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    # Create free shipping cart rule
    Given there is a cart rule named "free_shipping_1" that applies no discount with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "free_shipping_1" offers free shipping
    Given cart rule "free_shipping_1" has a discount code "djbuch-12878"
    # Standard cart rule, disabled
    Given there is a cart rule named "bad_cart_rule" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "bad_cart_rule" is disabled
    # Standard location settings
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    # One standard carrier
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for price between 0 and 10000
    # Checkout begins
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    # Use discount code
    When I use the discount "free_shipping_1"
    When I select address "address1" in my cart
    # Enables standard cart rule
    When I enable cart rule "bad_cart_rule"
    When I select carrier "carrier1" in my cart
    Then there are available delivery options for my cart
