# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-seo
@reset-database-before-feature
@clear-cache-before-feature
@update-seo
Feature: Update product SEO options from Back Office (BO)
  As an employee
  I need to be able to update product SEO options from Back Office

  Background:
    Given language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists
    Given I add product "product1" with following information:
      | name[en-US] | just boots |
      | type        | standard   |
    And product product1 should have following seo options:
      | redirect_type | 404 |
    And product product1 should not have a redirect target
    And product "product1" localized "meta_title" is:
      | locale | value |
      | en-US  |       |
    And product "product1" localized "meta_description" is:
      | locale | value |
      | en-US  |       |
    And product "product1" localized "link_rewrite" is:
      | locale | value |
      | en-US  |       |
    And I add product "product2" with following information:
      | name[en-US] | magical boots |
      | type        | virtual       |
    And product product2 should have following seo options:
      | redirect_type | 404 |
    And product product2 should not have a redirect target
    And product "product1" localized "meta_title" is:
      | locale | value |
      | en-US  |       |
    And product "product1" localized "meta_description" is:
      | locale | value |
      | en-US  |       |
    And product "product1" localized "link_rewrite" is:
      | locale | value |
      | en-US  |       |

  Scenario: I update product SEO
    When I update product product2 SEO information with following values:
      | meta_title[en-US]       | product2 meta title       |
      | meta_description[en-US] | product2 meta description |
      | link_rewrite[en-US]     | waterproof-boots          |
      | redirect_type           | 301-product               |
      | redirect_target         | product1                  |
    Then product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |
    And product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |

  Scenario: Update product redirect type without providing redirect target
    Given I update product product2 SEO information with following values:
      | meta_title[en-US]       | product2 meta title       |
      | meta_description[en-US] | product2 meta description |
      | link_rewrite[en-US]     | waterproof-boots          |
      | redirect_type           | 301-product               |
      | redirect_target         | product1                  |
    Then product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |
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
    And product product2 should have following seo options:
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
    Then product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |

  Scenario: I update product seo information providing invalid redirect type
    When I update product product2 SEO information with following values:
      | meta_title[en-US]       | product2 meta title       |
      | meta_description[en-US] | product2 meta description |
      | link_rewrite[en-US]     | waterproof-boots          |
    Then product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |
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
    Then product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |

  Scenario: I update product seo redirect type providing valid redirect target
    When I update product product2 SEO information with following values:
      | meta_title[en-US]       | product2 meta title       |
      | meta_description[en-US] | product2 meta description |
      | link_rewrite[en-US]     | waterproof-boots          |
    Then product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |
    When I update product product2 SEO information with following values:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    And product product2 should have following seo options:
      | redirect_type   | 301-product |
      | redirect_target | product1    |
    When I update product product2 SEO information with following values:
      | redirect_type   | 302-product |
      | redirect_target | product1    |
    And product product2 should have following seo options:
      | redirect_type   | 302-product |
      | redirect_target | product1    |
    When I update product product2 SEO information with following values:
      | redirect_type   | 301-category |
      | redirect_target | men          |
    And product product2 should have following seo options:
      | redirect_type   | 301-category |
      | redirect_target | men          |
    When I update product product2 SEO information with following values:
      | redirect_type   | 302-category |
      | redirect_target | clothes      |
    And product product2 should have following seo options:
      | redirect_type   | 302-category |
      | redirect_target | clothes      |
    And product "product2" localized "meta_title" should be:
      | locale | value               |
      | en-US  | product2 meta title |
      | fr-FR  |                     |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |

  Scenario: Update product SEO multi-lang fields providing only default language values
    Given product "product2" localized "meta_title" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product2" localized "meta_description" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product2" localized "link_rewrite" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    When I update product product2 SEO information with following values:
      | meta_title[en-US]       | metatitl prod2            |
      | meta_description[en-US] | product2 meta description |
      | link_rewrite[en-US]     | waterproof-boots          |
    Then product "product2" localized "meta_title" should be:
      | locale | value          |
      | en-US  | metatitl prod2 |
      | fr-FR  |                |
    And product "product2" localized "meta_description" should be:
      | locale | value                     |
      | en-US  | product2 meta description |
      | fr-FR  |                           |
    And product "product2" localized "link_rewrite" should be:
      | locale | value            |
      | en-US  | waterproof-boots |
      | fr-FR  |                  |

  Scenario: Update product SEO multi-lang fields not providing default language values
    Given product "product2" localized "meta_title" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product2" localized "meta_description" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product2" localized "link_rewrite" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    When I update product product2 SEO information with following values:
      | meta_title[fr-FR]       | la meta title1 |
      | meta_description[fr-FR] | la meta desc1  |
      | link_rewrite[fr-FR]     | la-boots       |
    Then product "product2" localized "meta_title" should be:
      | locale | value          |
      | en-US  |                |
      | fr-FR  | la meta title1 |
    And product "product2" localized "meta_description" should be:
      | locale | value         |
      | en-US  |               |
      | fr-FR  | la meta desc1 |
    And product "product2" localized "link_rewrite" should be:
      | locale | value    |
      | en-US  |          |
      | fr-FR  | la-boots |

  Scenario: Update product SEO multi-lang fields with invalid values
    Given I update product product1 SEO information with following values:
      | meta_title[fr-FR]       | la meta title1 |
      | meta_description[fr-FR] | la meta desc1  |
      | link_rewrite[fr-FR]     | la-boots       |
    And product "product1" localized "meta_title" should be:
      | locale | value          |
      | en-US  |                |
      | fr-FR  | la meta title1 |
    And product "product1" localized "meta_description" should be:
      | locale | value         |
      | en-US  |               |
      | fr-FR  | la meta desc1 |
    And product "product1" localized "link_rewrite" should be:
      | locale | value    |
      | en-US  |          |
      | fr-FR  | la-boots |
    When I update product product1 SEO information with following values:
      | meta_title[en-US] | #{ |
    Then I should get error that product meta_title is invalid
    When I update product product1 SEO information with following values:
      | meta_description[en-US] | #{ |
    Then I should get error that product meta_description is invalid
    When I update product product1 SEO information with following values:
      | link_rewrite[en-US] | #{&_ |
    Then I should get error that product link_rewrite is invalid
    When I update product product1 localized SEO field meta_title with a value of 256 symbols length
    Then I should get error that product meta_title is invalid
    When I update product product1 localized SEO field meta_description with a value of 513 symbols length
    Then I should get error that product meta_description is invalid
    When I update product product1 localized SEO field link_rewrite with a value of 256 symbols length
    Then I should get error that product link_rewrite is invalid
    And product "product1" localized "meta_title" should be:
      | locale | value          |
      | en-US  |                |
      | fr-FR  | la meta title1 |
    And product "product1" localized "meta_description" should be:
      | locale | value         |
      | en-US  |               |
      | fr-FR  | la meta desc1 |
    And product "product1" localized "link_rewrite" should be:
      | locale | value    |
      | en-US  |          |
      | fr-FR  | la-boots |
