# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-description
@reset-database-before-feature
Feature: Update product descriptions from Back Office
  As a BO user I need to be able to update product description and short description

  @update-description
  Scenario: Update product descriptions
    Given I add product "product1" with following basic information:
      | name       | en-US:potato |
      | is_virtual | false        |
    And product "product1" localized "description" should be "en-US:"
    And product "product1" localized "description_short" should be "en-US:"
    When I update product "product1" descriptions with following information:
      | description       | en-US:sweet potato        |
      | description_short | en-US:Just a sweet potato |
    Then product "product1" localized "description" should be "en-US:sweet potato"
    Then product "product1" localized "description_short" should be "en-US:Just a sweet potato"

  @update-description
  Scenario: Update product descriptions with invalid characters
    Given product "product1" localized "description" is "en-US:sweet potato"
    And product "product1" localized "description_short" is "en-US:Just a sweet potato"
    When I update product "product1" descriptions with following information:
      | description       | en-US:<script>            |
    Then I should get error that product description is invalid
    And product "product1" localized "description" should be "en-US:sweet potato"
    When I update product "product1" descriptions with following information:
      | description_short       | en-US:<div onmousedown=hack()>   |
    Then I should get error that product short description is invalid
    And product "product1" localized "description_short" should be "en-US:Just a sweet potato"

  @update-description
  Scenario: Update product description to empty value
    Given product "product1" localized "description" is "en-US:sweet potato"
    And product "product1" localized "description_short" is "en-US:Just a sweet potato"
    When I update product "product1" descriptions with following information:
      | description       | en-US:            |
      | description_short | en-US:            |
    And product "product1" localized "description" should be "en-US:"
    And product "product1" localized "description_short" should be "en-US:"
