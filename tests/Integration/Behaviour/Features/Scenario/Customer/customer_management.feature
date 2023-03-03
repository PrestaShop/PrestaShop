# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags customer-management
@restore-all-tables-before-feature
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

  Scenario: Fail to create a duplicate registered customer, if registered customer with that email exists
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
    Then I should be returned an error message 'Registered customer with email "naapoler.dev@prestashop.com" already exists'

  Scenario: Ability to create a guest customer, even if registered customer with that email exists
    When I create a customer "CUST-3" with following properties:
      | firstName | Mathieu                     |
      | lastName  | Napoler                     |
      | email     | naapoler.dev@prestashop.com |
      | isGuest   | true                        |
    And I query customer "CUST-3" I should get a Customer with properties:
      | firstName      | Mathieu                     |
      | lastName       | Napoler                     |
      | email          | naapoler.dev@prestashop.com |
      | guest          | true                        |
      | defaultGroupId | Guest                       |
      | groupIds       | [Guest]                     |

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

  Scenario: Edit guest customer email, even if a registered customer with the same email exists
    When I create a customer "CUST-8" with following properties:
      | firstName      | Mathieu                     |
      | lastName       | Guest                       |
      | email          | guest@prestashop.com        |
      | isGuest        | true                        |
    And I create a customer "CUST-9" with following properties:
      | firstName | Mathieu                     |
      | lastName  | Customer                    |
      | email     | customernine@prestashop.com |
      | password  | PrestaShopForever1_!        |
    And I edit customer "CUST-8" and I change the following properties:
      | email | customernine@prestashop.com |
    Then I query customer "CUST-8" I should get a Customer with properties:
      | email | customernine@prestashop.com |

  Scenario: Attempt to edit registered customer email, while another customer with this email exists
    When I create a customer "CUST-10" with following properties:
      | firstName | Mathieu                       |
      | lastName  | Customer                      |
      | email     | customereten@prestashop.com   |
      | password  | PrestaShopForever1_!          |
    And I create a customer "CUST-11" with following properties:
      | firstName | Mathieu                       |
      | lastName  | Customer                      |
      | email     | customereleven@prestashop.com |
      | password  | PrestaShopForever1_!          |
    And I attempt to edit customer "CUST-10" and I change the following properties:
      | email | customereleven@prestashop.com |
    Then I should be returned an error message 'Registered customer with email "customereleven@prestashop.com" already exists'

  Scenario: Fail to create a customer with mismatching groups
    When I attempt to create a customer "CUST-12" with following properties:
      | firstName      | Mathieu                       |
      | lastName       | Napoler                       |
      | email          | customertwelve@prestashop.com |
      | password       | PrestaShopForever1_!          |
      | defaultGroupId | Visitor                       |
      | groupIds       | [Customer]                    |
    Then I should be returned an error message 'Customer default group with id "1" must be in access groups'

  Scenario: Fail to set mismatching groups on a customer
    When I create a customer "CUST-13" with following properties:
      | firstName      | Mathieu                            |
      | lastName       | Customer                           |
      | email          | customerethirteen@prestashop.com   |
      | password       | PrestaShopForever1_!               |
      | defaultGroupId | Customer                           |
      | groupIds       | [Customer]                         |
    And I attempt to edit customer "CUST-13" and I change the following properties:
      | defaultGroupId | Visitor |
    Then I should be returned an error message 'Customer default group with id "1" must be in access groups'
