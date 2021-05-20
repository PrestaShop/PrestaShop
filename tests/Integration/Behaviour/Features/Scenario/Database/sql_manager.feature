# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s database
@reset-database-before-feature
Feature: SQL Manager
  PrestaShop allows BO users to manage SQL queries in the Configure > Advanced > Database page
  As a BO user
  I must be able to create, save, edit and run SQL queries

  Background:
    Given there is a customer named "John Doe" whose email is "john.doe@prestashop.com"

  Scenario: Create a SELECT request
    Given I add sql request "sql-request-select" with the following properties
      | name            | sql request |
      | sql             | SELECT 1 FROM ps_customer; |
    Then the sql request is valid
    Given I edit sql request "sql-request-select" with the following properties
      | name            | New SQL request |
      | sql             | SELECT 1 FROM ps_configuration; |
    Then the sql request is valid

  Scenario: Create an invalid SELECT request
    Given I add sql request "sql-request-invalid-select" with the following properties
      | name            | sql request |
      | sql             | SELECT FROM ps_customer; |
    Then I should get an error that the SQL request is malformed

  Scenario: Create an SELECT request targetting an unknown table
    Given I add sql request "sql-request-invalid-select" with the following properties
      | name            | sql request |
      | sql             | SELECT * FROM ps_customers; |
    Then I should get an error that the table "ps_customers" does not exists

  Scenario: Create a UPDATE request
    Given I add sql request "sql-request-invalid-update" with the following properties
      | name            | sql request |
      | sql             | UPDATE ps_customer SET name='test'; |
    Then I should get an error that only the SELECT request is allowed

  Scenario: Create a DELETE request
    Given I add sql request "sql-request-invalid-delete" with the following properties
      | name            | sql request |
      | sql             | DELETE FROM ps_customer WHERE id=1; |
    Then I should get an error that only the SELECT request is allowed

  Scenario: Create an INSERT request
    Given I add sql request "sql-request-invalid-insert" with the following properties
      | name            | sql request |
      | sql             | INSERT INTO ps_customer VALUES(NULL, 'test'); |
    Then I should get an error that only the SELECT request is allowed

  Scenario: Create an ALTER request
    Given I add sql request "sql-request-invalid-alter" with the following properties
      | name            | sql request |
      | sql             | ALTER TABLE ps_customer ADD troufignon BOOLEAN; |
    Then I should get an error that only the SELECT request is allowed

  Scenario: Create an DROP request
    Given I add sql request "sql-request-invalid-drop" with the following properties
      | name            | sql request |
      | sql             | DROP TABLE ps_customer; |
    Then I should get an error that only the SELECT request is allowed

  Scenario: Get required database fields
    When I request the database fields from table carrier
    Then I should get a set of database fields that contain values:
      | id_carrier      |
      | name            |
      | shipping_method |

  Scenario: Save a SQL request
    Given there is 0 stored SQL requests
    When I add the SQL request "SELECT * FROM ps_carrier" named "select_carriers_count_1"
    Then there should be 1 stored SQL request
