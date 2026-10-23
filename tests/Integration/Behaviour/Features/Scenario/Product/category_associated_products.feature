# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags category-associated-products
@restore-products-before-feature
@clear-cache-before-feature
@category-associated-products
Feature: List the products associated with a category from Back Office (BO).
  As an employee I want to see which products a category holds, the ones counted in the categories list.

  Background:
    Given category "home" in default language named "Home" exists
    And category "clothes" in default language named "Clothes" exists
    And category "men" in default language named "Men" exists

  Scenario: A category lists only its own products, in their position, with their reference, name and status
    Then category "clothes" should be associated with following products:
      | reference | name                        | active |
      | demo_1    | Hummingbird printed t-shirt | true   |
      | demo_3    | Hummingbird printed sweater | true   |
    And category "men" should be associated with following products:
      | reference | name                        | active |
      | demo_1    | Hummingbird printed t-shirt | true   |
    When I add product "product1" with following information:
      | name[en-US] | linen shirt |
      | type        | standard    |
    And I update product "product1" with following values:
      | reference | SHIRT-01 |
    And I assign product product1 to following categories:
      | categories       | [home, men] |
      | default category | men         |
    Then category "men" should be associated with following products:
      | reference | name                        | active |
      | demo_1    | Hummingbird printed t-shirt | true   |
      | SHIRT-01  | linen shirt                 | false  |
    # Products of a subcategory are not listed in its parent
    And category "clothes" should be associated with following products:
      | reference | name                        | active |
      | demo_1    | Hummingbird printed t-shirt | true   |
      | demo_3    | Hummingbird printed sweater | true   |
    # The products are listed page by page and can be searched by ID, or a part of their reference or name
    And listing 1 products from offset 1 of category "men" should give 2 in total and:
      | reference | name        | active |
      | SHIRT-01  | linen shirt | false  |
    And the products of category "men" matching "linen" should be:
      | reference | name        | active |
      | SHIRT-01  | linen shirt | false  |
    And the products of category "clothes" matching "sweater" should be:
      | reference | name                        | active |
      | demo_3    | Hummingbird printed sweater | true   |
    When I assign product product1 to following categories:
      | categories       | [home] |
      | default category | home   |
    Then category "men" should be associated with following products:
      | reference | name                        | active |
      | demo_1    | Hummingbird printed t-shirt | true   |
