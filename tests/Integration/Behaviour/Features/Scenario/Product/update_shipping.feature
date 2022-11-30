# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-shipping
@restore-products-before-feature
@clear-cache-before-feature
@update-shipping
Feature: Update product shipping options from Back Office (BO)
  As a BO user I must be able to update product shipping options from BO

  Scenario: I update product shipping
    Given I add product "product1" with following information:
      | name[en-US] | Last samurai dvd |
      | type        | standard         |
    And product product1 should have following shipping information:
      | width                                   | 0       |
      | height                                  | 0       |
      | depth                                   | 0       |
      | weight                                  | 0       |
      | additional_shipping_cost                | 0       |
      | delivery time notes type                | default |
      | delivery time in stock notes[en-US]     |         |
      | delivery time out of stock notes[en-US] |         |
      | carriers                                | []      |
    Given carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    When I update product "product1" with following values:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
    And I assign product product1 with following carriers:
      | carrier1 |
      | carrier2 |
    Then product product1 should have following shipping information:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |

  Scenario: Only update product dimensions without changing other values
    Given product product1 should have following shipping information:
      | width                                   | 10.5                 |
      | height                                  | 6                    |
      | depth                                   | 7                    |
      | weight                                  | 0.5                  |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |
    When I update product "product1" with following values:
      | width  | 15 |
      | height | 5  |
      | depth  | 4  |
      | weight | 2  |
    Then product product1 should have following shipping information:
      | width                                   | 15                   |
      | height                                  | 5                    |
      | depth                                   | 4                    |
      | weight                                  | 2                    |
      | additional_shipping_cost                | 12                   |
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
      | carriers                                | [carrier1,carrier2]  |

  Scenario: Provide negative product dimensions
    Given product product1 should have following shipping information:
      | width  | 15 |
      | height | 5  |
      | depth  | 4  |
      | weight | 2  |
    When I update product "product1" with following values:
      | width | -15 |
    Then I should get error that product width is invalid
    When I update product "product1" with following values:
      | height | -5 |
    Then I should get error that product height is invalid
    When I update product "product1" with following values:
      | depth | -4 |
    Then I should get error that product depth is invalid
    When I update product "product1" with following values:
      | weight | -2 |
    Then I should get error that product weight is invalid
    And product product1 should have following shipping information:
      | width  | 15 |
      | height | 5  |
      | depth  | 4  |
      | weight | 2  |

  Scenario: Provide negative additional shipping cost
    Given product product1 should have following shipping information:
      | additional_shipping_cost | 12 |
    When I update product "product1" with following values:
      | additional_shipping_cost | -12 |
    Then I should get error that product additional_shipping_cost is invalid

  Scenario: Provide invalid delivery notes
    Given product product1 should have following shipping information:
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
    When I update product "product1" with following values:
      | delivery time in stock notes[en-US] | bla bla <{} |
    Then I should get error that product delivery_in_stock is invalid
    When I update product "product1" with following values:
      | delivery time out of stock notes[en-US] | ble ble >= |
    Then I should get error that product delivery_out_stock is invalid
    And product product1 should have following shipping information:
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |

  Scenario: Remove delivery time notes, but still have specific notes type
    Given product product1 should have following shipping information:
      | delivery time notes type                | specific             |
      | delivery time in stock notes[en-US]     | product in stock     |
      | delivery time out of stock notes[en-US] | product out of stock |
    When I update product "product1" with following values:
      | delivery time in stock notes[en-US]     |  |
      | delivery time out of stock notes[en-US] |  |
    Then product product1 should have following shipping information:
      | delivery time notes type                | specific |
      | delivery time in stock notes[en-US]     |          |
      | delivery time out of stock notes[en-US] |          |

  Scenario: Remove all product carriers
    When I assign product product1 with following carriers:
      | carrier1 |
      | carrier2 |
    Then product product1 should have following shipping information:
      | carriers | [carrier1,carrier2] |
