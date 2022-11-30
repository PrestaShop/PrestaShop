# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-options
@restore-products-before-feature
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
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    When I update product "product1" with following values:
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should not be indexed

  Scenario: I only update product availability for order, leaving other properties unchanged
    Given product "product1" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I update product "product1" with following values:
      | available_for_order | true |
    # show_price is automatically set to true
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | true         |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should not be indexed

  Scenario: I update manufacturer and check the relationship is updated correctly
    Given product "product1" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | true         |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I update product "product1" with following values:
      | manufacturer | graphicCorner |
    Then product "product1" should have following options:
      | product option      | value         |
      | visibility          | catalog       |
      | available_for_order | true          |
      | online_only         | true          |
      | show_price          | true          |
      | condition           | used          |
      | show_condition      | true          |
      | manufacturer        | graphicCorner |
    When I update product "product1" with following values:
      | manufacturer |  |
    Then product "product1" should have following options:
      | product option      | value   |
      | visibility          | catalog |
      | available_for_order | true    |
      | online_only         | true    |
      | show_price          | true    |
      | condition           | used    |
      | show_condition      | true    |
      | manufacturer        |         |
    And product "product1" should not be indexed

  Scenario: I update product options providing invalid values
    Given I add product "product2" with following information:
      | name[en-US] | 'The truth is out there' wallpaper |
      | type        | virtual                            |
    And product "product2" should have following options:
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    When I assign non existing manufacturer to product "product2"
    Then I should get error that manufacturer does not exist
    When I update product "product2" with following values:
      | visibility | show it to me plz |
    Then I should get error that product visibility is invalid
    When I update product "product2" with following values:
      | condition | very good condition |
    Then I should get error that product condition is invalid
    And product "product2" should have following options:
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    And product "product1" should not be indexed

  Scenario: I update a product's options for a product that should be indexed
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following options:
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    When I update product "product1" with following values:
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I enable product "product1"
    Then product "product1" should be enabled
    And product "product1" should have following options:
      | product option      | value        |
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should be indexed

  Scenario: I update a product's options for an indexable product when indexation feature is disabled
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following options:
      | product option      | value |
      | visibility          | both  |
      | available_for_order | true  |
      | online_only         | false |
      | show_price          | true  |
      | condition           | new   |
      | show_condition      | false |
      | manufacturer        |       |
    And search indexation feature is disabled
    When I update product "product1" with following values:
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should not be indexed

  Scenario: Product indexation depends on its visibility and status
    Given product "product1" should have following options:
      | product option      | value        |
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should be disabled
    And product "product1" should not be indexed
    When I update product "product1" with following values:
      | visibility | search |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | search       |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should be disabled
    And product "product1" should not be indexed
    When I enable product "product1"
    Then product "product1" should be enabled
    And product "product1" should be indexed
    When I update product "product1" with following values:
      | visibility | catalog |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | catalog      |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should be enabled
    And product "product1" should not be indexed
    When I update product "product1" with following values:
      | visibility | both |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | both         |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    And product "product1" should be enabled
    And product "product1" should be indexed
    When I disable product "product1"
    Then product "product1" should be disabled
    And product "product1" should not be indexed

  Scenario: Price should always be shown when product is available for ordering
    # Based on previous changes in previous scenarios we already know that order can be disabled and prices hidden
    Given product "product1" should have following options:
      | product option      | value        |
      | visibility          | both         |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | false        |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I update product "product1" with following values:
      | show_price | true |
    # We can show price when product is not available for order
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | both         |
      | available_for_order | false        |
      | online_only         | true         |
      | show_price          | true         |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    # Even if we try forcing the show price to false it will be true as long as product is available for order
    When I update product "product1" with following values:
      | available_for_order | true  |
      | show_price          | false |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | both         |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | true         |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
    When I update product "product1" with following values:
      | show_price          | false  |
    Then product "product1" should have following options:
      | product option      | value        |
      | visibility          | both         |
      | available_for_order | true         |
      | online_only         | true         |
      | show_price          | true         |
      | condition           | used         |
      | show_condition      | true         |
      | manufacturer        | studioDesign |
