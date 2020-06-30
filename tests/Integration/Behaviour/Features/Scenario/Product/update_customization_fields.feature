# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-customization-fields
@reset-database-before-feature
@update-customization-fields
Feature: Update product customization fields in Back Office (BO)
  As a BO user
  I need to be able to update product customization fields in the BO

  Scenario: I add customization fields to product
    When I add product "product1" with following information:
      | name       | en-US: nice customizable t-shirt  |
      | is_virtual | false                             |
    And product "product1" type should be standard
    When I update product product1 with following customization fields:
      | customization id      | type    | name                    | is required |
      |                       | text    | en-US:front-text        | true         |
      |                       | text    | en-US:back-text         | false       |
    Then product product1 should have following customization fields:
      | customization id      | type    | name                    | is required |
      | 2                     | text    | en-US:front-text        | true        |
      | 3                     | text    | en-US:back-text         | false       |

  Scenario: I update some product customization fields and add additional one
    Given product product1 should have following customization fields:
      | customization id      | type    | name                    | is required |
      | 2                     | text    | en-US:front-text        | true        |
      | 3                     | text    | en-US:back-text         | false       |
    When I update product product1 with following customization fields:
      | customization id      | type    | name                    | is required |
      | 2                     | text    | en-US:front-text        | false       |
      | 3                     | text    | en-US:bottom-text       | true        |
      |                       | file    | en-US:back image        | false       |
    Then product product1 should have following customization fields:
      | customization id      | type    | name                    | is required |
      | 2                     | text    | en-US:front-text        | false       |
      | 3                     | text    | en-US:bottom-text       | true        |
      | 4                     | file    | en-US:back image        | false       |

  Scenario: I delete some product customization fields
    Given product product1 should have following customization fields:
      | customization id      | type    | name                    | is required |
      | 2                     | text    | en-US:front-text        | false       |
      | 3                     | text    | en-US:bottom-text       | true        |
      | 4                     | file    | en-US:back image        | false       |
    When I update product product1 with following customization fields:
      | customization id      | type    | name                    | is required |
      | 3                     | text    | en-US:bottom-text        | true       |
    Then product product1 should have following customization fields:
      | customization id      | type    | name                    | is required |
      | 3                     | text    | en-US:bottom-text        | true       |
