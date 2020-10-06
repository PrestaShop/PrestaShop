# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags specific-price-priorities
@reset-database-before-feature
@specific-price-priorities
Feature: Set Specific Price priorities from Back Office (BO).
  As an employee I want to be able to set specific price priorities to single product and to all products

  Scenario: I set specific price priorities to single product
    Given I add product "product1" with following information:
      | name       | en-US:pocket watch   |
      | is_virtual | false                |
    And product "product1" type should be standard
    When I set following specific price priorities for product "product1":
      | [id_country, id_currency, id_group, id_shop] |
    Then product "product1" should have following specific price priorities:
      | [id_country, id_currency, id_group, id_shop] |
    When I set following specific price priorities for product "product1":
      | [id_currency, id_country, id_group, id_shop] |
    Then product "product1" should have following specific price priorities:
      | [id_currency, id_country, id_group, id_shop] |
