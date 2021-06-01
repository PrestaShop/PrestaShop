# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags specific-price-priorities
@reset-database-before-feature
@clear-cache-before-feature
@specific-price-priorities
@specific-prices
Feature: Set Specific Price priorities from Back Office (BO).
  As an employee I want to be able to set specific price priorities to single product and to all products

  Scenario: I set specific price priorities to single product
    Given I add product "product1" with following information:
      | name[en-US] | pocket watch |
      | type        | standard     |
    And product "product1" should have following specific price priorities:
      | id_shop | id_currency | id_country | id_group |
    When I set following specific price priorities for product "product1":
      | id_country | id_currency | id_group | id_shop |
    Then product "product1" should have following specific price priorities:
      | id_country | id_currency | id_group | id_shop |
    When I set following specific price priorities for product "product1":
      | id_currency | id_country | id_group | id_shop |
    Then product "product1" should have following specific price priorities:
      | id_currency | id_country | id_group | id_shop |

  Scenario: I set specific price priorities to all products
    Given I add product "product2" with following information:
      | name[en-US] | golden wrist watch |
      | type        | standard           |
    And I add product "product3" with following information:
      | name[en-US] | silver wrist watch |
      | type        | standard           |
    And product "product2" should have following specific price priorities:
      | id_shop | id_currency | id_country | id_group |
    And product "product3" should have following specific price priorities:
      | id_shop | id_currency | id_country | id_group |
    When I set following specific price priorities for all products:
      | id_country | id_currency | id_group | id_shop |
    Then product "product2" should have following specific price priorities:
      | id_country | id_currency | id_group | id_shop |
    And product "product3" should have following specific price priorities:
      | id_country | id_currency | id_group | id_shop |

  Scenario: Specific price priorities that was set to a single product
  overrides the previously set global priorities (overrides priorities for this product only)
    Given product "product1" should have following specific price priorities:
      | id_country | id_currency | id_group | id_shop |
    And product "product2" should have following specific price priorities:
      | id_country | id_currency | id_group | id_shop |
    And product "product3" should have following specific price priorities:
      | id_country | id_currency | id_group | id_shop |
    When I set following specific price priorities for all products:
      | id_shop | id_currency | id_group | id_country |
    Then product "product1" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    And product "product2" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    And product "product3" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    When I set following specific price priorities for product "product1":
      | id_group | id_currency | id_country | id_shop |
    Then product "product2" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    And product "product3" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    But product "product1" should have following specific price priorities:
      | id_group | id_currency | id_country | id_shop |

  Scenario: Specific price priorities that was set to all products
  overrides the previously set priorities for a single product (overrides priorities for all products)
    Given product "product1" should have following specific price priorities:
      | id_group | id_currency | id_country | id_shop |
    And product "product2" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    And product "product3" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    When I set following specific price priorities for product "product1":
      | id_shop | id_currency | id_group | id_country |
    And I set following specific price priorities for product "product2":
      | id_currency | id_shop | id_group | id_country |
    And I set following specific price priorities for product "product3":
      | id_country | id_shop | id_group | id_currency |
    Then product "product1" should have following specific price priorities:
      | id_shop | id_currency | id_group | id_country |
    And product "product2" should have following specific price priorities:
      | id_currency | id_shop | id_group | id_country |
    And product "product3" should have following specific price priorities:
      | id_country | id_shop | id_group | id_currency |
    When I set following specific price priorities for all products:
      | id_group | id_currency | id_shop | id_country |
    Then product "product1" should have following specific price priorities:
      | id_group | id_currency | id_shop | id_country |
    And product "product2" should have following specific price priorities:
      | id_group | id_currency | id_shop | id_country |
    And product "product1" should have following specific price priorities:
      | id_group | id_currency | id_shop | id_country |
