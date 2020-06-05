# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update_prices
@reset-database-before-feature
Feature: Update product price fields from Back Office (BO).
  As a BO user I want to be able to update product fields associated with price.

  @update_prices
  Scenario: I update product prices
    Given I add product "product1" with following information:
      | name       | en-US:magic staff |
      | is_virtual | false             |
    And product "product1" should have following values:
      | price              | 0           |
      | ecotax             | 0           |
      # @todo; although it doesnt make sense, the legacy obj model has "1" tax rule group id by default.
      # @todo: remake to a more user friendly name instead of using "id"?
      | tax_rules_group_id | 1           |
      | on_sale            | false       |
      | wholesale_price    | 0           |
      | unit_price         | 0           |
      | unity              |             |
      | unit_price_ratio   | 0           |
