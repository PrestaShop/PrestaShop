# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-options
@reset-database-before-feature
@clear-cache-before-feature
@update-options
Feature: Update product options from Back Office (BO)
  As a BO user
  I need to be able to update product options from BO

  Background:
    Given manufacturer studioDesign named "Studio Design" exists
    And manufacturer graphicCorner named "Graphic Corner" exists

  Scenario: I update product options
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following options:
      | product option      | value |
      | active              | false |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    When I update product "product1" options with following values:
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    Then product "product1" should have following options:
      | product option      | value        |
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |

  Scenario: I only update product availability for order, leaving other properties unchanged
    Given product "product1" should have following options:
      | product option      | value        |
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I update product "product1" options with following values:
      | available_for_order | true |
    Then product "product1" should have following options:
      | product option      | value        |
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |

  Scenario: I update manufacturer and check the relationship is updated correctly
    Given product "product1" should have following options:
      | product option      | value        |
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I update product "product1" options with following values:
      | manufacturer | graphicCorner |
    Then product "product1" should have following options:
      | product option      | value         |
      | active              | true          |
      | visibility          | catalog       |
      | available_for_order | true          |
      | online_only         | true          |
      | show_price          | false         |
      | condition           | used          |
      | show_condition      | true          |
      | manufacturer        | graphicCorner |
    When I update product "product1" options with following values:
      | manufacturer |  |
    Then product "product1" should have following options:
      | product option      | value   |
      | active              | true    |
      | visibility          | catalog |
      | available_for_order | true    |
      | online_only         | true    |
      | show_price          | false   |
      | condition           | used    |
      | show_condition      | true    |
      | manufacturer        |         |

  Scenario: I update product options providing invalid values
    Given I add product "product2" with following information:
      | name[en-US] | 'The truth is out there' wallpaper |
      | type        | virtual                            |
    And product "product2" should have following options:
      | product option      | value |
      | active              | false |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    When I assign non existing manufacturer to product "product2"
    Then I should get error that manufacturer does not exist
    When I update product "product2" options with following values:
      | visibility | show it to me plz |
    Then I should get error that product visibility is invalid
    When I update product "product2" options with following values:
      | condition | very good condition |
    Then I should get error that product condition is invalid
    And product "product2" should have following options:
      | product option      | value |
      | active              | false |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
