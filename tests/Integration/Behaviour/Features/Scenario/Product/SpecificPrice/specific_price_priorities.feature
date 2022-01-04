# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags specific-price-priorities
@restore-products-before-feature
@restore-specific-prices-priorities-after-feature
@clear-cache-before-feature
@specific-price-priorities
@specific-prices
Feature: Set Specific Price priorities from Back Office (BO).
  As an employee I want to be able to set specific price priorities to single product and to all products

  Scenario: I set specific price priorities to single product
    Given default specific price priorities are set to following:
      | priorities  |
      | id_shop     |
      | id_currency |
      | id_country  |
      | id_group    |
    And I add product "product1" with following information:
      | name[en-US] | pocket watch |
      | type        | standard     |
    And product "product1" should have following specific price priorities:
      | priorities  |
      | id_shop     |
      | id_currency |
      | id_country  |
      | id_group    |
    When I set following specific price priorities for product "product1":
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    Then product "product1" should have following specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |

  Scenario: Default specific price priorities should be applied for products that has no specific priorities set
    Given default specific price priorities are set to following:
      | priorities  |
      | id_shop     |
      | id_currency |
      | id_country  |
      | id_group    |
    And product "product1" should have following specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    And I add product "product2" with following information:
      | name[en-US] | golden wrist watch |
      | type        | standard           |
    And I add product "product3" with following information:
      | name[en-US] | silver wrist watch |
      | type        | standard           |
    And product "product2" should have following specific price priorities:
      | priorities  |
      | id_shop     |
      | id_currency |
      | id_country  |
      | id_group    |
    And product "product3" should have following specific price priorities:
      | priorities  |
      | id_shop     |
      | id_currency |
      | id_country  |
      | id_group    |
    When I set following default specific price priorities:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    Then default specific price priorities should be the following:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    And product "product2" should have following specific price priorities:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    And product "product3" should have following specific price priorities:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    But product "product1" should have following specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    When I set following specific price priorities for product "product2":
      | priorities  |
      | id_currency |
      | id_group    |
      | id_country  |
      | id_shop     |
    Then product "product2" should have following specific price priorities:
      | priorities  |
      | id_currency |
      | id_group    |
      | id_country  |
      | id_shop     |
