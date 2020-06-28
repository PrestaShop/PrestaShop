# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-stock
@reset-database-before-feature
@update-stock
Feature: Update product stock from Back Office (BO)
  As a BO user
  I need to be able to update product stock from BO

  Background:
    Given shop configuration for "PS_ADVANCED_STOCK_MANAGEMENT" is set to 1

  Scenario: I update product stock management
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following values:
      | use_advanced_stock_management | 0           |
    When I update product "product1" stock with following values:
      | use_advanced_stock_management | 1           |
    Then product "product1" should have following values:
      | use_advanced_stock_management | 1           |

  Scenario: I try to update stock while the configuration is disabled
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following values:
      | use_advanced_stock_management | 0           |
    And shop configuration for "PS_ADVANCED_STOCK_MANAGEMENT" is set to 0
    When I update product "product1" stock with following values:
      | use_advanced_stock_management | 1           |
    And I should get error that stock management is disabled
    Then product "product1" should have following values:
      | use_advanced_stock_management | 0           |
