# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags bulk-update-status
@restore-products-before-feature
@clear-cache-before-feature
@bulk-product
@bulk-update-status
Feature: Bulk update product status from BO (Back Office)
  As an employee I must be able to bulk update product status (enable/disable)

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute group "Dimension" named "Dimension" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "40x60cm" named "40x60cm" in en language exists
    And attribute "60x90cm" named "60x90cm" in en language exists
    And attribute "80x120cm" named "80x120cm" in en language exists
    Given I add product "product1" with following information:
      | name[en-US] | Values list poster nr. 1 (paper) |
      | type        | standard                         |
    And product product1 type should be standard
    And product "product1" should be disabled
    And product "product1" should not be indexed
    And I add product "product2" with following information:
      | name[en-US] | Values list poster nr. 2 (virtual) |
      | type        | virtual                            |
    And product product2 type should be virtual
    And product "product2" should be disabled
    And I add product "product3" with following information:
      | name[en-US] | T-Shirt with listed values |
      | type        | combinations               |
    When I generate combinations for product product3 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black] |
    Then product "product3" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product3SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product3SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product3MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product3MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And product product3 type should be combinations
    And product "product3" should be disabled

  Scenario: I update product statuses
    When I bulk change status to be enabled for following products:
      | reference |
      | product1  |
      | product2  |
      | product3  |
    Then product "product1" should be enabled
    And product "product1" should be indexed
    Then product "product2" should be enabled
    And product "product2" should be indexed
    Then product "product3" should be enabled
    And product "product3" should be indexed

  Scenario: I disable enabled products
    Given I enable product "product1"
    And I enable product "product2"
    And I enable product "product3"
    Then product "product1" should be enabled
    And product "product2" should be enabled
    And product "product3" should be enabled
    And I bulk change status to be disabled for following products:
      | reference |
      | product1  |
      | product2  |
      | product3  |
    Then product "product1" should be disabled
    And product "product1" should not be indexed
    Then product "product2" should be disabled
    And product "product2" should not be indexed
    Then product "product3" should be disabled
    And product "product3" should not be indexed

  Scenario: I disable products which are already disabled
    Given product "product1" should be disabled
    And product "product2" should be disabled
    And product "product3" should be disabled
    And I bulk change status to be disabled for following products:
      | reference |
      | product1  |
      | product2  |
      | product3  |
    Then product "product1" should be disabled
    And product "product1" should not be indexed
    Then product "product2" should be disabled
    And product "product2" should not be indexed
    Then product "product3" should be disabled
    And product "product3" should not be indexed

  Scenario: I disable products one opf them has no name and can't be enabled, the others are
    Given I add product "noNameProduct" with following information:
      | type | standard |
    And product noNameProduct type should be standard
    And product "noNameProduct" should be disabled
    And product "noNameProduct" should not be indexed
    And product "noNameProduct" localized "name" should be:
      | locale | value |
      | en-US  |       |
    And product "product1" should be disabled
    And product "product2" should be disabled
    And product "product3" should be disabled
    # NoNameProduct is in the middle of the list it doesn't mean the following products are ignored
    And I bulk change status to be enabled for following products:
      | reference     |
      | product1      |
      | noNameProduct |
      | product2      |
      | product3      |
    Then I should get an error that online data are invalid for products:
      | reference     |
      | noNameProduct |
    And product "product1" should be enabled
    And product "product2" should be enabled
    And product "product3" should be enabled
    And product "noNameProduct" should be disabled
