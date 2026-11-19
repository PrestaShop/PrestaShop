# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags update-legacy-discount-without-type
@restore-all-tables-before-feature
@update-legacy-discount-without-type
Feature: Update a legacy cart rule without discount type
  PrestaShop allows BO users to edit cart rules created before the discount types were introduced
  As a BO user
  I must be able to edit them with the type deduced from their settings

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one

  Scenario: The type of a legacy cart rule is deduced, and only saved when it is given
    Given there is a product "hummingbird-tshirt" with name "Hummingbird printed t-shirt"
    And there is a cart rule "legacy_cart_rule" with following properties:
      | name[en-US]         | Legacy cart rule   |
      | discount_percentage | 10                 |
      | free_shipping       | true               |
      | gift_product        | hummingbird-tshirt |
      | code                | LEGACY10           |
    Then discount "legacy_cart_rule" should have the type "cart_level" deduced from its legacy settings
    And discount "legacy_cart_rule" should have the following unsupported legacy settings: "free_shipping,free_gift"
    And discount "legacy_cart_rule" should have the following properties:
      | reduction_percent | 10.0     |
      | code              | LEGACY10 |
    # Changing the status does not save the deduced type
    When I disable discount "legacy_cart_rule"
    Then discount "legacy_cart_rule" is disabled
    And discount "legacy_cart_rule" should have the type "cart_level" deduced from its legacy settings
    When I bulk enable discounts "legacy_cart_rule"
    Then discount "legacy_cart_rule" is enabled
    And discount "legacy_cart_rule" should have the type "cart_level" deduced from its legacy settings
    # The discount form gives the type, which is then saved
    When I update discount "legacy_cart_rule" with the following properties:
      | type      | cart_level |
      | highlight | true       |
    Then discount "legacy_cart_rule" should have the type "cart_level" saved
    And discount "legacy_cart_rule" should have the following properties:
      | highlight         | true     |
      | reduction_percent | 10.0     |
      | code              | LEGACY10 |
    # Free shipping and the free gift are kept as is
    And there is a cart rule "legacy_cart_rule" with following properties:
      | name[en-US]         | Legacy cart rule   |
      | discount_percentage | 10                 |
      | free_shipping       | true               |
      | gift_product        | hummingbird-tshirt |
      | code                | LEGACY10           |

  Scenario: The type of a discount cannot be changed
    When I create a "cart_level" discount "typed_discount" with following properties:
      | name[en-US]       | Typed discount |
      | reduction_percent | 10.0           |
    When I update discount "typed_discount" with the following properties:
      | type              | product_level |
      | reduction_percent | 20.0          |
    Then I should get an error that the discount type cannot be changed
    And discount "typed_discount" should have the type "cart_level" saved
    And discount "typed_discount" should have the following properties:
      | reduction_percent | 10.0 |
    When I update discount "typed_discount" with the following properties:
      | type              | cart_level |
      | reduction_percent | 20.0       |
    Then discount "typed_discount" should have the type "cart_level" saved
    And discount "typed_discount" should have the following properties:
      | reduction_percent | 20.0 |
