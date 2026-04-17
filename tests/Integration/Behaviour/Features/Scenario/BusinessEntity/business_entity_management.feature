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
      | flag_delivery_authorized     | 1                    |
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
      | flag_delivery_authorized     | 1                         |
      | status                       | active                    |
      | billing_as_shipping          | 0                         |
    And the business entity has the following billing addresses:
      | alias   | address1      | city  | postcode | country_id | is_default |
      | Billing | 456 Invoice St| Lyon  | 69001    | 8          | 1          |
    And the business entity has the following shipping addresses:
      | alias    | address1       | city      | postcode | country_id | is_default |
      | Shipping | 789 Delivery Rd| Marseille | 13001    | 8          | 1          |
    When I add the business entity
    Then the business entity should be successfully created
    And the business entity "Separate Addresses Entity" should exist in the database
    And the business entity "Separate Addresses Entity" should have 2 addresses
    And the address with alias "Billing" for business entity "Separate Addresses Entity" should have type "invoice"
    And the address with alias "Shipping" for business entity "Separate Addresses Entity" should have type "delivery"
