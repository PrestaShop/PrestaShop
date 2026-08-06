# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier --tags carrier-available
@restore-all-tables-before-feature
@clear-cache-before-feature
@carrier-available
Feature: Carrier available
  As a BO user i want to recover the common carriers of different products, as well as those filtered

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I set up shop context to single shop shop1
    And language "fr" with locale "fr-FR" exists
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one
    And I add new zone "zone1" with following properties:
      | name    | zone1  |
      | enabled | true   |
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"

Scenario: Carrier available when is deleted
  Given I add product "s3_product1" with following information:
    | name[en-US] | product D |
    | type        | standard  |
  And I create carrier "s42_carrier" with specified properties:
    | name  | Deleted Carrier |
    | zones | zone1           |
  And I assign product "s3_product1" with following carriers:
    | s42_carrier |
  And I soft delete carrier "s42_carrier"
  Then the products "s3_product1" should have the following carriers with address "address1":
    | carrier          | carrier_reference | state     | products  |
    | Deleted Carrier  | s42_carrier       | available | product D |
    | Click and collect|                   | filtered  | product D |
    | My carrier       |                   | filtered  | product D |

Scenario: Get available carriers for existing order
  Given I add product "s0_product1" with following information:
    | name[fr-FR] | bouteille de bière |
    | name[en-US] | bottle of beer     |
    | type        | standard           |
  And I add product "s0_product2" with following information:
    | name[fr-FR] | bouteille de rhum |
    | name[en-US] | bottle of rhum    |
    | type        | standard          |
  And I add product "s0_product3" with following information:
    | name[en-US] | bottle of whiskey |
    | type        | standard          |
  And I create carrier "s0_standard_carrier" with specified properties:
    | name | s0_standard |
  And I create carrier "s0_express_carrier" with specified properties:
    | name | s0_express |
  And I create carrier "s0_pickup_carrier" with specified properties:
    | name | s0_pickup |
  And I assign product "s0_product1" with following carriers:
    | s0_standard_carrier |
    | s0_pickup_carrier   |
    | s0_express_carrier  |
  And I assign product "s0_product2" with following carriers:
    | s0_standard_carrier |
    | s0_pickup_carrier   |
  Then the products "s0_product1, s0_product2, s0_product3" should have the following carriers with address "address1":
    | carrier           | state     | products                          |
    | s0_express        | filtered  | bottle of beer, bottle of whiskey |
    | Click and collect | filtered  | bottle of whiskey                 |
    | My carrier        | filtered  | bottle of whiskey                 |
    | s0_standard       | available |                                   |
    | s0_pickup         | available |                                   |

Scenario: All products share the exact same carriers
  Given I add product "s1_product1" with following information:
    | name[en-US] | product A |
    | type        | standard  |
  And I add product "s1_product2" with following information:
    | name[en-US] | product B |
    | type        | standard  |
  And I create carrier "s1_common_carrier" with specified properties:
    | name  | Common Carrier |
    | zones | zone1          |
  And I assign product "s1_product1" with following carriers:
    | s1_common_carrier |
  And I assign product "s1_product2" with following carriers:
    | s1_common_carrier |
  Then the products "s1_product1, s1_product2" should have the following carriers with address "address1":
    | carrier        | state     | products |
    | Common Carrier | available |          |

Scenario: No common carriers between products
  Given I add product "s2_product1" with following information:
    | name[en-US] | product A |
    | type        | standard  |
  And I add product "s2_product2" with following information:
    | name[en-US] | product B |
    | type        | standard  |
  And I create carrier "s2_carrier1" with specified properties:
    | name  | Carrier One |
    | zones | zone1   |
  And I create carrier "s2_carrier2" with specified properties:
    | name  | Carrier Two |
    | zones | zone1       |
  And I assign product "s2_product1" with following carriers:
    | s2_carrier1 |
  And I assign product "s2_product2" with following carriers:
    | s2_carrier2 |
  Then the products "s2_product1, s2_product2" should have the following carriers with address "address1":
    | carrier     | state     | products  |
    | Carrier One | filtered  | product A |
    | Carrier Two | filtered  | product B |

Scenario: Carrier filtered due to exceeding max weight
  Given I add product "s4_heavy_product" with following information:
    | name[en-US] | Heavy Box |
    | type        | standard  |
  And I create carrier "s4_weight_carrier" with specified properties:
    | name       | Weight Carrier |
    | max_weight | 10             |
    | zones      | zone1          |
  And I assign product "s4_heavy_product" with following carriers:
    | s4_weight_carrier |
  And I update product "s4_heavy_product" with following values:
    | weight | 15 |
  Then the products "s4_heavy_product" should have the following carriers with address "address1":
    | carrier        | state    | products  |
    | Weight Carrier | filtered | Heavy Box |

Scenario: Carrier filtered due to exceeding max width
  Given I add product "s5_wide_product" with following information:
    | name[en-US] | Wide Frame |
    | type        | standard   |
  And I create carrier "s5_width_carrier" with specified properties:
    | name      | Width Carrier |
    | max_width | 50            |
    | zones     | zone1         |
  And I assign product "s5_wide_product" with following carriers:
    | s5_width_carrier |
  And I update product "s5_wide_product" with following values:
    | width | 60 |
  Then the products "s5_wide_product" should have the following carriers with address "address1":
    | carrier       | state    | products   |
    | Width Carrier | filtered | Wide Frame |

Scenario: Carrier filtered due to exceeding max height
  Given I add product "s6_tall_product" with following information:
    | name[en-US] | Tall Statue |
    | type        | standard    |
  And I create carrier "s6_height_carrier" with specified properties:
    | name       | Height Carrier |
    | max_height | 100            |
    | zones      | zone1          |
  And I assign product "s6_tall_product" with following carriers:
    | s6_height_carrier |
  And I update product "s6_tall_product" with following values:
    | height | 150 |
  Then the products "s6_tall_product" should have the following carriers with address "address1":
    | carrier        | state    | products    |
    | Height Carrier | filtered | Tall Statue |

Scenario: Carrier filtered due to exceeding max depth
  Given I add product "s7_deep_product" with following information:
    | name[en-US] | Deep Box |
    | type        | standard |
  And I create carrier "s7_depth_carrier" with specified properties:
    | name      | Depth Carrier |
    | max_depth | 40            |
    | zones     | zone1         |
  And I assign product "s7_deep_product" with following carriers:
    | s7_depth_carrier |
  And I update product "s7_deep_product" with following values:
    | depth | 45 |
  Then the products "s7_deep_product" should have the following carriers with address "address1":
    | carrier       | state    | products  |
    | Depth Carrier | filtered | Deep Box  |

Scenario: Carrier available when product fits all constraints
  Given I add product "s8_fit_product" with following information:
    | name[en-US] | Perfect Box |
    | type        | standard    |
  And I create carrier "s8_perfect_carrier" with specified properties:
    | name        | Perfect Carrier |
    | max_width   | 50              |
    | max_height  | 50              |
    | max_depth   | 50              |
    | max_weight  | 20              |
    | zones       | zone1           |
  And I assign product "s8_fit_product" with following carriers:
    | s8_perfect_carrier |
  And I update product "s8_fit_product" with following values:
    | width  | 40 |
    | height | 40 |
    | depth  | 30 |
    | weight | 10 |
  Then the products "s8_fit_product" should have the following carriers with address "address1":
    | carrier         | state     | products     |
    | Perfect Carrier | available |              |

Scenario: Carrier available when carrier constraints is not set
  Given I add product "s81_fit_product" with following information:
    | name[en-US] | Perfect Box |
    | type        | standard    |
  And I create carrier "s81_zero_carrier" with specified properties:
    | name        | Zero Carrier |
    | max_width   | 0            |
    | max_height  | 0            |
    | max_depth   | 0            |
    | max_weight  | 0            |
    | zones       | zone1        |
  And I assign product "s81_fit_product" with following carriers:
    | s81_zero_carrier |
  And I update product "s81_fit_product" with following values:
    | width  | 40 |
    | height | 40 |
    | depth  | 30 |
    | weight | 10 |
  Then the products "s81_fit_product" should have the following carriers with address "address1":
    | carrier         | state     | products |
    | Zero Carrier    | available |          |

Scenario: Get available carrier when product constraints are equal to carrier limits
  Given I add product "s9_precise_product" with following information:
    | name[en-US] | precise product |
    | type        | standard        |
  And I create carrier "s9_limit_carrier" with specified properties:
    | name         | Limit Carrier |
    | max_width    | 10            |
    | max_height   | 20            |
    | max_depth    | 30            |
    | max_weight   | 5             |
    | zones        | zone1         |
  And I assign product "s9_precise_product" with following carriers:
    | s9_limit_carrier |
  And I update product "s9_precise_product" with following values:
    | width  | 10  |
    | height | 20  |
    | depth  | 30  |
    | weight | 5   |
  Then the products "s9_precise_product" should have the following carriers with address "address1":
    | carrier       | state     | products       |
    | Limit Carrier | available |                |

Scenario: Get filtered carrier when product exceeds weight constraint by a small margin
  Given I add product "s10_heavy_product" with following information:
    | name[en-US] | Heavy Product |
    | type        | standard      |
  And I create carrier "s10_weight_limit_carrier" with specified properties:
    | name         | Weight Limited Carrier |
    | max_width    | 50                     |
    | max_height   | 50                     |
    | max_depth    | 50                     |
    | max_weight   | 5                      |
    | zones        | zone1                  |
  And I assign product "s10_heavy_product" with following carriers:
    | s10_weight_limit_carrier |
  And I update product "s10_heavy_product" with following values:
    | width  | 30   |
    | height | 30   |
    | depth  | 30   |
    | weight | 5.01 |
  Then the products "s10_heavy_product" should have the following carriers with address "address1":
    | carrier                | state    | products       |
    | Weight Limited Carrier | filtered | Heavy Product  |

Scenario: Test zone eligibility by address
  Given I add product "s11_zoned_product" with following information:
    | name[en-US] | Zoned Product |
    | type        | standard      |
  And I create carrier "s11_zone1_carrier" with specified properties:
    | name  | Zone 1 Carrier |
    | zones | zone1          |
  And I assign product "s11_zoned_product" with following carriers:
    | s11_zone1_carrier |
  Then the products "s11_zoned_product" should have the following carriers with address "address1":
    | carrier        | state     | products       |
    | Zone 1 Carrier | available | Zoned Product  |

Scenario: Test zone ineligibility by address
  Given I add new zone "zone2" with following properties:
    | name    | zone2  |
    | enabled | true   |
  And I add product "s12_zoned_product" with following information:
    | name[en-US] | Zoned_2 Product |
    | type        | standard      |
  And I create carrier "s12_zone_carrier" with specified properties:
    | name  | Zone 2 Carrier |
    | zones | zone2          |
  And I assign product "s12_zoned_product" with following carriers:
    | s12_zone_carrier |
  Then the products "s12_zoned_product" should have the following carriers with address "address1":
    | carrier        | state    | products        |
    | Zone 2 Carrier | filtered | Zoned_2 Product |
