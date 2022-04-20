# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-status
@reset-database-before-feature
@clear-cache-before-feature
@update-status
Feature: Bulk update product status from BO (Back Office)
  As an employee I must be able to bulk update product status (enable/disable)

  Scenario: I update product statuses
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
    And product "product3" has following combinations:
      | reference | quantity | attributes         |
      | whiteS    | 100      | Size:S;Color:White |
      | whiteM    | 150      | Size:M;Color:White |
      | blackM    | 130      | Size:M;Color:Black |
    And product product3 type should be combinations
    And product "product3" should be disabled
    And I bulk change status to be enabled for following products:
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
