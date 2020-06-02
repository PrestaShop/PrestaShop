# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-description
@reset-database-before-feature
Feature: Update product descriptions from Back Office
  As a BO user I need to be able to update product description and short description

  @update-description
  Scenario: Update product descriptions
    Given I add product "product1" with following basic information:
      | name | en-US:potato |
      | type | standard     |
    And product "product1" localized "description" should be "en-US:"
    And product "product1" localized "short description" should be "en-US:"
    When I update product "product1" descriptions with following information:
      | description | en-US:sweet potato |
      | short description | en-US:Just a sweet potato |
    Then product "product1" localized "description" should be "en-US:sweet potato"
    Then product "product1" localized "short description" should be "en-US:Just a sweet potato"
