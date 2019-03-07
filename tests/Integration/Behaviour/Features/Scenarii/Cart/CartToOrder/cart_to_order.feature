@reset-database-before-feature
Feature: Check cart to order data copy
  As a customer
  I must be able to have a correct order when validating payment step

  @current
  Scenario: 1 product in cart, 1 cart rule
    Given I have an empty default cart
    Given Email sending is disabled
    Given Shop configuration of PS_SHIPPING_HANDLING is set to 2.0
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule1 and percent discount of 50.0% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule1 has a code: foo1
    Given There is a zone with name zone1
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is a tax with name tax1 and rate 4.0%
    Given There is a tax rule with name taxrule1 in country with name country1 and state with name state1 with tax with name tax1
    Given Product with name product1 belongs to tax group with name taxrule1
    Given There is a customer with name customer1 and email fake@prestashop.com
    Given Address with name address1 is associated to customer with name customer1
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 ships to all groups
    Given Carrier with name carrier1 has a shipping fees of 5.0 in zone with name zone1 for quantities between 0 and 10000
    When Current customer is customer with name customer1
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule1 to my cart
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    When I validate my cart with payment module fake
    Then Current cart order total for products should be 20.6 tax included
    Then Current cart order total for products should be 19.81 tax excluded
    Then Current cart order total discount should be 10.3 tax included
    Then Current cart order total discount should be 9.91 tax excluded
    Then Current cart order shipping fees should be 7.0 tax included
    Then Current cart order shipping fees should be 7.0 tax excluded
    Then Current cart order should have a discount in position 1 with value 10.3 tax included and 9.91 tax excluded
    Then Voucher count for customer with name customer1 should be 0

  Scenario: 1 product in cart, 2 cart rules
    Given I have an empty default cart
    Given Email sending is disabled
    Given Shop configuration of PS_SHIPPING_HANDLING is set to 2.0
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule1 and percent discount of 50.0% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule1 has a code: foo1
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    Given There is a zone with name zone1
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is a tax with name tax1 and rate 4.0%
    Given There is a tax rule with name taxrule1 in country with name country1 and state with name state1 with tax with name tax1
    Given Product with name product1 belongs to tax group with name taxrule1
    Given There is a customer with name customer1 and email fake@prestashop.com
    Given Address with name address1 is associated to customer with name customer1
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 ships to all groups
    Given Carrier with name carrier1 has a shipping fees of 5.0 in zone with name zone1 for quantities between 0 and 10000
    When Current customer is customer with name customer1
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule1 to my cart
    When I add cart rule named cartrule2 to my cart
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    When I validate my cart with payment module fake
    Then Current cart order total for products should be 20.6 tax included
    Then Current cart order total for products should be 19.81 tax excluded
    Then Current cart order total discount should be 15.45 tax included
    Then Current cart order total discount should be 14.86 tax excluded
    Then Current cart order shipping fees should be 7.0 tax included
    Then Current cart order shipping fees should be 7.0 tax excluded
    Then Current cart order should have a discount in position 1 with value 10.3 tax included and 9.91 tax excluded
    Then Current cart order should have a discount in position 2 with value 5.15 tax included and 4.95 tax excluded
    Then Voucher count for customer with name customer1 should be 0

  Scenario: 3 product in cart, 1 cart rule
    Given I have an empty default cart
    Given Email sending is disabled
    Given Shop configuration of PS_SHIPPING_HANDLING is set to 2.0
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule1 and percent discount of 50.0% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule1 has a code: foo1
    Given There is a zone with name zone1
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is a tax with name tax1 and rate 4.0%
    Given There is a tax rule with name taxrule1 in country with name country1 and state with name state1 with tax with name tax1
    Given Product with name product1 belongs to tax group with name taxrule1
    Given Product with name product2 belongs to tax group with name taxrule1
    Given Product with name product3 belongs to tax group with name taxrule1
    Given There is a customer with name customer1 and email fake@prestashop.com
    Given Address with name address1 is associated to customer with name customer1
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 ships to all groups
    Given Carrier with name carrier1 has a shipping fees of 5.0 in zone with name zone1 for quantities between 0 and 10000
    When Current customer is customer with name customer1
    When I add product named product2 in my cart with quantity 1
    When I add product named product1 in my cart with quantity 1
    When I add product named product3 in my cart with quantity 2
    When I add cart rule named cartrule1 to my cart
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    When I validate my cart with payment module fake
    Then Current cart order total for products should be 119.15 tax included
    Then Current cart order total for products should be 114.58 tax excluded
    Then Current cart order total discount should be 59.58 tax included
    Then Current cart order total discount should be 57.29 tax excluded
    Then Current cart order shipping fees should be 7.0 tax included
    Then Current cart order shipping fees should be 7.0 tax excluded
    Then Current cart order should have a discount in position 1 with value 59.58 tax included and 57.29 tax excluded
    Then Voucher count for customer with name customer1 should be 0

  Scenario: 3 product in cart, 3 cart rules
    Given I have an empty default cart
    Given Email sending is disabled
    Given Shop configuration of PS_SHIPPING_HANDLING is set to 2.0
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a product with name product2 and price 32.388 and quantity 1000
    Given There is a product with name product3 and price 31.188 and quantity 1000
    Given There is a cart rule with name cartrule1 and percent discount of 50.0% and priority of 1 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule1 has a code: foo1
    Given There is a cart rule with name cartrule2 and percent discount of 50.0% and priority of 2 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule2 has a code: foo2
    Given There is a zone with name zone1
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is a tax with name tax1 and rate 4.0%
    Given There is a tax rule with name taxrule1 in country with name country1 and state with name state1 with tax with name tax1
    Given Product with name product1 belongs to tax group with name taxrule1
    Given Product with name product2 belongs to tax group with name taxrule1
    Given Product with name product3 belongs to tax group with name taxrule1
    Given There is a customer with name customer1 and email fake@prestashop.com
    Given Address with name address1 is associated to customer with name customer1
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 ships to all groups
    Given Carrier with name carrier1 has a shipping fees of 5.0 in zone with name zone1 for quantities between 0 and 10000
    When Current customer is customer with name customer1
    When I add product named product2 in my cart with quantity 1
    When I add product named product1 in my cart with quantity 1
    When I add product named product3 in my cart with quantity 2
    When I add cart rule named cartrule1 to my cart
    When I add cart rule named cartrule2 to my cart
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    When I validate my cart with payment module fake
    Then Current cart order total for products should be 119.15 tax included
    Then Current cart order total for products should be 114.58 tax excluded
    Then Current cart order total discount should be 89.36 tax included
    Then Current cart order total discount should be 85.94 tax excluded
    Then Current cart order shipping fees should be 7.0 tax included
    Then Current cart order shipping fees should be 7.0 tax excluded
    Then Current cart order should have a discount in position 1 with value 59.58 tax included and 57.29 tax excluded
    Then Current cart order should have a discount in position 2 with value 29.79 tax included and 28.65 tax excluded
    Then Voucher count for customer with name customer1 should be 0

  Scenario: 1 product in cart, 1 cart rule with too-much amount
    Given I have an empty default cart
    Given Email sending is disabled
    Given Shop configuration of PS_SHIPPING_HANDLING is set to 2.0
    Given There is a product with name product1 and price 19.812 and quantity 1000
    Given There is a cart rule with name cartrule5 and amount discount of 500 and priority of 5 and quantity of 1000 and quantity per user of 1000
    Given Cart rule named cartrule5 has a code: foo5
    Given There is a zone with name zone1
    Given There is a country with name country1 and iso code FR in zone named zone1
    Given There is a state with name state1 and iso code TEST-1 in country named country1 and zone named zone1
    Given There is an address with name address1 and post code 1 in country named country1 and state named state1
    Given There is a tax with name tax1 and rate 4.0%
    Given There is a tax rule with name taxrule1 in country with name country1 and state with name state1 with tax with name tax1
    Given Product with name product1 belongs to tax group with name taxrule1
    Given There is a customer with name customer1 and email fake@prestashop.com
    Given Address with name address1 is associated to customer with name customer1
    Given There is a carrier with name carrier1
    Given Carrier with name carrier1 ships to all groups
    Given Carrier with name carrier1 has a shipping fees of 5.0 in zone with name zone1 for quantities between 0 and 10000
    When Current customer is customer with name customer1
    When I add product named product1 in my cart with quantity 1
    When I add cart rule named cartrule5 to my cart
    When I select in my cart address with name address1
    When I select in my cart carrier with name carrier1
    When I validate my cart with payment module fake
    Then Current cart order total for products should be 20.6 tax included
    Then Current cart order total for products should be 19.81 tax excluded
    Then Current cart order total discount should be 20.6 tax included
    Then Current cart order total discount should be 19.81 tax excluded
    Then Current cart order shipping fees should be 7.0 tax included
    Then Current cart order shipping fees should be 7.0 tax excluded
    Then Current cart order should have a discount in position 1 with value 20.6 tax included and 19.81 tax excluded
    Then Voucher count for customer with name customer1 should be 1
    Then Voucher on position 1 for customer with name customer1 should have reduction value 480.19
