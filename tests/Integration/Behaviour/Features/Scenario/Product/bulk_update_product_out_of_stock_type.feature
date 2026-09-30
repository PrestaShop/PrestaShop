# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags bulk-update-out-of-stock-type
@restore-products-before-feature
@clear-cache-before-feature
@bulk-product
@bulk-update-out-of-stock-type
Feature: Bulk update product out of stock type from BO (Back Office)
  As an employee I must be able to set the out of stock behavior of several products at once

  Background:
    Given language with iso code "en" is the default one
    And I add product "product1" with following information:
      | name[en-US] | Bulk out of stock poster nr. 1 |
      | type        | standard                       |
    And I add product "product2" with following information:
      | name[en-US] | Bulk out of stock poster nr. 2 |
      | type        | standard                       |
    And I add product "product3" with following information:
      | name[en-US] | Bulk out of stock poster nr. 3 |
      | type        | standard                       |

  Scenario: I deny orders when out of stock for a selection
    When I bulk change out of stock type to be "not_available" for following products:
      | reference |
      | product1  |
      | product2  |
      | product3  |
    Then product "product1" should have following stock information:
      | out_of_stock_type | not_available |
    And product "product2" should have following stock information:
      | out_of_stock_type | not_available |
    And product "product3" should have following stock information:
      | out_of_stock_type | not_available |

  Scenario: I allow orders when out of stock for a selection
    Given I bulk change out of stock type to be "not_available" for following products:
      | reference |
      | product1  |
      | product2  |
    And product "product1" should have following stock information:
      | out_of_stock_type | not_available |
    When I bulk change out of stock type to be "available" for following products:
      | reference |
      | product1  |
      | product2  |
    Then product "product1" should have following stock information:
      | out_of_stock_type | available |
    And product "product2" should have following stock information:
      | out_of_stock_type | available |

  Scenario: A product left out of the selection keeps its own behavior
    Given I bulk change out of stock type to be "available" for following products:
      | reference |
      | product1  |
      | product2  |
      | product3  |
    When I bulk change out of stock type to be "not_available" for following products:
      | reference |
      | product1  |
      | product2  |
    Then product "product1" should have following stock information:
      | out_of_stock_type | not_available |
    And product "product2" should have following stock information:
      | out_of_stock_type | not_available |
    And product "product3" should have following stock information:
      | out_of_stock_type | available |
