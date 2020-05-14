# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags product_combination
@reset-database-before-feature
Feature: Product combination management
  As a BO user
  I must be able to generate and delete product attribute combinations

  Background:
    Given the current currency is "USD"

  @product_combination
  Scenario: Generate product combinations
    When I generate attribute combinations for product "Hummingbird printed t-shirt" with following attributes in default language:
      | Size         | [S,M,L]      |
      | Color        | [Red, White] |
      | Dimension    | [40x60]      |
    Then product "Hummingbird printed t-shirt" should contain following combinations:
      | [S,Red,40x60]   |
      | [M,Red,40x60]   |
      | [L,Red,40x60]   |
      | [S,White,40x60] |
      | [M,White,40x60] |
      | [L,White,40x60] |
