# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-for-product
@restore-all-tables-before-feature
@clear-cache-before-feature
@carrier-for-product
Feature: Get carriers for product
  As a BO user I want to retrieve the carriers associated with a specific product
  In order to propose available shipping options when creating or editing a shipment

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I set up shop context to single shop shop1
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one

  Scenario: Product with specific carriers assigned returns only those carriers
    Given I add product "cfp_product1" with following information:
      | name[en-US] | Bottle of beer |
      | type        | standard       |
    And I create carrier "cfp_carrier1" with specified properties:
      | name | Express Carrier |
    And I create carrier "cfp_carrier2" with specified properties:
      | name | Standard Carrier |
    And I create carrier "cfp_carrier3" with specified properties:
      | name | Pickup Carrier |
    When I assign product "cfp_product1" with following carriers:
      | cfp_carrier1 |
      | cfp_carrier2 |
    Then the product "cfp_product1" should have the following carriers assigned:
      | name             |
      | Express Carrier  |
      | Standard Carrier |

  Scenario: Product with a single carrier assigned returns only that carrier
    Given I add product "cfp_product2" with following information:
      | name[en-US] | Bottle of rum |
      | type        | standard      |
    And I create carrier "cfp_solo_carrier" with specified properties:
      | name | Solo Carrier |
    When I assign product "cfp_product2" with following carriers:
      | cfp_solo_carrier |
    Then the product "cfp_product2" should have the following carriers assigned:
      | name         |
      | Solo Carrier |

  Scenario: Deleted carrier is excluded even when assigned to the product
    Given I add product "cfp_product3" with following information:
      | name[en-US] | Bottle of whiskey |
      | type        | standard          |
    And I create carrier "cfp_active_carrier" with specified properties:
      | name | Active Carrier |
    And I create carrier "cfp_deleted_carrier" with specified properties:
      | name | Deleted Carrier |
    And I assign product "cfp_product3" with following carriers:
      | cfp_active_carrier  |
      | cfp_deleted_carrier |
    And I soft delete carrier "cfp_deleted_carrier"
    Then the product "cfp_product3" should have the following carriers assigned:
      | name           |
      | Active Carrier |
