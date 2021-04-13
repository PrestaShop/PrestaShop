# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags related-products
@reset-database-before-feature
@clear-cache-before-feature
@related-products
Feature: Update product related products from Back Office (BO)
  As an employee
  I need to be able to update related products of a product from Back Office

  Scenario: I set related products
    Given I add product "product1" with following information:
      | name[en-US] | book of law |
      | type        | standard    |
    And I add product "product2" with following information:
      | name[en-US] | book of love |
      | type        | standard    |
    And I add product "product3" with following information:
      | name[en-US] | lovely books package |
      | type        | pack                 |
    And I update pack "product3" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    And I add product "product4" with following information:
      | name[en-US] | Reading glasses |
      | type        | combinations    |
    And product "product4" has following combinations:
      | reference   | quantity | attributes  |
      | whiteFramed | 10       | Color:White |
      | blackFramed | 10       | Color:Black |
    And product product1 should have no related products
    And product product2 type should be standard
    And product "product3" type should be pack
    And product product4 type should be combinations
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
