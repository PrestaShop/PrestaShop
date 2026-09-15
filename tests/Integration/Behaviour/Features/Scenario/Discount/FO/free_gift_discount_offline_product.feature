# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags free-gift-discount-offline-product
@restore-all-tables-before-feature
@restore-languages-after-feature
@free-gift-discount-offline-product
Feature: Free gift discount with an offline product
  PrestaShop refuses a free gift discount whose gift product is no longer sold
  As a customer
  I must not receive a gift the shop has taken offline

  Background:
    Given there is a customer named "testCustomer" whose email is "pub2@prestashop.com"
    And language with iso code "en" is the default one
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And currency "usd" is the default one
    And I enable feature flag "discount"

  Scenario: A gift product that is still online can be offered
    Given there is a product in the catalog named "bought-online" with a price of 20.0 and 100 items in stock
    And there is a product in the catalog named "gift-online" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "online_gift_discount" with following properties:
      | name[en-US]  | Online gift         |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2036-12-31 00:00:00 |
      | code         | ONLINEGIFT          |
      | gift_product | gift-online         |
    And I create an empty cart "online_gift_cart" for customer "testCustomer"
    And I add 1 product "bought-online" to the cart "online_gift_cart"
    And I use a voucher "online_gift_discount" on the cart "online_gift_cart"
    Then cart "online_gift_cart" should contain gift product "gift-online"

  Scenario: A gift product taken offline cannot be offered
    Given there is a product in the catalog named "bought-offline" with a price of 20.0 and 100 items in stock
    And there is a product in the catalog named "gift-offline" with a price of 10.0 and 100 items in stock
    When I create a "free_gift" discount "offline_gift_discount" with following properties:
      | name[en-US]  | Offline gift        |
      | active       | true                |
      | valid_from   | 2025-01-01 00:00:00 |
      | valid_to     | 2036-12-31 00:00:00 |
      | code         | OFFLINEGIFT         |
      | gift_product | gift-offline        |
    And I create an empty cart "offline_gift_cart" for customer "testCustomer"
    And I add 1 product "bought-offline" to the cart "offline_gift_cart"
    And I update product "gift-offline" with following values:
      | active | false |
    And I use a voucher "offline_gift_discount" on the cart "offline_gift_cart"
    Then I should get an error that the discount is invalid
    And cart "offline_gift_cart" should not contain product "gift-offline"
