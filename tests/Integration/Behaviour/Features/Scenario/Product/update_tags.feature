# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-tags
@reset-database-before-feature
@update-tags
Feature: Update product tags from Back Office (BO)
  As a BO user
  I need to be able to update product tags from BO

  Scenario: I update product tags in multiple languages
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And I add product "product3" with following information:
      | name       | en-US:Mechanical watch; fr-FR:montre mécanique |
      | is_virtual | false                                         |
    And product "product3" should have following values:
      | name       | en-US:Mechanical watch; fr-FR:montre mécanique |
    And product "product3" localized "tags" should be "en-US: ;fr-FR:"
    When I update product "product3" tags with following values:
      | tags       | en-US:mechanic,watch; fr-FR:montre,mécanique |
    And product "product3" localized "tags" should be "en-US:mechanic,watch; fr-FR:montre,mécanique"
    When I update product "product3" tags with following values:
      | tags       | en-US:mechanic,watch; fr-FR: |
    And product "product3" localized "tags" should be "en-US:mechanic,watch; fr-FR:"
    When I update product "product3" tags with following values:
      | tags       | fr-FR:montre |
    And product "product3" localized "tags" should be "en-US:mechanic,watch; fr-FR:montre"

  Scenario: Update product tags with invalid values
    Given product "product3" localized "tags" should be "en-US:mechanic,watch; fr-FR:montre"
    When I update product "product3" tags with following values:
      | tags       | en-US:#<{ |
    Then I should get error that product tag is invalid
