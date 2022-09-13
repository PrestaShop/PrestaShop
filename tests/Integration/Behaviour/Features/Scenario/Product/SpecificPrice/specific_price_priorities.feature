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
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    And I add product "product1" with following information:
      | name[en-US] | pocket watch |
      | type        | standard     |
    And product "product1" should not have custom specific price priorities
    And default specific price priorities should be used for product "product1"
    When I set following custom specific price priorities for product "product1":
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
#   Checks ProductForEditing priorities
    Then product "product1" should have following custom specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
#   Checks legacy priorities which are actually responsible for prioritizing in FO
    And following specific price priorities should be used for product "product1":
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |

  Scenario: Default specific price priorities should be applied for products that has no custom priorities set
    Given default specific price priorities are set to following:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_country  |
      | id_shop     |
    And I add product "product2" with following information:
      | name[en-US] | golden wrist watch |
      | type        | standard           |
    And I add product "product3" with following information:
      | name[en-US] | silver wrist watch |
      | type        | standard           |
    And product "product2" should not have custom specific price priorities
    And default specific price priorities should be used for product "product2"
    And product "product3" should not have custom specific price priorities
    And default specific price priorities should be used for product "product3"
    And product "product1" should have following custom specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    When I set following default specific price priorities:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_shop     |
      | id_country  |
    Then default specific price priorities should be the following:
      | priorities  |
      | id_group    |
      | id_currency |
      | id_shop     |
      | id_country  |
    And product "product2" should not have custom specific price priorities
    And default specific price priorities should be used for product "product2"
    And product "product3" should not have custom specific price priorities
    And default specific price priorities should be used for product "product3"
    But following specific price priorities should be used for product "product1":
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    And product "product1" should have following custom specific price priorities:
      | priorities  |
      | id_country  |
      | id_currency |
      | id_group    |
      | id_shop     |
    When I remove custom specific price priorities for product "product1"
    Then product "product1" should not have custom specific price priorities
    And default specific price priorities should be used for product "product1"
    And following specific price priorities should be used for product "product1":
      | priorities  |
      | id_group    |
      | id_currency |
      | id_shop     |
      | id_country  |
