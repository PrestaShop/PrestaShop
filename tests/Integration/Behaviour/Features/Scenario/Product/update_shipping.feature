# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-shipping
@reset-database-before-feature
@update-shipping
Feature: Update product shipping options from Back Office (BO)
  As a BO user I must be able to update product shipping options from BO

  Scenario: I update product shipping
    Given I add product "product1" with following information:
      | name       | en-US:Last samurai dvd |
      | is_virtual | false                  |
    And product product1 should have following values:
    #@todo: following values arent handled yet int context
      | width                       | 0                 |
      | height                      | 0                 |
      | depth                       | 0                 |
      | weight                      | 0                 |
      | additional_shipping_cost    | 0                 |
      | additional_delivery_times   | default           |
      | delivery_in_stock           | en-US:            |
      | delivery_out_stock          | en-US:            |
    And product product1 should have no carriers assigned
