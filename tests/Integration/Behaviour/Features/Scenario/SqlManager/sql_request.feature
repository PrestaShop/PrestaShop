# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s sql_manager
Feature: SQL Manager SQL request form
  PrestaShop allows BO users to manage SQL requests
  As a BO user
  I must be able to edit and execute SQL requests

  Background:
    Given there is a customer named "John Doe" whose email is "john.doe@prestashop.com"

  Scenario: Create a SELECT request
    Given I specify following properties for sql request form
      | name            | sql request |
      | sql             | SELECT 1 FROM ps_customer; |
    When I submit the sql request form
    Then the sql request form is valid

  Scenario: Create an invalid SELECT request
    Given I specify following properties for sql request form
      | name            | sql request |
      | sql             | SELECT FROM ps_customer; |
    When I submit the sql request form
    Then I should get an error that the SQL request is malformed

  Scenario: Create an SELECT request targetting an unknown table
    Given I specify following properties for sql request form
      | name            | sql request |
      | sql             | SELECT * FROM ps_customers; |
    When I submit the sql request form
    Then I should get an error that the table "ps_customers" does not exists

  Scenario: Create a UPDATE request
    Given I specify following properties for sql request form
      | name            | sql request |
      | sql             | UPDATE ps_customer SET name='test'; |
    When I submit the sql request form
    Then I should get an error that only the SELECT request is allowed

  Scenario: Create a DELETE request
    Given I specify following properties for sql request form
      | name            | sql request |
      | sql             | DELETE FROM ps_customer WHERE id=1; |
    When I submit the sql request form
    Then I should get an error that only the SELECT request is allowed

  Scenario: Create an INSERT request
    Given I specify following properties for sql request form
      | name            | sql request |
      | sql             | INSERT INTO ps_customer VALUES(NULL, 'test'); |
    When I submit the sql request form
    Then I should get an error that only the SELECT request is allowed
