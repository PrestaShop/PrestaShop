# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product
@reset-database-before-feature
Feature: Product list management
  Prestashop allows BO users to see prestashop products
  As a BO user
  I must be able to see different columns on different conditions

  Scenario: Product list quantity column is not visible
    Given shop configuration for "PS_STOCK_MANAGEMENT" is set to 0
    Then product grid in BO should not contain column "quantity"
