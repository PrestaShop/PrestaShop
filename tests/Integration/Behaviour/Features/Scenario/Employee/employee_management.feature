# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s employee --tags employee-management
@restore-all-tables-before-feature
@employee-management
Feature: Employee management
  PrestaShop allows BO users to manage employees
  As a BO user
  I must be able to create, edit and delete employees

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists

  Scenario: I can create an employee
    Given I Add new employee "new_employee" to shop "shop1" with the following details:
      | First name         | Tadas                        |
      | Last name          | Davidsonas                   |
      | Email address      | tadas.davidsonas@invertus.eu |
      | Password           | secretpassword               |
      | Default page       | Dashboard                    |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | SuperAdmin                   |
    Then employee new_employee should have the following details:
      | First name         | Tadas                        |
      | Last name          | Davidsonas                   |
      | Email address      | tadas.davidsonas@invertus.eu |
      | Password           | secretpassword               |
      | Default page       | Dashboard                    |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | SuperAdmin                   |
      | Associated shops   | shop1                        |

  Scenario: I can edit an employee
    Given I edit employee "new_employee" with the following details:
      | First name         | John                     |
      | Last name          | Snow                     |
      | Email address      | john.snow@prestashop.com |
      | Password           | newpassword              |
      | Default page       | Products                 |
      | Language           | fr-FR                    |
      | Active             | false                    |
      | Permission profile | Logistician              |
      | Associated shops   | shop1                    |
    Then employee new_employee should have the following details:
      | First name         | John                     |
      | Last name          | Snow                     |
      | Email address      | john.snow@prestashop.com |
      | Password           | newpassword              |
      | Default page       | Products                 |
      | Language           | fr-FR                    |
      | Active             | false                    |
      | Permission profile | Logistician              |
      | Associated shops   | shop1                    |

  Scenario: I can delete an employee
    Then employee new_employee should have the following details:
      | Email address | john.snow@prestashop.com |
    When I delete the employee new_employee
    Then employee new_employee does not exist

  Scenario: I can update employee status individually or by bulk
    Given I Add new employee "first_employee" to shop "shop1" with the following details:
      | First name         | Isaac                       |
      | Last name          | Newton                      |
      | Email address      | isaac.newton@prestashop.com |
      | Password           | secretpassword              |
      | Default page       | Dashboard                   |
      | Language           | English (English)           |
      | Active             | true                        |
      | Permission profile | Logistician                 |
    And I Add new employee "future_employee" to shop "shop1" with the following details:
      | First name         | Nicolas                      |
      | Last name          | Tesla                        |
      | Email address      | nicolas.tesla@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Dashboard                    |
      | Language           | English (English)            |
      | Active             | false                        |
      | Permission profile | Salesman                     |
    Then employee first_employee should have the following details:
      | Email address | isaac.newton@prestashop.com |
      | Active        | true                        |
    And employee future_employee should have the following details:
      | Email address | nicolas.tesla@prestashop.com |
      | Active        | false                        |
    When I toggle employee status for future_employee
    And I toggle employee status for first_employee
    Then employee first_employee should have the following details:
      | Email address | isaac.newton@prestashop.com |
      | Active        | false                       |
    And employee future_employee should have the following details:
      | Email address | nicolas.tesla@prestashop.com |
      | Active        | true                         |
    When I bulk enable employees "first_employee,future_employee"
    Then employee first_employee should have the following details:
      | Email address | isaac.newton@prestashop.com |
      | Active        | true                        |
    And employee future_employee should have the following details:
      | Email address | nicolas.tesla@prestashop.com |
      | Active        | true                         |
    When I bulk disable employees "first_employee,future_employee"
    Then employee first_employee should have the following details:
      | Email address | isaac.newton@prestashop.com |
      | Active        | false                       |
    And employee future_employee should have the following details:
      | Email address | nicolas.tesla@prestashop.com |
      | Active        | false                        |

  Scenario: I can bulk delete employees
    Given employee first_employee should have the following details:
      | Email address | isaac.newton@prestashop.com |
    And employee future_employee should have the following details:
      | Email address | nicolas.tesla@prestashop.com |
    When I bulk delete the employees "first_employee,future_employee"
    Then employee first_employee does not exist
    And employee future_employee does not exist

  Scenario: I can send a password reset email to an employee and reset their password
    Given I Add new employee "sleepy_head" to shop "shop1" with the following details:
      | First name         | Alois                          |
      | Last name          | Alzheimer                      |
      | Email address      | alois.alzheimer@prestashop.com |
      | Password           | secretpassword                 |
      | Default page       | Dashboard                      |
      | Language           | English (English)              |
      | Active             | true                           |
      | Permission profile | Logistician                    |
    And employee sleepy_head should have the following details:
      | Email address | alois.alzheimer@prestashop.com |
    Given I set up shop context to single shop shop1
    When I send password reset email to "alois.alzheimer@prestashop.com" and reference the token as reset_token
    Then I can use token reset_token to set new password as newsecretpassword
    And employee sleepy_head should have the following details:
      | Email address | alois.alzheimer@prestashop.com |
      | Password      | newsecretpassword              |
