# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-seo
@reset-database-before-feature
@clear-cache-before-feature
@update-seo
Feature: Update product SEO options from Back Office (BO)
  As an employee
  I need to be able to update product SEO options from Back Office

  Scenario: I update product SEO
    Given I add product "product1" with following information:
      | name       | en-US: just boots |
      | is_virtual | false             |
    Given I add product "product2" with following information:
      | name       | en-US: just boots |
      | is_virtual | false             |
    And product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    And product "product2" localized "meta_title" is "en-US:"
    And product "product2" localized "meta_description" is "en-US:"
    And product "product2" localized "link_rewrite" is "en-US:"
    When I update product product2 SEO information with following values:
      | meta_title       | en-US:product2 meta title       |
      | meta_description | en-US:product2 meta description |
      | link_rewrite     | en-US:waterproof-boots          |
      | redirect_type    | 301-product                     |
      | redirect_target  | product1                        |
    Then product "product2" localized "meta_title" should be "en-US:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots"
    And product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |

  Scenario: Update product redirect type without providing redirect target
    Given product "product2" localized "meta_title" should be "en-US:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots"
    And product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    When I update product product2 SEO information with following values:
      | redirect_type   | 302-product |
      | redirect_target |             |
    Then I should get error that product redirect_target is invalid
    And product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    When I update product product2 SEO information with following values:
      | redirect_type   | 301-category |
      | redirect_target |              |
    Then product product2 should have following seo options:
      | redirect_type | 301-category |
    And product product2 should not have a redirect target
    When I update product product2 SEO information with following values:
      | redirect_type   | 302-category |
      | redirect_target |              |
    Then product product2 should have following seo options:
      | redirect_type | 302-category |
    And product product2 should not have a redirect target
    When I update product product2 SEO information with following values:
      | redirect_type   | 404 |
      | redirect_target |     |
    Then product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    And product "product2" localized "meta_title" should be "en-US:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots"

  Scenario: I update product seo information providing invalid redirect type
    And category "men" in default language named "Men" exists
    And product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    When I update product product2 SEO information with following values:
      | redirect_type   | 303-men-category |
      | redirect_target | men              |
    Then I should get error that product redirect_type is invalid
    And product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    When I update product product2 SEO information with following values:
      | redirect_type   | 303-product1 |
      | redirect_target | product1     |
    Then I should get error that product redirect_type is invalid
    And product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    And product "product2" localized "meta_title" should be "en-US:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots"

  Scenario: I update product seo redirect type providing valid redirect target
    Given category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    And product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    And product "product2" localized "meta_title" should be "en-US:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots"
    When I update product product2 SEO information with following values:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    Then product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    When I update product product2 SEO information with following values:
      | redirect_type   | 302-product |
      | redirect_target | product1    |
    Then product product2 should have following seo options:
      | redirect_type   | 302-product |
      | redirect_target | product1    |
    When I update product product2 SEO information with following values:
      | redirect_type   | 301-category |
      | redirect_target | men          |
    Then product product2 should have following seo options:
      | redirect_type   | 301-category |
      | redirect_target | men          |
    When I update product product2 SEO information with following values:
      | redirect_type   | 302-category |
      | redirect_target | clothes      |
    Then product product2 should have following seo options:
      | redirect_type   | 302-category |
      | redirect_target | clothes      |
    And product "product2" localized "meta_title" should be "en-US:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots"

  Scenario: Update product SEO multi-lang fields
    Given language "french" with locale "fr-FR" exists
    And product "product2" localized "meta_title" should be "en-US:product2 meta title;fr-FR:product2 meta title"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description;fr-FR:product2 meta description"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots;fr-FR:waterproof-boots"
    When I update product product2 SEO information with following values:
      | meta_title       | en-US:metatitl prod1;fr-FR:toolazytofindFRtrans meta title1           |
      | meta_description | en-US:product2 meta description;fr-FR:toolazytofindFRtrans meta desc1 |
      | link_rewrite     | en-US:waterproof-boots;fr-FR:toolazytofindFRtrans-link-rewr           |
    Then product "product2" localized "meta_title" should be "en-US:metatitl prod1;fr-FR:toolazytofindFRtrans meta title1"
    Then product "product2" localized "meta_description" should be "en-US:product2 meta description;fr-FR:toolazytofindFRtrans meta desc1"
    Then product "product2" localized "link_rewrite" should be "en-US:waterproof-boots;fr-FR:toolazytofindFRtrans-link-rewr"

  Scenario: Update product SEO multi-lang fields with invalid values
    Given product "product2" localized "meta_title" should be "en-US:metatitl prod1;fr-FR:toolazytofindFRtrans meta title1"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description;fr-FR:toolazytofindFRtrans meta desc1"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots;fr-FR:toolazytofindFRtrans-link-rewr"
    When I update product product2 SEO information with following values:
      | meta_title | en-US:#{ |
    Then I should get error that product meta_title is invalid
    When I update product product2 SEO information with following values:
      | meta_description | en-US:#{ |
    Then I should get error that product meta_description is invalid
    When I update product product2 SEO information with following values:
      | link_rewrite | en-US:#{&_ |
    Then I should get error that product link_rewrite is invalid
    When I update product product2 localized SEO field meta_title with a value of 256 symbols length
    Then I should get error that product meta_title is invalid
    When I update product product2 localized SEO field meta_description with a value of 513 symbols length
    Then I should get error that product meta_description is invalid
    When I update product product2 localized SEO field link_rewrite with a value of 256 symbols length
    Then I should get error that product link_rewrite is invalid
    And product "product2" localized "meta_title" should be "en-US:metatitl prod1;fr-FR:toolazytofindFRtrans meta title1"
    And product "product2" localized "meta_description" should be "en-US:product2 meta description;fr-FR:toolazytofindFRtrans meta desc1"
    And product "product2" localized "link_rewrite" should be "en-US:waterproof-boots;fr-FR:toolazytofindFRtrans-link-rewr"

  Scenario: Search product for seo redirect option by name
    Given I add product product3 with following information:
      | name       | en-US:nice boots |
      | is_virtual | false            |
    And I update product product3 options with following information:
      | reference | 1234567 |
    And I add product product4 with following information:
      | name       | en-US:nice pants |
      | is_virtual | false            |
    And I update product product4 options with following information:
      | reference | 1334567 |
    And I add product product5 with following information:
      | name       | en-US:nice hat |
      | is_virtual | false          |
    And I update product product5 options with following information:
      | reference | 1344567 |
    And I add product product6 with following information:
      | name       | en-US:nice scarf |
      | is_virtual | false            |
    And I update product product6 options with following information:
      | reference | 1344567 |
    And I add product product7 with following information:
      | name       | en-US:nice glasses |
      | is_virtual | false              |
    And I update product product7 options with following information:
      | reference | 1345567 |
    When I search products for SEO redirect option in "en" language by phrase "nic" and limit 10
    Then search results for product SEO redirect option should be the following:
      | product  | name         | reference |
      | product3 | nice boots   | 1234567   |
      | product4 | nice pants   | 1334567   |
      | product5 | nice hat     | 1344567   |
      | product6 | nice scarf   | 1344567   |
      | product7 | nice glasses | 1345567   |
    When I search products for SEO redirect option in "en" language by phrase "nic" and limit 10
    When I search products for SEO redirect option in "en" language by phrase "nic" and limit 3
    Then search results for product SEO redirect option should be the following:
      | product  | name       | reference |
      | product3 | nice boots | 1234567   |
      | product4 | nice pants | 1334567   |
      | product5 | nice hat   | 1344567   |

  Scenario: I search product for seo redirect option by reference
    Given product "product3" localized "name" should be "en-US:nice boots"
    And product product3 should have following options information:
      | reference | 1234567 |
    And product "product4" localized "name" should be "en-US:nice pants"
    And product product4 should have following options information:
      | reference | 1334567 |
    And product "product5" localized "name" should be "en-US:nice hat"
    And product product5 should have following options information:
      | reference | 1344567 |
    And product "product6" localized "name" should be "en-US:nice scarf"
    And product product6 should have following options information:
      | reference | 1344567 |
    And product "product7" localized "name" should be "en-US:nice glasses"
    And product product7 should have following options information:
      | reference | 1345567 |
    When I search products for SEO redirect option in "en" language by phrase "1344" and limit 10
    Then search results for product SEO redirect option should be the following:
      | product  | name       | reference |
      | product5 | nice hat   | 1344567   |
      | product6 | nice scarf | 1344567   |
