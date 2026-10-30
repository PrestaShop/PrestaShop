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
      | Default page       | Products                    |
      | Language           | English (English)           |
      | Active             | true                        |
      | Permission profile | Logistician                 |
    And I Add new employee "future_employee" to shop "shop1" with the following details:
      | First name         | Nicolas                      |
      | Last name          | Tesla                        |
      | Email address      | nicolas.tesla@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Products                     |
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
      | Default page       | Products                       |
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

  Scenario: I cannot assign a default page to a profile that cannot access it
    Given I Add new employee cheater_employee to shop "shop1" with the following details:
      | First name         | Cheater                      |
      | Last name          | Faker                        |
      | Email address      | cheater.faker@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Dashboard                    |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | Logistician                  |
    Then I should get an error indicating that employee field homepage is invalid
    # Now use a valid homepage and then try to edit to an invalid one
    Given I Add new employee cheater_employee to shop "shop1" with the following details:
      | First name         | Cheater                      |
      | Last name          | Faker                        |
      | Email address      | cheater.faker@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Products                     |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | Logistician                  |
    Then employee cheater_employee should have the following details:
      | First name         | Cheater                      |
      | Last name          | Faker                        |
      | Email address      | cheater.faker@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Products                     |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | Logistician                  |
    Given I edit employee cheater_employee with the following details:
      | First name         | Cheater                      |
      | Last name          | Faker                        |
      | Email address      | cheater.faker@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Dashboard                    |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | Logistician                  |
    Then I should get an error indicating that employee field homepage is invalid
    And employee cheater_employee should have the following details:
      | First name         | Cheater                      |
      | Last name          | Faker                        |
      | Email address      | cheater.faker@prestashop.com |
      | Password           | secretpassword               |
      | Default page       | Products                     |
      | Language           | English (English)            |
      | Active             | true                         |
      | Permission profile | Logistician                  |

  Scenario: I can edit only some fields of an employee
    Given I Add new employee "partial_employee" to shop "shop1" with the following details:
      | First name         | Ada                         |
      | Last name          | Byron                       |
      | Email address      | ada.byron@prestashop.com    |
      | Password           | secretpassword              |
      | Default page       | Products                    |
      | Language           | English (English)           |
      | Active             | true                        |
      | Permission profile | Salesman                    |
    When I edit employee "partial_employee" with the following details:
      | Last name | Lovelace |
    Then employee partial_employee should have the following details:
      | First name         | Ada                         |
      | Last name          | Lovelace                    |
      | Email address      | ada.byron@prestashop.com    |
      | Password           | secretpassword              |
      | Default page       | Products                    |
      | Language           | English (English)           |
      | Active             | true                        |
      | Permission profile | Salesman                    |
      | Associated shops   | shop1                       |

  Scenario: I cannot edit an employee that does not exist
    Given I delete the employee partial_employee
    When I edit employee "partial_employee" with the following details:
      | Last name | Ghost |
    Then I should get an error that the employee does not exist

  Scenario: I can change the status of an employee and delete it without an employee in the context
    Given I Add new employee "api_employee" to shop "shop1" with the following details:
      | First name         | Grace                       |
      | Last name          | Hopper                      |
      | Email address      | grace.hopper@prestashop.com |
      | Password           | secretpassword              |
      | Default page       | Products                    |
      | Language           | English (English)           |
      | Active             | true                        |
      | Permission profile | Salesman                    |
    And there is no employee in the legacy context
    When I toggle employee status for api_employee
    Then employee api_employee should have the following details:
      | Active | false |
    When I bulk enable employees "api_employee"
    Then employee api_employee should have the following details:
      | Active | true |
    When I delete the employee api_employee
    Then employee api_employee does not exist
