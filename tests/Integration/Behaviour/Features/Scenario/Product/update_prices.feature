# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update_prices
@reset-database-before-feature
Feature: Update product price fields from Back Office (BO).
  As a BO user I want to be able to update product fields associated with price.

  @update_prices
  Scenario: I update product prices
    Given I add product "product1" with following information:
      | name       | en-US:magic staff |
      | is_virtual | false             |
    Then product "product1" should have following values:
      | price            | 0           |

