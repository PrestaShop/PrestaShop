# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product
@reset-database-before-feature
Feature: Product management
  Prestashop allows BO users to manage products
  As a BO user
  I must be able to creat, edit, delete products in my shop

  Scenario: Toggle product active status
    Given product "product1" with id product "1" exists
    When I toggle status of product "product1"
    Then product "product1" should have status "0"
    When I toggle status of product "product1"
    Then product "product1" should have status "1"

  Scenario: Bulk enable products
    Given product "product1" with id product "1" exists
    And product "product2" with id product "2" exists
    When I toggle status of product "product1"
    And I toggle status of product "product2"
    When I bulk enable products "product2,product1"
    Then product "product1" should have status "1"
    And product "product2" should have status "1"

  Scenario: Bulk disable products
    Given product "product1" with id product "1" exists
    And product "product2" with id product "2" exists
    When I bulk disable products "product2,product1"
    Then product "product1" should have status "0"
    And product "product2" should have status "0"

  Scenario: Delete product
    Given product "product3" with id product "3" exists
    When I delete product "product3"
    Then product with id "3" should not exist

  Scenario: Bulk delete products
    Given product "product4" with id product "4" exists
    And product "product5" with id product "5" exists
    When I bulk delete products "product4,product5"
    Then product with id "4" should not exist
    And product with id "5" should not exist

  Scenario: Duplicate product
    Given product "product1" with id product "1" exists
    When duplicate product "product1"
    Then product with reference "demo_1" count is equal to "2"

  Scenario: Bulk duplicate products
    Given product "product6" with id product "6" exists
    And product "product7" with id product "7" exists
    When bulk duplicate product "product6,product7"
    Then product with reference "demo_11" count is equal to "2"
    And product with reference "demo_12" count is equal to "2"
