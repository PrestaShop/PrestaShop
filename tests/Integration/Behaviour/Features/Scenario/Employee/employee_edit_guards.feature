# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s employee --tags employee-edit-guards
@restore-all-tables-before-feature
@employee-edit-guards
Feature: Employee edit guards
  PrestaShop refuses an employee edit that references unknown entities, and only a super admin changes a super
  admin, so a caller without employee in the context (Admin API, CLI) cannot

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists
    # Same languages as employee_management: the language choice provider is cached between features
    And language "language2" with locale "fr-FR" exists

  Scenario: I cannot edit an employee with unknown references
    Given I Add new employee "guarded_employee" to shop "shop1" with the following details:
      | First name         | Alan                    |
      | Last name          | Turing                  |
      | Email address      | alan.turing@example.com |
      | Password           | secretpassword          |
      | Default page       | Products                |
      | Language           | English (English)       |
      | Active             | true                    |
      | Permission profile | Salesman                |
    When I edit employee "guarded_employee" setting the unknown "profile" id 99999
    Then I should get an error that the referenced profile does not exist
    When I edit employee "guarded_employee" setting the unknown "language" id 99999
    Then I should get an error that the referenced language does not exist
    When I edit employee "guarded_employee" setting the unknown "shop" id 99999
    Then I should get an error that the referenced shop does not exist
    When I edit employee "guarded_employee" setting the unknown "default page" id 99999
    Then I should get an error that the referenced tab does not exist
    And employee guarded_employee should have the following details:
      | Default page       | Products          |
      | Language           | English (English) |
      | Permission profile | Salesman          |
      | Associated shops   | shop1             |

  Scenario: Only a super admin changes a super admin
    Given I Add new employee "other_admin" to shop "shop1" with the following details:
      | First name         | Grace                   |
      | Last name          | Hopper                  |
      | Email address      | grace.admin@example.com |
      | Password           | secretpassword          |
      | Default page       | Dashboard               |
      | Language           | English (English)       |
      | Active             | true                    |
      | Permission profile | SuperAdmin              |
    And there is no employee in the legacy context
    When I edit employee "other_admin" with the following details:
      | Email address | attacker@example.com |
    Then I should get an error that only a super admin can change a super admin
    When I edit employee "other_admin" with the following details:
      | Active | false |
    Then I should get an error that only a super admin can change a super admin
    When I edit employee "other_admin" setting the profile "Salesman" and the default page "AdminProducts"
    Then I should get an error that only a super admin can change a super admin
    When I toggle employee status for other_admin
    Then I should get an error that only a super admin can change a super admin
    When I delete the employee other_admin
    Then I should get an error that only a super admin can change a super admin
    And employee other_admin should have the following details:
      | Email address      | grace.admin@example.com |
      | Active             | true                    |
      | Permission profile | SuperAdmin              |
