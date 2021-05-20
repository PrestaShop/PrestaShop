# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-details
@reset-database-before-feature
@clear-cache-before-feature
@update-details
Feature: Update product details from Back Office (BO)
  As a BO user
  I need to be able to update product details from BO

  Scenario: I update product details
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following details:
      | product detail | value |
      | isbn           |       |
      | upc            |       |
      | ean13          |       |
      | mpn            |       |
      | reference      |       |
    When I update product "product1" details with following values:
      | isbn      | 978-3-16-148410-0 |
      | upc       | 72527273070       |
      | ean13     | 978020137962      |
      | mpn       | mpn1              |
      | reference | ref1              |
    Then product "product1" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |

  Scenario: I only update product availability for order, leaving other properties unchanged
    Given product "product1" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    When I update product "product1" details with following values:
      | isbn |  |
    Then product "product1" should have following details:
      | product detail | value        |
      | isbn           |              |
      | upc            | 72527273070  |
      | ean13          | 978020137962 |
      | mpn            | mpn1         |
      | reference      | ref1         |

  Scenario: I update product details providing invalid values
    Given I add product "product2" with following information:
      | name[en-US] | 'The truth is out there' wallpaper |
      | type        | virtual                            |
    And product "product2" should have following details:
      | product detail | value |
      | isbn           |       |
      | upc            |       |
      | ean13          |       |
      | mpn            |       |
      | reference      |       |
    When I update product "product2" details with following values:
      | mpn | this is more than forty characters long string |
    Then I should get error that product mpn is invalid
    And product "product2" should have following details:
      | product detail | value |
      | isbn           |       |
      | upc            |       |
      | ean13          |       |
      | mpn            |       |
      | reference      |       |
