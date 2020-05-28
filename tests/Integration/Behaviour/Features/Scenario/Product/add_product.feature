# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add
@reset-database-before-feature
Feature: Add basic product from Back Office (BO)
  As a BO user
  I need to be able to add new product with basic information from the BO

  @add
  Scenario: I add a product with basic information
    When I add product "product1" with following basic information:
      | name | en-US:bottle of beer |
      | type | standard             |
    Then product "product1" should have following values:
      | active           | false          |
      | condition        | new            |
    And product "product1" type should be standard
    And product "product1" "name" should be "en-US:bottle of beer"
    And product "product1" should be assigned to default category

# @todo; add if we make product name required. #19441
#  @add
#  Scenario: I add a product without providing name in default language
#    Given language with iso code "en" is the default one
#    Given language "french" with locale "fr-FR" exists
#    When I add product "product2" with following basic information:
#      | name | fr-FR: glass of wine |
#      | type | standard             |
#    Then I should get error that product name is invalid

