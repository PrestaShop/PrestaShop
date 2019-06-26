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
    Given product "product2" with id product "2" exists
    When I toggle status of product "product1"
    When I toggle status of product "product2"
    Then product "product1" should have status "0"
    Then product "product2" should have status "0"
    When I bulk enable products "product2,product1"
    Then product "product1" should have status "1"
    Then product "product2" should have status "1"
