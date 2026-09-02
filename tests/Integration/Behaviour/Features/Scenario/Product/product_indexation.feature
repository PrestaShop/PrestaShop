# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags product-indexation
@restore-products-before-feature
@restore-languages-after-feature
@product-indexation
Feature: Index product properly from Back Office (BO)
  As a BO user
  I need to be able to update product from BO and it correctly impacts its indexes and its searchability on FO

  Background:
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    # Enable the context currency which is needed in search
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    And currency "currency1" is the current one
    # Disable fuzzy search, only search by tag value
    And shop configuration for "PS_SEARCH_FUZZY" is set to 0
    # We create a product search (based on its visibility) and enabled right away
    # The following scenario won't change this status (this is tested in update_options.feature already)
    # It focuses on updating only the content and the indexation must react accordingly
    And I add product "searchableProduct" with following information:
      | name[en-US] | Mechanical watch |
      | name[fr-FR] | Montre mécanique |
      | type        | standard         |
      | visibility  | both             |
    And I enable product "searchableProduct"
    Then product "searchableProduct" localized "name" should be:
      | locale | value            |
      | en-US  | Mechanical watch |
      | fr-FR  | Montre mécanique |
    Then product "searchableProduct" should have following options:
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    And product "searchableProduct" should be enabled
    And product "searchableProduct" should be indexed

  Scenario: Product is searchable by name after update
    When I search for products on front office with sentence "electronic" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "électronique" with locale "fr-FR" I should find nothing
    When I update product "searchableProduct" with following values:
      | name[en-US]         | Electronic watch    |
      | name[fr-FR]         | Montre électronique |
      | link_rewrite[en-US] | electronic-watch    |
      | link_rewrite[fr-FR] | montre-electronique |
    Then I search for products on front office with sentence "electronic" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Electronic watch |
    And I search for products on front office with sentence "électronique" with locale "fr-FR" I should find:
      | product_id        | name                |
      | searchableProduct | Montre électronique |
    And I search for products on front office with sentence "mechanical" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "mécanique" with locale "fr-FR" I should find nothing
    Then I delete product searchableProduct

  Scenario: Product is searchable by description after update
    When I search for products on front office with sentence "gears" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "rouages" with locale "fr-FR" I should find nothing
    When I update product "searchableProduct" with following values:
      | description[en-US] | With amazing gears           |
      | description[fr-FR] | Avec des rouages incroyables |
    Then I search for products on front office with sentence "gears" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    And I search for products on front office with sentence "rouages" with locale "fr-FR" I should find:
      | product_id        | name             |
      | searchableProduct | Montre mécanique |
    Then I delete product searchableProduct

  Scenario: Product is searchable by short description after update
    When I search for products on front office with sentence "battery" with locale "en-US" I should find nothing
    And I search for products on front office with sentence "pile" with locale "fr-FR" I should find nothing
    When I update product "searchableProduct" with following values:
      | description_short[en-US] | Not battery needed |
      | description_short[fr-FR] | Pile inutile       |
    Then I search for products on front office with sentence "battery" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    And I search for products on front office with sentence "pile" with locale "fr-FR" I should find:
      | product_id        | name             |
      | searchableProduct | Montre mécanique |
    Then I delete product searchableProduct

  Scenario: Product is searchable by reference after update
    When I search for products on front office with sentence "reference-12345" with locale "en-US" I should find nothing
    When I update product "searchableProduct" with following values:
      | reference | reference-12345   |
    When I search for products on front office with sentence "reference-12345" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    Then I delete product searchableProduct

  Scenario: Product is searchable by isbn after update
    When I search for products on front office with sentence "978-0-596-52068-7" with locale "en-US" I should find nothing
    When I update product "searchableProduct" with following values:
      | isbn      | 978-0-596-52068-7 |
    And I search for products on front office with sentence "978-0-596-52068-7" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    Then I delete product searchableProduct

  Scenario: Product is searchable by upc after update
    When I search for products on front office with sentence "98765" with locale "en-US" I should find nothing
    When I update product "searchableProduct" with following values:
      | upc       | 98765             |
    And I search for products on front office with sentence "98765" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    Then I delete product searchableProduct

  Scenario: Product is searchable by ean13 after update
    When I search for products on front office with sentence "13121110" with locale "en-US" I should find nothing
    When I update product "searchableProduct" with following values:
      | ean13     | 13121110          |
    And I search for products on front office with sentence "13121110" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    Then I delete product searchableProduct

  Scenario: Product is searchable by mpn after update
    When I search for products on front office with sentence "mpn-12345" with locale "en-US" I should find nothing
    When I update product "searchableProduct" with following values:
      | mpn       | mpn-12345         |
    And I search for products on front office with sentence "mpn-12345" with locale "en-US" I should find:
      | product_id        | name             |
      | searchableProduct | Mechanical watch |
    Then I delete product searchableProduct
