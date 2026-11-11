# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags indexed_products_count
@restore-search-index-before-feature
@restore-search-index-after-feature
@clear-cache-before-feature
@indexed_products_count
Feature: Count the products of the search index from Back Office (BO)
  As a BO user
  I need to see how many searchable products are indexed

  Scenario: Adding the missing products indexes every searchable product
    When I mark all products as not indexed for search
    Then no searchable product should be indexed in the default shop
    When I add the missing products to the search index
    Then all searchable products should be indexed in the default shop

  Scenario: Rebuilding the index indexes every searchable product
    When I mark all products as not indexed for search
    Then no searchable product should be indexed in the default shop
    When I rebuild the search index
    Then all searchable products should be indexed in the default shop
    And all searchable products should be indexed in all shops context

  Scenario: Products of other shops are not counted
    Then no searchable product should be counted for a shop that does not exist
