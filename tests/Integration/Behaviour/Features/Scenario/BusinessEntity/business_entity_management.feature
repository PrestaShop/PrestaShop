# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s business_entity --tags business-entity-management
@restore-all-tables-before-feature
@business-entity-management
Feature: Add business entity
  In order to manage B2B entities
  As a BO user
  I need to be able to add a new business entity

  Scenario: Add a new business entity with billing address used as shipping address
    Given there is a business entity with the following details:
      | name                         | Test Business Entity |
      | legal_name                   | Test Legal Name      |
      | external_ref                 | EXT-001              |
      | delivery_authorized          | 1                    |
      | status                       | active               |
      | billing_as_shipping          | 1                    |
    And the business entity has the following billing addresses:
      | alias   | address1      | city  | postcode | country_id | is_default |
      | Billing | 123 Main St   | Paris | 75001    | 8          | 1          |
    When I add the business entity
    Then the business entity should be successfully created
    And the business entity "Test Business Entity" should exist in the database
    And the business entity "Test Business Entity" should have 1 address
    And the address with alias "Billing" for business entity "Test Business Entity" should have type "both" and be default

  Scenario: Add a new business entity with separate billing and shipping addresses
    Given there is a business entity with the following details:
      | name                         | Separate Addresses Entity |
      | legal_name                   | Separate Legal Name       |
      | external_ref                 | EXT-002                   |
      | delivery_authorized          | 1                         |
      | status                       | active                    |
      | billing_as_shipping          | 0                         |
    And the business entity has the following billing addresses:
      | alias   | address1      | city  | postcode | country_id | is_default |
      | Billing | 456 Invoice St| Lyon  | 69001    | 8          | 1          |
    And the business entity has the following shipping addresses:
      | alias    | address1       | city      | postcode | country_id | is_default | phone      | phone_mobile |
      | Shipping | 789 Delivery Rd| Marseille | 13001    | 8          | 1          | 0499887766 | 0655443322   |
    When I add the business entity
    Then the business entity should be successfully created
    And the business entity "Separate Addresses Entity" should exist in the database
    And the business entity "Separate Addresses Entity" should have 2 addresses
    And the address with alias "Billing" for business entity "Separate Addresses Entity" should have type "invoice"
    And the address with alias "Shipping" for business entity "Separate Addresses Entity" should have type "delivery"
    And the address with alias "Shipping" for business entity "Separate Addresses Entity" should have phone "0499887766" and mobile phone "0655443322"

  Scenario: Add a new business entity with a non-default customer group and shop
    Given there is a business entity with the following details:
      | name                         | Non Default Scope Entity |
      | legal_name                   | Non Default Legal        |
      | external_ref                 | EXT-003                  |
      | delivery_authorized          | 1                        |
      | status                       | active                   |
      | billing_as_shipping          | 1                        |
      | shop_id                      | 1                        |
      | customer_group_id            | 1                        |
    And the business entity has the following billing addresses:
      | alias   | address1      | city     | postcode | country_id | is_default |
      | Billing | 1 Scope Rd    | Toulouse | 31000    | 8          | 1          |
    When I add the business entity
    Then the business entity should be successfully created
    And the business entity "Non Default Scope Entity" should exist in the database
    And the business entity "Non Default Scope Entity" should belong to customer group 1
    And the business entity "Non Default Scope Entity" should be attached to shop 1

  Scenario: Add a new business entity with phone numbers on the billing address
    Given there is a business entity with the following details:
      | name                         | Phone Entity     |
      | legal_name                   | Phone Legal Name |
      | external_ref                 | EXT-004          |
      | delivery_authorized          | 1                |
      | status                       | active           |
      | billing_as_shipping          | 1                |
    And the business entity has the following billing addresses:
      | alias   | address1    | city  | postcode | country_id | is_default | phone      | phone_mobile |
      | Billing | 12 Phone St | Paris | 75001    | 8          | 1          | 0102030405 | 0611223344   |
    When I add the business entity
    Then the business entity should be successfully created
    And the business entity "Phone Entity" should exist in the database
    And the address with alias "Billing" for business entity "Phone Entity" should have phone "0102030405" and mobile phone "0611223344"
