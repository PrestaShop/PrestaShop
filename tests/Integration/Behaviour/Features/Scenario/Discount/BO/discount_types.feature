# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-types
@discount-types
Feature: Discount Types
  PrestaShop provides discount types
  As a developer
  I must be able to retrieve the list of available discount types

  Background:
    Given I enable feature flag "discount"

  Scenario: List all discount types
    Then I should receive the following discount types:
      | type          | core | enabled | names[en-US]        | descriptions[en-US]                              |
      | cart_level    | true | true    | On cart amount     | Discount applied to cart                          |
      | product_level | true | true    | On catalog products | Discount applied to specific products            |
      | free_shipping | true | true    | On free shipping   | Discount that provides free shipping to the order |
      | free_gift     | true | true    | On free gift       | Discount that provides a free gift product        |
      | order_level   | true | true    | On total order     | Discount applied to the order                    |
