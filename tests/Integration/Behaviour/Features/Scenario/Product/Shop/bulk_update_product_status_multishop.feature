# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags bulk-update-product-status-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@bulk-update-product-status-multishop
Feature: Copy product from shop to shop.
  As a BO user I want to be able to copy product from shop to shop.

  Scenario:
    Given I enable multishop feature
    And language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_second_shop" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And language "french" with locale "fr-FR" exists
    And I add product "product1" to shop shop1 with following information:
      | name[en-US] | product 1 |
      | type        | standard  |
    And I set following shops for product "product1":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    And product product1 type should be standard for shops "shop1,shop2,shop3,shop4"
    And product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And I add product "product2" to shop shop1 with following information:
      | name[en-US] | product 2 |
      | type        | standard  |
    And I set following shops for product "product2":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    And product product2 type should be standard for shops "shop1,shop2,shop3,shop4"
    And product "product2" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product2" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And I add product "product3" to shop shop1 with following information:
      | name[en-US] | product 3 |
      | type        | standard  |
    And I set following shops for product "product3":
      | source shop | shop1                   |
      | shops       | shop1,shop2,shop3,shop4 |
    And product product3 type should be standard for shops "shop1,shop2,shop3,shop4"
    And product "product3" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product3" should not be indexed for shops "shop1,shop2,shop3,shop4"

  Scenario: I update product statuses for specific shop
    When I bulk change status to be enabled for following products for shop "shop2":
      | reference |
      | product1  |
      | product3  |
    Then product "product1" should be enabled for shops "shop2"
    And product "product1" should be indexed for shops "shop2"
    And product "product1" should be disabled for shops "shop1,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop3,shop4"
    And product "product2" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product2" should not be indexed for shops "shop1,shop2,shop3,shop4"
    Then product "product3" should be enabled for shops "shop2"
    And product "product3" should be indexed for shops "shop2"
    And product "product3" should be disabled for shops "shop1,shop3,shop4"
    And product "product3" should not be indexed for shops "shop1,shop3,shop4"

  Scenario: I update product statuses for all shops
    When I bulk change status to be enabled for following products for all shops:
      | reference |
      | product2  |
    Then product "product1" should be enabled for shops "shop2"
    And product "product1" should be indexed for shops "shop2"
    And product "product1" should be disabled for shops "shop1,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop3,shop4"
    And product "product2" should be enabled for shops "shop1,shop2,shop3,shop4"
    And product "product2" should be indexed for shops "shop1,shop2,shop3,shop4"
    Then product "product3" should be enabled for shops "shop2"
    And product "product3" should be indexed for shops "shop2"
    And product "product3" should be disabled for shops "shop1,shop3,shop4"
    And product "product3" should not be indexed for shops "shop1,shop3,shop4"
    When I bulk change status to be disabled for following products for all shops:
      | reference |
      | product1  |
      | product2  |
      | product3  |
    Then product "product1" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product1" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And product "product2" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product2" should not be indexed for shops "shop1,shop2,shop3,shop4"
    And product "product3" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product3" should not be indexed for shops "shop1,shop2,shop3,shop4"

  Scenario: I update product statuses for shop group
    When I bulk change status to be enabled for following products for shop group "test_second_shop_group":
      | reference |
      | product1  |
      | product2  |
    Then product "product1" should be disabled for shops "shop1,shop2"
    And product "product1" should not be indexed for shops "shop1,shop2"
    And product "product1" should be enabled for shops "shop3,shop4"
    And product "product1" should be indexed for shops "shop3,shop4"
    And product "product2" should be disabled for shops "shop1,shop2"
    And product "product2" should not be indexed for shops "shop1,shop2"
    And product "product2" should be enabled for shops "shop3,shop4"
    And product "product2" should be indexed for shops "shop3,shop4"
    And product "product3" should be disabled for shops "shop1,shop2,shop3,shop4"
    And product "product3" should not be indexed for shops "shop1,shop2,shop3,shop4"
