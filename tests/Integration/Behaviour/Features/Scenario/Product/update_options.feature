# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-options
@reset-database-before-feature
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
    Then product "product1" should have following options:
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
    And product product1 should have no manufacturer assigned
    When I update product "product1" options with following information:
      | visibility          | catalog           |
      | available_for_order | false             |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
      | manufacturer        | studioDesign      |
    Then product "product1" should have following options:
      | visibility          | catalog           |
      | available_for_order | false             |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
    And manufacturer "studioDesign" should be assigned to product product1

  Scenario: I only update product availability for order, leaving other properties unchanged
    Given product "product1" should have following options:
      | visibility          | catalog           |
      | available_for_order | false             |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
    And manufacturer "studioDesign" should be assigned to product product1
    When I update product "product1" options with following information:
      | available_for_order | true |
    Then product "product1" should have following options:
      | visibility          | catalog           |
      | available_for_order | true              |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
    And manufacturer "studioDesign" should be assigned to product product1

  Scenario: I update manufacturer with invalid values
    Given manufacturer "studioDesign" should be assigned to product product1
    When I update product "product1" options with following information:
      | manufacturer | invalid |
    Then I should get error that assigned manufacturer is invalid
    When I update product "product1" options with following information:
      | manufacturer | non-existent |
    Then I should get error that assigned manufacturer does not exist

  Scenario: I update manufacturer and check the relationship is updated correctly
    Given manufacturer "studioDesign" should be assigned to product product1
    When I update product "product1" options with following information:
      | manufacturer | graphicCorner |
    Then manufacturer "graphicCorner" should be assigned to product product1
    When I update product "product1" options with following information:
      | manufacturer |  |
    Then product product1 should have no manufacturer assigned

  Scenario: I update product options providing invalid values
    Given I add product "product2" with following information:
      | name[en-US] | 'The truth is out there' wallpaper |
      | is_virtual  | true                               |
    And product "product2" should have following options:
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
    When I update product "product2" options with following information:
      | visibility | show it to me plz |
    Then I should get error that product visibility is invalid
    When I update product "product2" options with following information:
      | condition | very good condition |
    Then I should get error that product condition is invalid
    And product "product2" should have following options:
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
