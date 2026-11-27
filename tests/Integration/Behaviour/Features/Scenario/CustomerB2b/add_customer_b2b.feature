# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_b2b --tags add-customer-b2b
@restore-all-tables-before-feature
@add-customer-b2b
Feature: Add B2B customers
  As a BO user
  I need to be able to attach a B2B profile to a customer

  Scenario: Attach a B2B profile to an existing customer
    Given I create customer "customer1" with following details:
      | firstName | first                    |
      | lastName  | customer                 |
      | email     | customer1@prestashop.com |
      | password  | PrestaShopForever1_!     |
    And there is a business entity with the following details:
      | name                | Acme Corp     |
      | legal_name          | Acme Corp Ltd |
      | status              | active        |
      | billing_as_shipping | 1             |
      | customer_group_id   | 3             |
    And the business entity has the following billing addresses:
      | alias   | address1   | city  | postcode | country_id | is_default |
      | Billing | 123 Street | Paris | 75001    | 8          | 1          |
    And I add the business entity
    When I add a B2B profile to customer "customer1" with the following details:
      | business_entity | Acme Corp      |
      | role            | ROLE_B2B_BUYER |
      | status          | active         |
      | external_ref    | ERP-0001       |
    Then customer "customer1" should have a B2B profile with the following details:
      | status          | active         |
      | external_ref    | ERP-0001       |
      | business_entity | Acme Corp      |
      | role            | ROLE_B2B_BUYER |
      | is_default      | true           |
    And the creation of the B2B profile of customer "customer1" should be logged

  Scenario: Attach a B2B profile with the default status and no external reference
    Given I create customer "defaults" with following details:
      | firstName | defaults                |
      | lastName  | customer                |
      | email     | defaults@prestashop.com |
      | password  | PrestaShopForever1_!    |
    And there is a business entity named "Defaults Corp" with status "active"
    When I add a B2B profile to customer "defaults" with the following details:
      | business_entity | Defaults Corp  |
      | role            | ROLE_B2B_ADMIN |
    Then customer "defaults" should have a B2B profile with the following details:
      | status          | pending        |
      | external_ref    |                |
      | business_entity | Defaults Corp  |
      | role            | ROLE_B2B_ADMIN |
      | is_default      | true           |

  Scenario: A customer cannot get a second B2B profile
    Given I create customer "existing_b2b" with following details:
      | firstName | existing                    |
      | lastName  | customer                    |
      | email     | existing-b2b@prestashop.com |
      | password  | PrestaShopForever1_!        |
    And there is a business entity named "First Corp" with status "active"
    And there is a business entity named "Second Corp" with status "active"
    And I add a B2B profile to customer "existing_b2b" with the following details:
      | business_entity | First Corp     |
      | role            | ROLE_B2B_BUYER |
    When I add a B2B profile to customer "existing_b2b" with the following details:
      | business_entity | Second Corp    |
      | role            | ROLE_B2B_ADMIN |
    Then I should get an error that the customer already has a B2B profile
    And customer "existing_b2b" should have a B2B profile with the following details:
      | business_entity | First Corp     |
      | role            | ROLE_B2B_BUYER |

  Scenario: A guest cannot get a B2B profile
    Given I create customer "guest" with following details:
      | firstName | guest                |
      | lastName  | customer             |
      | email     | guest@prestashop.com |
      | isGuest   | true                 |
    And there is a business entity named "Guest Corp" with status "active"
    When I add a B2B profile to customer "guest" with the following details:
      | business_entity | Guest Corp     |
      | role            | ROLE_B2B_BUYER |
    Then I should get an error that a guest customer cannot have a B2B profile
    And customer "guest" should not have a B2B profile

  Scenario: A deleted customer cannot get a B2B profile
    Given I create customer "deleted" with following details:
      | firstName | deleted                |
      | lastName  | customer               |
      | email     | deleted@prestashop.com |
      | password  | PrestaShopForever1_!   |
    And customer "deleted" has been deleted
    And there is a business entity named "Deleted Customer Corp" with status "active"
    When I add a B2B profile to customer "deleted" with the following details:
      | business_entity | Deleted Customer Corp |
      | role            | ROLE_B2B_BUYER        |
    Then I should get an error that a deleted customer cannot have a B2B profile
    And customer "deleted" should not have a B2B profile

  Scenario: A customer that does not exist cannot get a B2B profile
    Given customer "imaginary" does not exist
    And there is a business entity named "Imaginary Customer Corp" with status "active"
    When I add a B2B profile to customer "imaginary" with the following details:
      | business_entity | Imaginary Customer Corp |
      | role            | ROLE_B2B_BUYER          |
    Then I should get error that customer was not found

  Scenario: A B2B profile cannot be attached to a business entity that does not exist
    Given I create customer "jobless" with following details:
      | firstName | jobless                |
      | lastName  | customer               |
      | email     | jobless@prestashop.com |
      | password  | PrestaShopForever1_!   |
    When I add a B2B profile to customer "jobless" with the following details:
      | business_entity_id | 999999         |
      | role               | ROLE_B2B_BUYER |
    Then I should get an error that the business entity was not found
    And customer "jobless" should not have a B2B profile

  Scenario: A B2B profile cannot be attached to a deleted business entity
    Given I create customer "closed_be" with following details:
      | firstName | closed BE                |
      | lastName  | customer                 |
      | email     | closed-be@prestashop.com |
      | password  | PrestaShopForever1_!     |
    And there is a business entity named "Closed Corp" with status "active"
    And I delete the business entity "Closed Corp"
    When I add a B2B profile to customer "closed_be" with the following details:
      | business_entity | Closed Corp    |
      | role            | ROLE_B2B_BUYER |
    Then I should get an error that the business entity was not found
    And customer "closed_be" should not have a B2B profile

  Scenario: A B2B profile cannot have a role that does not exist
    Given I create customer "roleless" with following details:
      | firstName | roleless                |
      | lastName  | customer                |
      | email     | roleless@prestashop.com |
      | password  | PrestaShopForever1_!    |
    And there is a business entity named "Roleless Corp" with status "active"
    When I add a B2B profile to customer "roleless" with the following details:
      | business_entity | Roleless Corp |
      | role_id         | 999999        |
    Then I should get an error that the B2B role was not found
    And customer "roleless" should not have a B2B profile

  Scenario: A B2B profile external reference cannot exceed 255 characters
    Given I create customer "long_ref" with following details:
      | firstName | long ref                |
      | lastName  | customer                |
      | email     | long-ref@prestashop.com |
      | password  | PrestaShopForever1_!    |
    And there is a business entity named "Long Ref Corp" with status "active"
    When I add a B2B profile to customer "long_ref" in business entity "Long Ref Corp" with an external reference of 256 characters
    Then I should get an error that the B2B profile external reference is invalid
    And customer "long_ref" should not have a B2B profile
