# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule
@reset-database-before-feature
Feature: Add cart rule
  PrestaShop allows BO users to create cart rules
  As a BO user
  I must be able to create cart rules

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given currency "currency1" is the default one

  Scenario: Create a cart rule with amount discount
    When I want to create a new cart rule
    And I specify its name in default language as "Promotion"
    And I specify its "description" as "Promotion for holidays"
    And I specify that its active from "2019-01-01 11:05:00"
    And I specify that its active until "2019-12-01 00:00:00"
    And I specify that its "quantity" is "10"
    And I specify that its "quantity per user" is "1"
    And I specify that its "priority" is "2"
    And I specify that partial use is disabled for it
    And I specify its status as enabled
    And I specify that it should not be highlighted in cart
    And I specify its "code" as "PROMO_2019"
    And its minimum purchase amount in currency "CHF" is "10"
    And its minimum purchase amount is tax excluded
    And its minimum purchase amount is shipping included
    And it gives free shipping
    And it gives a reduction amount of "15" in currency "USD" which is tax included and applies to order without shipping
    When I save it
    Then its name in default language should be "Promotion"
    And its "description" should be "Promotion for holidays"
    And it should be active from "2019-01-01 11:05:00"
    And it should be active until "2019-12-01 00:00:00"
    And its "quantity" should be "10"
    And its "quantity per user" should be "1"
    And its "priority" should be "2"
    And its "partial use" should be "disabled"
    And its "status" should be "enabled"
    And it should not be highlighted in cart
    And its "code" should be "PROMO_2019"
    And it should have minimum purchase amount of "10" in currency "CHF"
    And its minimum purchase amount should be tax excluded
    And its minimum purchase amount should be shipping included
    And it should give free shipping
    And it should give a reduction of "15" in currency "USD" which is tax included and applies to order without shipping

  Scenario: Create a cart rule with percentage discount
    When I want to create a new cart rule
    And I specify its name in default language as "50% off promo"
    And I specify its "description" as "Discount for whole catalog for one hour"
    And I specify that its active from "2019-01-01 11:00:00"
    And I specify that its active until "2019-01-01 12:00:00"
    And I specify that its "quantity" is "10"
    And I specify that its "quantity per user" is "2"
    And I specify that its "priority" is "1"
    And I specify that partial use is enabled for it
    And I specify its status as disabled
    And I specify that it should be highlighted in cart
    And I specify its "code" as "HAPPY_HOUR"
    And its minimum purchase amount in currency "USD" is "99.99"
    And its minimum purchase amount is tax included
    And its minimum purchase amount is shipping excluded
    And it gives a percentage reduction of "50" which excludes discounted products and applies to cheapest product
    When I save it
    Then its name in default language should be "50% off promo"
    And its "description" should be "Discount for whole catalog for one hour"
    And it should be active from "2019-01-01 11:00:00"
    And it should be active until "2019-01-01 12:00:00"
    And its "quantity" should be "10"
    And its "quantity per user" should be "2"
    And its "priority" should be "1"
    And its "partial use" should be "enabled"
    And its "status" should be "disabled"
    And it should be highlighted in cart
    And its "code" should be "HAPPY_HOUR"
    And it should have minimum purchase amount of "99.99" in currency "USD"
    And its minimum purchase amount should be tax included
    And its minimum purchase amount should be shipping excluded
    And it should give a percentage reduction of "50" which excludes discounted products and applies to cheapest product
