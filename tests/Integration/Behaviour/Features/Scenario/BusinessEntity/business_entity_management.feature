# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s business_entity --tags business-entity-management
@restore-all-tables-before-feature
@business-entity-management
Feature: Manage business entities
  In order to manage B2B entities
  As a BO user
  I need to be able to add, view, list and edit business entities

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

  Scenario: View a business entity splits its addresses into billing and shipping and resolves the country
    Given there is a business entity with the following details:
      | name                | Viewable Entity      |
      | legal_name          | Viewable Legal Name  |
      | external_ref        | EXT-006              |
      | delivery_authorized | 1                    |
      | status              | active               |
      | billing_as_shipping | 0                    |
    And the business entity has the following billing addresses:
      | alias   | address1       | city  | postcode | country_id | is_default |
      | Billing | 456 Invoice St | Lyon  | 69001    | 8          | 1          |
    And the business entity has the following shipping addresses:
      | alias    | address1        | city      | postcode | country_id | is_default |
      | Shipping | 789 Delivery Rd | Marseille | 13001    | 8          | 1          |
    When I add the business entity
    Then the business entity "Viewable Entity" should have the following view properties:
      | name                | Viewable Entity     |
      | legal_name          | Viewable Legal Name |
      | status              | active              |
      | addresses_count     | 2                   |
      | customer_group_name | Customer            |
    And the business entity "Viewable Entity" should have created and updated timestamps
    And the business entity "Viewable Entity" should have 1 billing address
    And the business entity "Viewable Entity" should have 1 shipping address
    And the business entity "Viewable Entity" "invoice" address "Billing" formatted address should contain "France"
    And the business entity "Viewable Entity" "invoice" address "Billing" formatted address should contain "Lyon"
    And the business entity "Viewable Entity" "invoice" address "Billing" formatted address should not contain "business-entity"
    And the business entity "Viewable Entity" "delivery" address "Shipping" formatted address should contain "Marseille"
    And the business entity "Viewable Entity" "delivery" address "Shipping" formatted address should not contain "business-entity"

  Scenario: A billing address also used for shipping is counted once in the summary
    Given there is a business entity with the following details:
      | name                | Shared Address Entity |
      | legal_name          | Shared Legal Name     |
      | external_ref        | EXT-005               |
      | delivery_authorized | 1                     |
      | status              | active                |
      | billing_as_shipping | 1                     |
    And the business entity has the following billing addresses:
      | alias   | address1     | city  | postcode | country_id | is_default |
      | Billing | 1 Shared St  | Paris | 75001    | 8          | 1          |
    When I add the business entity
    Then the business entity "Shared Address Entity" should have 1 billing address
    And the business entity "Shared Address Entity" should have 1 shipping address
    And the business entity "Shared Address Entity" should have the following view properties:
      | addresses_count | 1 |

  Scenario: A soft-deleted address is no longer exposed by the business entity view
    Given there is a business entity with the following details:
      | name                | Deleted Address Entity |
      | legal_name          | Deleted Legal Name     |
      | external_ref        | EXT-007                |
      | delivery_authorized | 1                      |
      | status              | active                 |
      | billing_as_shipping | 0                      |
    And the business entity has the following billing addresses:
      | alias    | address1      | city | postcode | country_id | is_default |
      | Billing  | 1 Kept St     | Lyon | 69001    | 8          | 1          |
      | Obsolete | 2 Obsolete St | Lyon | 69002    | 8          | 0          |
    And the business entity has the following shipping addresses:
      | alias    | address1        | city      | postcode | country_id | is_default |
      | Shipping | 789 Delivery Rd | Marseille | 13001    | 8          | 1          |
    When I add the business entity
    And the address with alias "Obsolete" for business entity "Deleted Address Entity" is soft deleted
    Then the business entity "Deleted Address Entity" should have 1 billing address
    And the business entity "Deleted Address Entity" should have 1 shipping address
    And the business entity "Deleted Address Entity" should have the following view properties:
      | addresses_count | 2 |

  Scenario: The default address is listed first whatever the creation order
    Given there is a business entity with the following details:
      | name                | Ordered Address Entity |
      | legal_name          | Ordered Legal Name     |
      | external_ref        | EXT-008                |
      | delivery_authorized | 1                      |
      | status              | active                 |
      | billing_as_shipping | 0                      |
    And the business entity has the following billing addresses:
      | alias     | address1       | city | postcode | country_id | is_default |
      | Secondary | 1 Secondary St | Lyon | 69001    | 8          | 0          |
      | Main      | 2 Main St      | Lyon | 69002    | 8          | 1          |
    And the business entity has the following shipping addresses:
      | alias    | address1        | city      | postcode | country_id | is_default |
      | Shipping | 789 Delivery Rd | Marseille | 13001    | 8          | 1          |
    When I add the business entity
    Then the business entity "Ordered Address Entity" should have 2 billing addresses
    And the first "invoice" address of business entity "Ordered Address Entity" should be "Main"

  Scenario: Viewing a business entity that does not exist raises a not found error
    When I view the business entity with id 999999
    Then I should get an error that the business entity was not found

  Scenario: The summary counts the b2b customers linked to the business entity
    Given there is a business entity named "Linked Entity" with status "active"
    And the business entity "Linked Entity" is linked to 2 b2b customers
    Then the business entity "Linked Entity" should have the following view properties:
      | linked_customers_count | 2 |

  Scenario: The pending count reflects only business entities awaiting validation
    Given there is a business entity named "Pending Alpha" with status "pending"
    And there is a business entity named "Pending Beta" with status "pending"
    And there is a business entity named "Active Gamma" with status "active"
    Then the pending business entities count should be 2

  Scenario: Edit the general information of a business entity
    Given there is a business entity with the following details:
      | name                | Editable Entity |
      | legal_name          | Editable Legal  |
      | external_ref        | EXT-010         |
      | delivery_authorized | 0               |
      | status              | pending         |
      | billing_as_shipping | 1               |
    And the business entity has the following billing addresses:
      | alias   | address1  | city  | postcode | country_id | is_default |
      | Billing | 1 Edit St | Paris | 75001    | 8          | 1          |
    When I add the business entity
    And I edit the business entity "Editable Entity" with the following details:
      | name                | Edited Entity |
      | legal_name          | Edited Legal  |
      | external_ref        | EXT-010-B     |
      | delivery_authorized | 1             |
      | status              | active        |
      | customer_group_id   | 1             |
    Then the business entity "Edited Entity" should have the following details:
      | legal_name          | Edited Legal |
      | external_ref        | EXT-010-B    |
      | delivery_authorized | 1            |
      | status              | active       |
      | customer_group_id   | 1            |
    And the business entity "Edited Entity" should have 1 address

  Scenario: Editing a business entity that does not exist raises a not found error
    When editing the business entity with id 999999 should raise a not found error
