# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s category --tags category-visibility-per-shop
@restore-all-tables-before-feature
@clear-cache-before-feature
@category-visibility-per-shop
Feature: Category visibility per shop
  PrestaShop allows BO users to manage categories for products
  As a BO user
  I must be able to enable or disable a category independently for each shop
  in a multistore installation

  Background:
    Given language "en" with locale "en-US" exists
    And language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    And category "home" in default language named "Home" exists
    And category "root" in default language named "Root" exists
    And category "root" is the root category and it cannot be edited
    And I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop_2" and color "red" for the group "default_shop_group"
    And single shop context is loaded
    And category "home" is set as the home category for shop "shop1"

  Scenario: Disabling a category for one shop keeps it enabled for the other shop
    Given I add new category "perShopCategory" with following details:
      | name[en-US]         | Per shop category |
      | active              | true              |
      | parent category     | home              |
      | link rewrite[en-US] | per-shop-category |
      | associated shops    | shop1,shop2       |
    # The category starts enabled for both shops
    When shop context "test_shop" is loaded
    Then category "perShopCategory" should have following details:
      | active | true |
    When shop context "test_shop_2" is loaded
    Then category "perShopCategory" should have following details:
      | active | true |
    # Disabling it while shop2 is the current context only affects shop2
    When I edit category "perShopCategory" with following details:
      | active | false |
    Then category "perShopCategory" should have following details:
      | active | false |
    # ... the category is still enabled for shop1
    When shop context "test_shop" is loaded
    Then category "perShopCategory" should have following details:
      | active | true |
