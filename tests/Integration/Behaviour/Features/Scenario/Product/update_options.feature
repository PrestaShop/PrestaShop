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
      | is_virtual  | false         |
    And product "product1" should have following options:
      | product option      | value |
      | active              | false |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | manufacturer        |       |
    When I update product "product1" options with following values:
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | manufacturer        | studioDesign |
    Then product "product1" should have following options:
      | product option      | value        |
      | active              | true         |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
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
      | manufacturer        | studioDesign |

  Scenario: I update manufacturer with invalid values
    Given manufacturer "studioDesign" should be assigned to product product1
    When I update product "product1" options with following values:
      | manufacturer | invalid |
    Then I should get error that assigned manufacturer is invalid
    When I update product "product1" options with following values:
      | manufacturer | non-existent |
    Then I should get error that assigned manufacturer does not exist

  Scenario: I update manufacturer and check the relationship is updated correctly
    Given manufacturer "studioDesign" should be assigned to product product1
    When I update product "product1" options with following values:
      | manufacturer | graphicCorner |
    Then manufacturer "graphicCorner" should be assigned to product product1
    When I update product "product1" options with following values:
      | manufacturer |  |
    Then product product1 should have no manufacturer assigned

  Scenario: I update product options providing invalid values
    Given I add product "product2" with following information:
      | name[en-US] | 'The truth is out there' wallpaper |
      | is_virtual  | true                               |
    And product "product2" should have following options:
      | product option      | value |
      | active              | false |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | manufacturer        |       |
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
      | manufacturer        |       |
