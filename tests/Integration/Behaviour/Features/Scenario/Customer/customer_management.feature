# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags customer-management
@reset-database-before-feature
@customer-management
Feature: Customer Management
  PrestaShop allows BO users to manage customers in the Customers > Customers page
  As a BO user
  I must be able to create, save and edit customers

  Background:
    Given groups feature is activated
    And risk none in default language named None exists
    And risk low in default language named Low exists

  Scenario: Create a simple customer and edit it
    When I create a customer "CUST-1" with following properties:
      | firstName | Mathieu                    |
      | lastName  | Napoler                    |
      | email     | napoler.dev@prestashop.com |
      | password  | PrestaShopForever1_!       |
    When I query customer "CUST-1" I should get a Customer with properties:
      | firstName | Mathieu                    |
      | lastName  | Napoler                    |
      | email     | napoler.dev@prestashop.com |
      | guest     | false                      |
    When I edit customer "CUST-1" and I change the following properties:
      | firstName | Jean |
    When I query customer "CUST-1" I should get a Customer with properties:
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
    When I query customer "CUST-4" I should get a Customer with properties:
      | firstName                | Mathieu                   |
      | lastName                 | Polarn                    |
      | email                    | polarn.dev@prestashop.com |
      | defaultGroupId           | Guest                     |
      | groupIds                 | [Guest, Customer]         |
      | genderId                 | Mrs.                      |
      | enabled                  | false                     |
      | partnerOffersSubscribed  | true                      |
      | newsletterSubscribed     | false                     |
      | birthday                 | 1987-02-22                |
      | guest                    | false                     |
      | companyName              |                           |
      | siretCode                |                           |
      | apeCode                  |                           |
      | website                  |                           |
      | allowedOutstandingAmount | 0.00                      |
      | maxPaymentDays           | 0                         |
      | riskId                   |                           |
    When I edit customer "CUST-4" and I change the following properties:
      | firstName                 | Jean                      |
      | lastName                  | Reno                      |
      | email                     | jean.reno@prestashop.com  |
      | defaultGroupId            | Customer                  |
      | groupIds                  | [Customer]                |
      | genderId                  | Mr.                       |
      | isEnabled                 | true                      |
      | isPartnerOffersSubscribed | false                     |
      | newsletterSubscribed      | true                      |
      | birthday                  | 1987-02-24                |
      | companyName               | PrestaShop                |
      | siretCode                 | 426169                    |
      | apeCode                   | 2845B                     |
      | website                   | http://www.prestashop.com |
      | allowedOutstandingAmount  | 3.14                      |
      | maxPaymentDays            | 7                         |
      | riskId                    | low                       |
    When I query customer "CUST-4" I should get a Customer with properties:
      | firstName                | Jean                      |
      | lastName                 | Reno                      |
      | email                    | jean.reno@prestashop.com  |
      | defaultGroupId           | Customer                  |
      | groupIds                 | [Customer]                |
      | genderId                 | Mr.                       |
      | enabled                  | true                      |
      | partnerOffersSubscribed  | false                     |
      | newsletterSubscribed     | true                      |
      | birthday                 | 1987-02-24                |
      | guest                    | false                     |
      | companyName              | PrestaShop                |
      | siretCode                | 426169                    |
      | apeCode                  | 2845B                     |
      | website                  | http://www.prestashop.com |
      | allowedOutstandingAmount  | 3.14                     |
      | maxPaymentDays            | 7                        |
      | riskId                    | low                      |

  Scenario: Delete customer but allow to register (pure delete from DB)
    When I create a customer "CUST-5" with following properties:
      | firstName      | Mathieu                   |
      | lastName       | Abigal                    |
      | email          | abigal.dev@prestashop.com |
      | password       | PrestaShopForever1_!      |
      | defaultGroupId | Guest                     |
      | groupIds       | [Guest]                   |
    And I delete customer "CUST-5" and allow it to register again
    Then the customer "CUST-5" should not be found
    # I can create another customer with the same mail
    When I create a customer "CUST-6" with following properties:
      | firstName      | Mathieu                   |
      | lastName       | Abigal                    |
      | email          | abigal.dev@prestashop.com |
      | password       | PrestaShopForever1_!      |
      | defaultGroupId | Guest                     |
      | groupIds       | [Guest]                   |
    When I query customer "CUST-6" I should get a Customer with properties:
      | firstName      | Mathieu                   |
      | lastName       | Abigal                    |
      | email          | abigal.dev@prestashop.com |
      | defaultGroupId | Guest                     |
      | groupIds       | [Guest]                   |

  Scenario: Delete customer but prevent to register (soft delete from DB, the customer cannot be recreated)
    When I create a customer "CUST-7" with following properties:
      | firstName      | Walter                       |
      | lastName       | Bishop                       |
      | email          | walter.bishop@prestashop.com |
      | password       | PrestaShopForever1_!         |
      | defaultGroupId | Guest                        |
      | groupIds       | [Guest]                      |
    And I delete customer "CUST-7" and prevent it from registering again
    # Customer is still present in DB but soft deleted
    When I query customer "CUST-7" I should get a Customer with properties:
      | firstName      | Walter                       |
      | lastName       | Bishop                       |
      | email          | walter.bishop@prestashop.com |
      | defaultGroupId | Guest                        |
      | groupIds       | [Guest]                      |
    And customer "CUST-7" should be soft deleted
