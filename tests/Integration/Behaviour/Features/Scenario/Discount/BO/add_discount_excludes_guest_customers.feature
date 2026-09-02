# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount-excludes-guests
@restore-all-tables-before-feature
@restore-languages-after-feature
@add-discount-excludes-guests
Feature: Add discount with single customer eligibility excludes guest customers
  PrestaShop should prevent BO users from creating discounts for guest customers
  As a BO user
  I should not be able to create discounts that are assigned to guest customers
  Guest customers should be excluded from the customer search when creating discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given there is a customer named "john_doe" whose email is "john.doe@example.com"
    And there is a guest customer named "guest_customer" whose email is "guest@example.com"

  Scenario: Cannot create a discount limited to a guest customer
    When I create a "cart_level" discount "discount_for_guest" with following properties:
      | name[en-US]              | Guest Discount      |
      | name[fr-FR]              | Réduction Invité    |
      | active                   | true                |
      | valid_from               | 2025-01-01 00:00:00 |
      | valid_to                 | 2025-12-31 23:59:59 |
      | code                     | GUEST_2025          |
      | reduction_percent        | 20.0                |
      | customer                 | guest_customer      |
      | total_quantity           | 100                 |
      | quantity_per_user        | 1                   |
      | minimum_product_quantity | 0                   |
    Then I should get an error that discount cannot be assigned to guest customers

  Scenario: Can create a discount for a regular customer but not for a guest
    When I create a "cart_level" discount "discount_for_regular" with following properties:
      | name[en-US]              | Regular Customer Discount |
      | name[fr-FR]              | Réduction Client          |
      | active                   | true                      |
      | valid_from               | 2025-01-01 00:00:00       |
      | valid_to                 | 2025-12-31 23:59:59       |
      | code                     | REGULAR_2025              |
      | reduction_percent        | 15.0                      |
      | customer                 | john_doe                  |
      | total_quantity           | 100                       |
      | quantity_per_user        | 1                         |
      | minimum_product_quantity | 0                         |
    Then discount "discount_for_regular" should have the following properties:
      | name[en-US] | Regular Customer Discount |
      | name[fr-FR] | Réduction Client          |
      | customer    | john_doe                  |
    # This should fail if we try to update to guest customer
    When I update discount "discount_for_regular" with the following properties:
      | customer          | guest_customer |
    Then I should get an error that discount cannot be assigned to guest customers
