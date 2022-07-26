# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s legacy_product --tags legacy-product-combination
@restore-products-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@legacy-product-type
@legacy-update-product-combination
Feature: Product can exist in different combinations (BO)
  As a BO user
  I want to be able to edit combination properties

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists

  Scenario: I add a reference name to a product combination
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White]            |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference  | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |            | [Size:S,Color:White] | 0               | 0        | true       |
      | product1MWhite | Size - M, Color - White |            | [Size:M,Color:White] | 0               | 0        | false      |
    And combination "product1SWhite" should have following details:
      | combination detail | value |
      | ean13              |       |
      | isbn               |       |
      | mpn                |       |
      | reference          |       |
      | upc                |       |
      | impact on weight   | 0     |
    When I update combination "product1SWhite" details the legacy way with following values:
      | ean13            | 978020137962      |
      | isbn             | 978-3-16-148410-0 |
      | mpn              | mpn1              |
      | reference        | ref1              |
      | upc              | 72527273070       |
      | weight           | 17.25             |
    Then combination "product1SWhite" should have following details:
      | combination detail | value             |
      | ean13              | 978020137962      |
      | isbn               | 978-3-16-148410-0 |
      | mpn                | mpn1              |
      | reference          | ref1              |
      | upc                | 72527273070       |
      | impact on weight   | 17.25             |
