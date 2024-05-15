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
