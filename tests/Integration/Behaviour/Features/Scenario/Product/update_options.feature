# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-options
@reset-database-before-feature
@update-options
Feature: Update product options from Back Office (BO)
  As a BO user
  I need to be able to update product options from BO

  Scenario: I update product options
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    Then product "product1" should have following values:
      | visibility           | both               |
      | available_for_order  | true               |
      | online_only          | false              |
      | show_price           | true               |
      | condition            | new                |
      | isbn                 |                    |
      | upc                  |                    |
      | ean13                |                    |
      | mpn                  |                    |
      | reference            |                    |
    And product "product1" localized "tags" should be "en-US:"
