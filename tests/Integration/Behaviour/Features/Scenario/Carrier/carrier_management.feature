# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier
@reset-database-before-feature
Feature: Carrier Management
  PrestaShop allows BO users to manage carriers
  As an employee
  I must be able to create, edit and delete carrier

  Scenario: Adding new carrier with priced shipping
    When I add new carrier "my carrier" with following properties:
      | carrier_name              | My carrier                                        |
      | shipping_delay            | Pickup in store                                   |
      | speed_grade               | 1                                                 |
      | tracking_url              | http://example.com/track.php?num=@                |
      | shipping_cost_included    | true                                              |
      | shipping_method           | price                                             |
      | tax_rules_group_id        | 1                                                 |
      | out_of_range_behavior     | carrier should be disabled                        |
      | ranges_from               | 1, 2, 3                                           |
      | ranges_to                 | 2, 3, 4                                           |
      | zone_ids                  | 1, 2, 3                                           |
      | prices                    | 5, 6, 7                                           |
      | max_width                 | 10                                                |
      | max_height                | 20                                                |
      | max_depth                 | 15                                                |
      | max_weight                | 5.5                                               |
      | group_ids                 | 1                                                 |
      | shop_ids                  | 1                                                 |
    Then Carrier "my carrier" name in default language should be "My carrier"
    And Carrier "my carrier" "name" should be "My carrier"
    And Carrier "my carrier" shipping delay in default language should be "Pickup in store"
    And Carrier "my carrier" "speed grade" should be "1"
    And the shipping of "my carrier" should be priced
    And Carrier "my carrier" shipping price calculation should be based on package price
    And when package is out of carrier "my carrier" range, the carrier should be disabled
    And Carrier "my carrier" "tracking url" should be "http://example.com/track.php?num=@"
    And Carrier "my carrier" "max package width" should be "10"
    And Carrier "my carrier" "max package height" should be "20"
    And Carrier "my carrier" "max package depth" should be "15"
    And Carrier "my carrier" "max package weight" should be "5.500000"
    And Carrier "my carrier" should not belong to module
    And Carrier "my carrier" shipping price should be calculated by PrestaShop

  Scenario: Adding new carrier with free shipping
    When I add new carrier "Free shipping carrier" with free shipping and following properties:
      | carrier_name              | Free shipping carrier                             |
      | shipping_delay            | One week                                          |
      | speed_grade               | 4                                                 |
      | tracking_url              | http://example.com/track.php?num=@                |
      | tax_rules_group_id        | 1                                                 |
      | max_width                 | 10                                                |
      | max_height                | 20                                                |
      | max_depth                 | 15                                                |
      | max_weight                | 5.5                                               |
      | group_ids                 | 1                                                 |
      | shop_ids                  | 1                                                 |
    Then Carrier "Free shipping carrier" name in default language should be "Free shipping carrier"
    And Carrier "Free shipping carrier" "name" should be "Free shipping carrier"
    And Carrier "Free shipping carrier" shipping delay in default language should be "One week"
    And Carrier "Free shipping carrier" "speed grade" should be "4"
    And the shipping of "Free shipping carrier" should be free of charge
    And Carrier "Free shipping carrier" shipping price calculation should be based on package weight
    And when package is out of carrier "Free shipping carrier" range, the highest range price should be applied
    And Carrier "Free shipping carrier" "tracking url" should be "http://example.com/track.php?num=@"
    And Carrier "Free shipping carrier" "max package width" should be "10"
    And Carrier "Free shipping carrier" "max package height" should be "20"
    And Carrier "Free shipping carrier" "max package depth" should be "15"
    And Carrier "Free shipping carrier" "max package weight" should be "5.500000"
    And Carrier "Free shipping carrier" should not belong to module

  Scenario: Adding new module carrier with free shipping
    When I add new module carrier "Fs module carrier" with free shipping and following properties:
      | carrier_name              | Free shipping module carrier                      |
      | shipping_delay            | Instant                                           |
      | speed_grade               | 9                                                 |
      | tracking_url              | https://fs.com?nr=@                               |
      | tax_rules_group_id        | 1                                                 |
      | max_width                 | 20                                                |
      | max_height                | 30                                                |
      | max_depth                 | 50                                                |
      | max_weight                | 50                                                |
      | group_ids                 | 1,2,3                                             |
      | shop_ids                  | 1                                                 |
      | module_name               | fs-shipping                                       |
    Then Carrier "Fs module carrier" name in default language should be "Free shipping module carrier"
    And Carrier "Fs module carrier" "name" should be "Free shipping module carrier"
    And Carrier "Fs module carrier" shipping delay in default language should be "Instant"
    And Carrier "Fs module carrier" "speed grade" should be "9"
    And the shipping of "Fs module carrier" should be free of charge
    And Carrier "Fs module carrier" shipping price calculation should be based on package weight
    And when package is out of carrier "Fs module carrier" range, the highest range price should be applied
    And Carrier "Fs module carrier" "tracking url" should be "https://fs.com?nr=@"
    And Carrier "Fs module carrier" "max package width" should be "20"
    And Carrier "Fs module carrier" "max package height" should be "30"
    And Carrier "Fs module carrier" "max package depth" should be "50"
    And Carrier "Fs module carrier" "max package weight" should be "50.000000"
    And Carrier "Fs module carrier" "module name" should be "fs-shipping"
    And Carrier "Fs module carrier" should belong to module

  Scenario: Adding new module carrier with shipping price which is calculated by PrestaShop core
    When I add new module carrier "P2 carry" with PrestaShop shipping price and following properties:
      | carrier_name              | Pay to carry                                      |
      | shipping_delay            | 2 days                                            |
      | speed_grade               | 3                                                 |
      | tracking_url              | https://www.p2carry.lt                            |
      | shipping_cost_included    | true                                              |
      | shipping_method           | price                                             |
      | tax_rules_group_id        | 1                                                 |
      | out_of_range_behavior     | highest range price should be applied             |
      | ranges_from               | 1, 5, 10, 15, 25, 50, 100                         |
      | ranges_to                 | 5, 10, 15, 25, 50, 100, 200                       |
      | zone_ids                  | 2                                                 |
      | prices                    | 3, 4, 5, 6, 9, 15, 30                             |
      | tax_rules_group_id        | 1, 2                                              |
      | max_width                 | 20                                                |
      | max_height                | 30                                                |
      | max_depth                 | 50                                                |
      | max_weight                | 50                                                |
      | group_ids                 | 1,2,3                                             |
      | shop_ids                  | 1                                                 |
      | module_name               | p2                                                |
    Then Carrier "P2 carry" name in default language should be "Pay to carry"
    And Carrier "P2 carry" "name" should be "Pay to carry"
    And Carrier "P2 carry" shipping delay in default language should be "2 days"
    And Carrier "P2 carry" "speed grade" should be "3"
    And the shipping of "P2 carry" should be priced
    And Carrier "P2 carry" shipping price calculation should be based on package price
    And when package is out of carrier "P2 carry" range, the highest range price should be applied
    And Carrier "P2 carry" "tracking url" should be "https://www.p2carry.lt"
    And Carrier "P2 carry" "max package width" should be "20"
    And Carrier "P2 carry" "max package height" should be "30"
    And Carrier "P2 carry" "max package depth" should be "50"
    And Carrier "P2 carry" "max package weight" should be "50.000000"
    And Carrier "P2 carry" should belong to module
    And Carrier "P2 carry" "module name" should be "p2"
    And Carrier "P2 carry" shipping price should be calculated by PrestaShop

  Scenario: Adding new module carrier with shipping price which is calculated by module
    When I add new module carrier "rollercoaster" with module shipping price and following properties:
      | carrier_name                           | Rollercoaster                                     |
      | shipping_delay                         | Up to one week                                    |
      | speed_grade                            | 3                                                 |
      | tracking_url                           | https://www.rollerCarry.com/id=@                  |
      | shipping_cost_included                 | false                                             |
      | shipping_method                        | weight                                            |
      | tax_rules_group_id                     | 1                                                 |
      | out_of_range_behavior                  | highest range price should be applied             |
      | ranges_from                            | 1, 5, 10, 15, 25, 50, 100                         |
      | ranges_to                              | 5, 10, 15, 25, 50, 100, 200                       |
      | zone_ids                               | 2                                                 |
      | prices                                 | 3, 4, 5, 6, 9, 15, 30                             |
      | tax_rules_group_id                     | 5                                                 |
      | max_width                              | 20                                                |
      | max_height                             | 30                                                |
      | max_depth                              | 50                                                |
      | max_weight                             | 50                                                |
      | group_ids                              | 1,2,3                                             |
      | shop_ids                               | 1                                                 |
      | module_name                            | p2                                                |
      | module_needs_core_shipping_price       | true                                              |
    Then Carrier "rollercoaster" name in default language should be "Rollercoaster"
    And Carrier "rollercoaster" "name" should be "Rollercoaster"
    And Carrier "rollercoaster" shipping delay in default language should be "Up to one week"
    And Carrier "rollercoaster" "speed grade" should be "3"
    And the shipping of "rollercoaster" should be priced
    And Carrier "rollercoaster" shipping price calculation should be based on package weight
    And when package is out of carrier "P2 carry" range, the highest range price should be applied
    And Carrier "rollercoaster" "tracking url" should be "https://www.rollerCarry.com/id=@"
    And Carrier "rollercoaster" "max package width" should be "20"
    And Carrier "rollercoaster" "max package height" should be "30"
    And Carrier "rollercoaster" "max package depth" should be "50"
    And Carrier "rollercoaster" "max package weight" should be "50.000000"
    And Carrier "rollercoaster" should belong to module
    And Carrier "rollercoaster" "module name" should be "p2"
    And Carrier "rollercoaster" shipping price should be calculated by module
    And Carrier "rollercoaster" module should need the shipping price calculated by PrestaShop
