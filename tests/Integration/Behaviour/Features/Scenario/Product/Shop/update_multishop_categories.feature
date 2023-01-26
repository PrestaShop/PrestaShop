# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-categories
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multi-shop
@update-multi-shop-categories
Feature: Copy product from shop to shop.
  As a BO user I want to be able to copy product from shop to shop.

  Background:
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
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And category "women" in default language named "Women" exists
    And category "accessories" in default language named "Accessories" exists

  Scenario: I assign product to categories they are the same on all shops, but default one can be different
    Given I add product "product1" to shop shop2 with following information:
      | name[en-US] | eastern european tracksuit |
      | type        | standard                   |
    When I copy product productWithPrices from shop shop2 to shop shop1
    And product "product1" should be assigned to following categories for shops "shop1,shop2":
      | id reference | name | is default |
      | home         | Home | true       |
    When I assign product product1 to following categories for shop shop1:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    # The associations are shared on all shops, but the default category can be different
    Then product product1 should be assigned to following categories for shops "shop1":
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |
    And product product1 should be assigned to following categories for shops "shop2":
      | id reference | name    | is default |
      | home         | Home    | true       |
      | men          | Men     | false      |
      | clothes      | Clothes | false      |
