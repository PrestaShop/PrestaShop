# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-categories-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-after-feature
@product-multishop
@update-categories-multishop
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
    And I edit home category "home" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I edit category "clothes" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    And I edit category "accessories" with following details:
      | associated shops | shop1,shop2,shop3,shop4 |
    # Men is only associated in shop1
    And I edit category "men" with following details:
      | associated shops | shop1 |
    # Women is only associated in shop2 and is the default category
    And I edit category "women" with following details:
      | associated shops | shop2 |
    And I set "women" as default category for shop shop2

  Scenario: I assign product to categories which are associated to different shops, the default fallback depends on which shops are associated
    Given I add product "product1" to shop shop2 with following information:
      | name[en-US] | eastern european tracksuit |
      | type        | standard                   |
    # Default category associated is the one from the shop
    Then product "product1" should be assigned to following categories for shop shop2:
      | id reference | name  | is default |
      | women        | Women | true       |
    When I set following shops for product "product1":
      | source shop | shop2       |
      | shops       | shop1,shop2 |
    # Women is not associated to shop1, we need to pick another default category for shop1 (home is picked since it's the shop's default category)
    # Since the associations are common to all shops, shop2 is already assigned to home now
    Then product "product1" should be assigned to following categories for shop shop2:
      | id reference | name  | is default |
      | women        | Women | true       |
      | home         | Home  | false      |
    And product "product1" should be assigned to following categories for shop shop1:
      | id reference | name | is default |
      | home         | Home | true       |
    # Now we set categories for shop1 with some categories common to all shops, and one specific to shop1 (men)
    When I assign product product1 to following categories for shop shop1:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    Then product product1 should be assigned to following categories for shops "shop1":
      | id reference | name    | is default |
      | home         | Home    | false      |
      | men          | Men     | false      |
      | clothes      | Clothes | true       |
    # Men is filtered since it doesn't belong to shop2, women is still the default category since it was not removed
    And product product1 should be assigned to following categories for shops "shop2":
      | id reference | name    | is default |
      | home         | Home    | false      |
      | clothes      | Clothes | false      |
      | women        | Women   | true       |
    # Now update for shop2, clothes is associated to shop2 but not in the updated list so it will be removed
    When I assign product product1 to following categories for shop shop2:
      | categories       | [home, accessories, women] |
      | default category | accessories                |
    Then product product1 should be assigned to following categories for shops "shop2":
      | id reference | name        | is default |
      | home         | Home        | false      |
      | accessories  | Accessories | true       |
      | women        | Women       | false      |
    # Clothes has been removed but since Men doesn't belong to shop2 it was not removed
    # Since clothes has been removed a new default category has been assigned though
    And product product1 should be assigned to following categories for shops "shop1":
      | id reference | name        | is default |
      | home         | Home        | true       |
      | accessories  | Accessories | false      |
      | men          | Men         | false      |
    # Now delete all categories, at least the default one of each shop should remain
    When I delete all categories from product product1 for shop shop2
    # Shop2 had all its categories removed so it has to use the shop's default one
    Then product product1 should be assigned to following categories for shops "shop2":
      | id reference | name  | is default |
      | women        | Women | true       |
    # Shop1 still had the Men category so it can be kept as the default one
    And product product1 should be assigned to following categories for shops "shop1":
      | id reference | name | is default |
      | men          | Men  | true       |
