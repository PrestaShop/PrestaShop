# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags related-products
@reset-database-before-feature
@clear-cache-before-feature
@related-products
Feature: Update product related products from Back Office (BO)
  As an employee
  I need to be able to update related products of a product from Back Office

  Scenario: I set related products
    Given I add product "product1" with following information:
      | name       | en-US:book of law |
      | is_virtual | false             |
    And I add product "product2" with following information:
      | name       | en-US:book of love |
      | is_virtual | false              |
    And I add product "product3" with following information:
      | name       | en-US:lovely books package |
      | is_virtual | false                      |
    And I update pack "product3" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    And I add product "product4" with following information:
      | name       | en-US:Reading glasses |
      | is_virtual | false                 |
    And product "product4" has following combinations:
      | reference   | quantity | attributes  |
      | whiteFramed | 10       | Color:White |
      | blackFramed | 10       | Color:Black |
    And product product1 should have no related products
    And product product2 type should be standard
    And product "product3" type should be pack
    And product product4 type should be combination
    When I set following related products to product product1:
      | product2 |
      | product3 |
      | product4 |
    Then product product1 should have following related products:
      | product2 |
      | product3 |
      | product4 |
    When I set following related products to product product1:
      | product2 |
      | product4 |
    Then product product1 should have following related products:
      | product2 |
      | product4 |

  Scenario: Remove all related products
    Given product product1 should have following related products:
      | product2 |
      | product4 |
    When I remove all related products from product product1
    Then product product1 should have no related products

  Scenario: Search for products to relate by name
    Given I add product "product10" with following information:
      | name       | en-US:my product10 |
      | is_virtual | false              |
    And I update product "product10" options with following information:
      | reference | 1235510 |
    And I add product "product11" with following information:
      | name       | en-US:your product11 |
      | is_virtual | false                |
    And I update product "product11" options with following information:
      | reference | 1235511 |
    And I add product "product12" with following information:
      | name       | en-US:my product12 |
      | is_virtual | false              |
    And I update product "product12" options with following information:
      | reference | 1235512 |
    And I add product "product13" with following information:
      | name       | en-US:your product13 |
      | is_virtual | false                |
    And I update product "product13" options with following information:
      | reference | 1235513 |
    And I add product "product14" with following information:
      | name       | en-US:my product14 |
      | is_virtual | true               |
    And I update product "product14" options with following information:
      | reference | 1235514 |
    And I add product "product15" with following information:
      | name       | en-US:your product15 |
      | is_virtual | true                 |
    And I update product "product15" options with following information:
      | reference | 1235515 |
    When I search products to relate in "en" language by phrase "your p" and limit 10
    Then search results for product to relate should be the following:
      | product   | name           | reference |
      | product11 | your product11 | 1235511   |
      | product13 | your product13 | 1235513   |
      | product15 | your product15 | 1235515   |
    When I search products to relate in "en" language by phrase "prod" and limit 10
    Then search results for product to relate should be the following:
      | product   | name           | reference |
      | product10 | my product10   | 1235510   |
      | product11 | your product11 | 1235511   |
      | product12 | my product12   | 1235512   |
      | product13 | your product13 | 1235513   |
      | product14 | my product14   | 1235514   |
      | product15 | your product15 | 1235515   |
    When I search products to relate in "en" language by phrase "prod" and limit 4
    Then search results for product to relate should be the following:
      | product   | name           | reference |
      | product10 | my product10   | 1235510   |
      | product11 | your product11 | 1235511   |
      | product12 | my product12   | 1235512   |
      | product13 | your product13 | 1235513   |

  Scenario: Search for products to relate by reference
    Given product "product10" localized "name" should be "en-US:my product10"
    And product product10 should have following options information:
      | reference | 1235510 |
    And product "product11" localized "name" should be "en-US:your product11"
    And product product11 should have following options information:
      | reference | 1235511 |
    And product "product12" localized "name" should be "en-US:my product12"
    And product product12 should have following options information:
      | reference | 1235512 |
    And product "product13" localized "name" should be "en-US:your product13"
    And product product13 should have following options information:
      | reference | 1235513 |
    And product "product14" localized "name" should be "en-US:my product14"
    And product product14 should have following options information:
      | reference | 1235514 |
    And product "product15" localized "name" should be "en-US:your product15"
    And product product15 should have following options information:
      | reference | 1235515 |
    When I search products to relate in "en" language by phrase "123" and limit 10
    Then search results for product to relate should be the following:
      | product   | name           | reference |
      | product10 | my product10   | 1235510   |
      | product11 | your product11 | 1235511   |
      | product12 | my product12   | 1235512   |
      | product13 | your product13 | 1235513   |
      | product14 | my product14   | 1235514   |
      | product15 | your product15 | 1235515   |
