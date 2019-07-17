# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier
@reset-database-before-feature
Feature: Carrier Management
  PrestaShop allows BO users to manage carriers
  As a BO user
  I must be able to create, edit and delete carrier in my shop

  Scenario: Adding new carrier with priced shipping
    When I add new carrier "my carrier" with following properties:
      | carrier_name              | My carrier                                        |
      | shipping_delay            | Pickup in store                                   |
      | speed_grade               | 1                                                 |
      | tracking_url              | http://example.com/track.php?num=@                |
      | shipping_cost_included    | 1                                                 |
      | shipping_method           | 2                                                 |
      | tax_rules_group_id        | 1                                                 |
      | out_of_range_behavior     | 1                                                 |
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
    And Carrier "my carrier" "shipping method" should be "2"
    And Carrier "my carrier" "out of range behavior" should be "1"
    And Carrier "my carrier" "tracking url" should be "http://example.com/track.php?num=@"
    And Carrier "my carrier" "max package width" should be "10"
    And Carrier "my carrier" "max package height" should be "20"
    And Carrier "my carrier" "max package depth" should be "15"
    And Carrier "my carrier" "max package weight" should be "5.500000"

  Scenario: Adding new carrier with free shipping
    When I add new carrier "Free shipping carrier" with free shipping and following properties:
      | carrier_name              | Free shipping carrier                             |
      | shipping_delay            | One week                                              |
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
    And Carrier "Free shipping carrier" "shipping method" should be "1"
    And Carrier "Free shipping carrier" "out of range behavior" should be "1"
    And Carrier "Free shipping carrier" "tracking url" should be "http://example.com/track.php?num=@"
    And Carrier "Free shipping carrier" "max package width" should be "10"
    And Carrier "Free shipping carrier" "max package height" should be "20"
    And Carrier "Free shipping carrier" "max package depth" should be "15"
    And Carrier "Free shipping carrier" "max package weight" should be "5.500000"
