# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-status
@reset-database-before-feature
@clear-cache-before-feature
@update-status
Feature: Update product status from BO (Back Office)
  As an employee I must be able to update product status (enable/disable)

  Scenario: I update standard product status
    Given I add product "product1" with following information:
      | name       | en-US:Values list poster nr. 1 (paper) |
      | is_virtual | false                                  |
    And product product1 type should be standard
    And product "product1" should be disabled
    When I enable product "product1"
    And product "product1" should be enabled
    When I disable product "product1"
    And product "product1" should be disabled

  Scenario: I update virtual product status
    And I add product "product2" with following information:
      | name       | en-US:Values list poster nr. 2 (virtual) |
      | is_virtual | true                                     |
    And product product2 type should be virtual
    And product "product2" should be disabled
    When I enable product "product2"
    And product "product2" should be enabled
    When I disable product "product2"
    And product "product2" should be disabled

  Scenario: I update combination product status
    And I add product "product3" with following information:
      | name       | en-US:T-Shirt with listed values |
      | is_virtual | false                            |
    And product "product3" has following combinations:
      | reference | quantity | attributes         |
      | whiteS    | 100      | Size:S;Color:White |
      | whiteM    | 150      | Size:M;Color:White |
      | blackM    | 130      | Size:M;Color:Black |
    And product product3 type should be combination
    And product "product3" should be disabled
    When I enable product "product3"
    And product "product3" should be enabled
    When I disable product "product3"
    Then product "product3" should be disabled

  Scenario: I disable product which is already disabled
    And product "product1" should be disabled
    When I disable product "product1"
    And product "product1" should be disabled

  Scenario: I enable product which is already enabled
    And product "product1" should be disabled
    And I enable product "product1"
    And product "product1" should be enabled
    When I enable product "product1"
    Then product "product1" should be enabled

# toggling product status will reset product redirect options. Check Product::toggleStatus()
# enabling product will reset product redirect target to 0 and redirect type to 404.
  Scenario: I enable product which has redirect options set
    Given I add product "product4" with following information:
      | name       | en-US:Values list poster nr. 3 (standard) |
      | is_virtual | false                                     |
    And product "product4" should be disabled
    And I update product product4 SEO information with following values:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    And product product4 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    When I enable product "product4"
    And product "product4" should be enabled
    And product product4 should have following seo options:
      | redirect_type | 404 |

# disabling product will reset product redirect target to 0 and redirect type to 301-category
  Scenario: I disable product which has redirect options set
    And category "men" in default language named "Men" exists
    Given I add product "product5" with following information:
      | name       | en-US:Man jeans |
      | is_virtual | false           |
    And product "product5" should be disabled
    And I enable product "product5"
    And product "product5" should be enabled
    And I update product product5 SEO information with following values:
      | redirect_type   | 302-category |
      | redirect_target | men          |
    And product product5 should have following seo options:
      | redirect_type   | 302-category |
      | redirect_target | men          |
    When I disable product "product5"
    Then product "product5" should be disabled
    And product product5 should have following seo options:
      | redirect_type | 301-category |

