Feature: SQL Manager
  PrestaShop allows BO users to manage SQL queries in the Configure > Advanced > Database page
  As a BO user
  I must be able to create, save, edit and run SQL queries

  Scenario: Get required database fields
    When I request the database fields from table carrier
    Then I should get a set of database fields that contain values:
      | id_carrier      |
      | name            |
      | shipping_method |

  Scenario: Save a SQL request
    Given there is 0 stored SQL requests
    When I add the SQL request "SELECT * FROM carrier" named "select_carriers_count_1"
    Then there should be 1 stored SQL request
