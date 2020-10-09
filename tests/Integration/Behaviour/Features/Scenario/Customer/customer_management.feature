# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer
@reset-database-before-feature
Feature: Customer Management
  PrestaShop allows BO users to manage customers in the Customers > Customers page
  As a BO user
  I must be able to create, save and edit customers

  Background:
    Given groups feature is activated

  Scenario: Create a simple customer and edit it
    When I create a customer "CUST-1" with following properties:
      | firstName | Mathieu                    |
      | lastName  | Napoler                    |
      | email     | napoler.dev@prestashop.com |
      | password  | PrestaShopForever1_!       |
    Then if I query customer "CUST-1" I should get a Customer with properties:
      | firstName | Mathieu                    |
      | lastName  | Napoler                    |
      | email     | napoler.dev@prestashop.com |
    When I edit customer "CUST-1" and I change the following properties:
      | firstName | Jean |
    Then if I query customer "CUST-1" I should get a Customer with properties:
      | firstName | Jean |

  Scenario: Fail to create a duplicate customer
    When I create a customer "CUST-2" with following properties:
      | firstName | Mathieu                     |
      | lastName  | Napoler                     |
      | email     | naapoler.dev@prestashop.com |
      | password  | PrestaShopForever1_!        |
    And I attempt to create a customer "CUST-3" with following properties:
      | firstName | Mathieu                     |
      | lastName  | Napoler                     |
      | email     | naapoler.dev@prestashop.com |
      | password  | PrestaShopForever1_!        |
    Then I should be returned an error message 'Customer with email "naapoler.dev@prestashop.com" already exists'

  Scenario: Create a complete customer and edit it
    When I create a customer "CUST-4" with following properties:
      | firstName                 | Mathieu                   |
      | lastName                  | Polarn                    |
      | email                     | polarn.dev@prestashop.com |
      | password                  | PrestaShopForever1_!      |
      | defaultGroupId            | Guest                     |
      | groupIds                  | [Guest, Customer]         |
      | genderId                  | Mrs.                      |
      | isEnabled                 | false                     |
      | isPartnerOffersSubscribed | true                      |
      | birthday                  | 1987-02-22                |
    Then if I query customer "CUST-4" I should get a Customer with properties:
      | firstName               | Mathieu                   |
      | lastName                | Polarn                    |
      | email                   | polarn.dev@prestashop.com |
      | defaultGroupId          | Guest                     |
      | groupIds                | [Guest, Customer]         |
      | genderId                | Mrs.                      |
      | enabled                 | false                     |
      | partnerOffersSubscribed | true                      |
      | birthday                | 1987-02-22                |
    When I edit customer "CUST-4" and I change the following properties:
      | firstName                 | Jean       |
      | defaultGroupId            | Customer   |
      | isPartnerOffersSubscribed | false      |
      | birthday                  | 1987-02-24 |
    Then if I query customer "CUST-4" I should get a Customer with properties:
      | firstName            | Jean       |
      | defaultGroupId       | Customer   |
      | newsletterSubscribed | false      |
      | birthday             | 1987-02-24 |

  Scenario: Delete customer
    When I create a customer "CUST-5" with following properties:
      | firstName      | Mathieu                   |
      | lastName       | Abigal                    |
      | email          | abigal.dev@prestashop.com |
      | password       | PrestaShopForever1_!      |
      | defaultGroupId | Guest                     |
      | groupIds       | [Guest]                   |
    And I delete customer "CUST-5" with "allow registration after deletion" checked
    Then the customer "CUST-5" should not be found
