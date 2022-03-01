# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-tags
@restore-products-before-feature
@restore-languages-after-feature
@update-tags
Feature: Update product tags from Back Office (BO)
  As a BO user
  I need to be able to update product tags from BO

  Background:
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And I add product "product3" with following information:
      | name[en-US] | Mechanical watch |
      | name[fr-FR] | montre mécanique |
      | type        | standard         |
    And product "product3" localized "name" should be:
      | locale | value            |
      | en-US  | Mechanical watch |
      | fr-FR  | montre mécanique |

  Scenario: I update product tags in multiple languages
    And product "product3" localized "tags" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    When I update product "product3" tags with following values:
      | tags[en-US] | mechanic,watch   |
      | tags[fr-FR] | montre,mécanique |
    And product "product3" localized "tags" should be:
      | locale | value            |
      | en-US  | mechanic,watch   |
      | fr-FR  | montre,mécanique |
    When I update product "product3" tags with following values:
      | tags[en-US] | mechanic,watch |
      | tags[fr-FR] |                |
    And product "product3" localized "tags" should be:
      | locale | value          |
      | en-US  | mechanic,watch |
      | fr-FR  |                |
    When I update product "product3" tags with following values:
      | tags[fr-FR] | montre |
    And product "product3" localized "tags" should be:
      | locale | value          |
      | en-US  | mechanic,watch |
      | fr-FR  | montre         |

  Scenario: Update product tags with invalid values
    When I update product "product3" tags with following values:
      | tags[en-US] | mechanic,watch   |
      | tags[fr-FR] | montre,mécanique |
    Then product "product3" localized "tags" should be:
      | locale | value            |
      | en-US  | mechanic,watch   |
      | fr-FR  | montre,mécanique |
    When I update product "product3" tags with following values:
      | tags[en-US] | #<{ |
    Then I should get error that product tag is invalid
    And product "product3" localized "tags" should be:
      | locale | value            |
      | en-US  | mechanic,watch   |
      | fr-FR  | montre,mécanique |

  Scenario: Remove all product tags
    When I update product "product3" tags with following values:
      | tags[en-US] | mechanic,watch   |
      | tags[fr-FR] | montre,mécanique |
    Then product "product3" localized "tags" should be:
      | locale | value            |
      | en-US  | mechanic,watch   |
      | fr-FR  | montre,mécanique |
    When I remove all product "product3" tags
    And product "product3" localized "tags" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |

  Scenario: Tags can be used for product search
    # Enable the context currency which is needed in search
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    And currency "currency1" is the current one
    # Disable fuzzy search, only search by tag value
    And shop configuration for "PS_SEARCH_FUZZY" is set to 0
    When I update product "product3" tags with following values:
      | tags[en-US] | time  |
      | tags[fr-FR] | temps |
    Then product "product3" localized "tags" should be:
      | locale | value |
      | en-US  | time  |
      | fr-FR  | temps |
    # When product is disabled it is not indexed
    When I search for products on front office with sentence "time" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "temps" with locale "fr-FR" I should find nothing
    Given I enable product "product3"
    When I search for products on front office with sentence "time" with locale "en-US" I should find:
      | product_id | name             |
      | product3   | Mechanical watch |
    And I search for products on front office with sentence "temps" with locale "fr-FR" I should find:
      | product_id | name             |
      | product3   | montre mécanique |
    # Now update the tags the search returns different results
    When I update product "product3" tags with following values:
      | tags[en-US] | running |
      | tags[fr-FR] | course  |
    Then product "product3" localized "tags" should be:
      | locale | value   |
      | en-US  | running |
      | fr-FR  | course  |
    When I search for products on front office with sentence "time" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "temps" with locale "fr-FR" I should find nothing
    When I search for products on front office with sentence "running" with locale "en-US" I should find:
      | product_id | name             |
      | product3   | Mechanical watch |
    And I search for products on front office with sentence "course" with locale "fr-FR" I should find:
      | product_id | name             |
      | product3   | montre mécanique |
    When I disable product "product3"
    When I search for products on front office with sentence "running" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "course" with locale "fr-FR" I should find nothing
