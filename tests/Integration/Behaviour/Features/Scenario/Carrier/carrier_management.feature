# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s carrier
@reset-database-before-feature
Feature: Carrier Management
  PrestaShop allows BO users to manage carriers
  As a BO user
  I must be able to create, edit and delete carrier in my shop

  Scenario: Adding new carrier
    When I add new carrier "my carrier" with following properties:
      | carrier_name         | My carrier                                        |
      | shipping_delay       | Pickup in store                 |
      | speed_grade          | 1                         |
      | tracking_url         | http://example.com/track.php?num=@             |
      | shipping_cost_included| 1               |
      | shipping_method      | 2                         |
      | tax_rules_group_id   | 1|
      | out_of_range_behavior| 1                                        |
      | ranges_from               | 1, 2, 3|
      | ranges_to                 | 2, 2, 4|
      | zone_ids                     | 1, 2, 3|
      | prices                       | 5, 6, 7|
      | max_width                    | 0      |
      | max_height                    | 0      |
      | max_depth                    | 0      |
      | max_weight                    | 0.0      |
      | group_ids                    | 1      |
      | shop_ids                    | 1      |

