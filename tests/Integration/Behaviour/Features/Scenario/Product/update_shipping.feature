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
      | width                            | 0                 |
      | height                           | 0                 |
      | depth                            | 0                 |
      | weight                           | 0                 |
      | additional_shipping_cost         | 0                 |
      | delivery time notes type         | default           |
      | delivery time in stock notes     | en-US:            |
      | delivery time out of stock notes | en-US:            |
      | carriers                         | []                |
    Given carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    When I update product product1 shipping information with following values:
      | width                            | 10.5                       |
      | height                           | 6                          |
      | depth                            | 7                          |
      | weight                           | 0.5                        |
      | additional_shipping_cost         | 12                         |
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:product in stock     |
      | delivery time out of stock notes | en-US:product out of stock |
      | carriers                         | [carrier1,carrier2]        |
    Then product product1 should have following values:
      | width                            | 10.5                       |
      | height                           | 6                          |
      | depth                            | 7                          |
      | weight                           | 0.5                        |
      | additional_shipping_cost         | 12                         |
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:product in stock     |
      | delivery time out of stock notes | en-US:product out of stock |
      | carriers                         | [carrier1,carrier2]        |

  Scenario: Only update product dimensions without changing other values
    Given product product1 should have following values:
      | width                            | 10.5                       |
      | height                           | 6                          |
      | depth                            | 7                          |
      | weight                           | 0.5                        |
      | additional_shipping_cost         | 12                         |
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:product in stock     |
      | delivery time out of stock notes | en-US:product out of stock |
      | carriers                         | [carrier1,carrier2]        |
    When I update product product1 shipping information with following values:
      | width                            | 15                         |
      | height                           | 5                          |
      | depth                            | 4                          |
      | weight                           | 2                          |
    Then product product1 should have following values:
      | width                            | 15                         |
      | height                           | 5                          |
      | depth                            | 4                          |
      | weight                           | 2                          |
      | additional_shipping_cost         | 12                         |
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:product in stock     |
      | delivery time out of stock notes | en-US:product out of stock |
      | carriers                         | [carrier1,carrier2]        |

  Scenario: Provide negative product dimensions
    Given product product1 should have following values:
      | width                            | 15                         |
      | height                           | 5                          |
      | depth                            | 4                          |
      | weight                           | 2                          |
    When I update product product1 shipping information with following values:
      | width                            | -15                        |
    Then I should get error that product width is invalid
    When I update product product1 shipping information with following values:
      | height                            | -5                        |
    Then I should get error that product height is invalid
    When I update product product1 shipping information with following values:
      | depth                            | -4                         |
    Then I should get error that product depth is invalid
    When I update product product1 shipping information with following values:
      | weight                            | -2                        |
    Then I should get error that product weight is invalid

  Scenario: Provide negative additional shipping cost
    Given product product1 should have following values:
      | additional_shipping_cost         | 12                         |
    When I update product product1 shipping information with following values:
      | additional_shipping_cost         | -12                        |
    Then I should get error that product additional_shipping_cost is invalid

    #@todo: finish up with field validations

  Scenario: Remove delivery time notes, but still have specific notes type
    Given product product1 should have following values:
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:product in stock     |
      | delivery time out of stock notes | en-US:product out of stock |
    When I update product product1 shipping information with following values:
      | delivery time in stock notes     | en-US:                     |
      | delivery time out of stock notes | en-US:                     |
    Given product product1 should have following values:
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:                     |
      | delivery time out of stock notes | en-US:                     |
