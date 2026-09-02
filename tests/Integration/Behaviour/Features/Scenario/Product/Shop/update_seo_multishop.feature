# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-seo-multishop
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multishop
@update-seo-multishop
Feature: Update product SEO options from Back Office (BO)
  As a BO user
  I need to be able to update product SEO options from BO

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I update product "product1" with following values:
      | meta_title[en-US]       | magic staff meta title       |
      | meta_description[en-US] | magic staff meta description |
      | link_rewrite[en-US]     | magic-staff                  |
      | redirect_type           | 404                          |
      | redirect_target         |                              |
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product "product1" localized "meta_title" for shops "shop1,shop2" should be:
      | locale | value                  |
      | en-US  | magic staff meta title |
    And product "product1" localized "meta_description" for shops "shop1,shop2" should be:
      | locale | value                        |
      | en-US  | magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop1,shop2" should be:
      | locale | value       |
      | en-US  | magic-staff |
    And product "product1" should have following seo options for shops "shop1,shop2":
      | redirect_type   | 404         |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product SEO options for specific shop
    When I update product "product1" for shop "shop2" with following values:
      | meta_title[en-US]       | cool magic staff meta title       |
      | meta_description[en-US] | cool magic staff meta description |
      | link_rewrite[en-US]     | cool-magic-staff                  |
      | redirect_type           | 301-category                      |
      | redirect_target         |                                   |
    Then product "product1" localized "meta_title" for shops "shop1" should be:
      | locale | value                  |
      | en-US  | magic staff meta title |
    And product "product1" localized "meta_description" for shops "shop1" should be:
      | locale | value                        |
      | en-US  | magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop1" should be:
      | locale | value       |
      | en-US  | magic-staff |
    And product "product1" should have following seo options for shops "shop1":
      | redirect_type   | 404 |
    But product "product1" localized "meta_title" for shops "shop2" should be:
      | locale | value                       |
      | en-US  | cool magic staff meta title |
    And product "product1" localized "meta_description" for shops "shop2" should be:
      | locale | value                             |
      | en-US  | cool magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop2" should be:
      | locale | value            |
      | en-US  | cool-magic-staff |
    And product "product1" should have following seo options for shops "shop2":
      | redirect_type   | 301-category |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product SEO options for all associated shop
    When I update product "product1" for all shops with following values:
      | meta_title[en-US]       | cool magic staff meta title       |
      | meta_description[en-US] | cool magic staff meta description |
      | link_rewrite[en-US]     | cool-magic-staff                  |
      | redirect_type           | 301-category                      |
      | redirect_target         |                                   |
    Then product "product1" localized "meta_title" for shops "shop1,shop2" should be:
      | locale | value                       |
      | en-US  | cool magic staff meta title |
    And product "product1" localized "meta_description" for shops "shop1,shop2" should be:
      | locale | value                             |
      | en-US  | cool magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | cool-magic-staff |
    And product "product1" should have following seo options for shops "shop1,shop2":
      | redirect_type   | 301-category |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product SEO options for single shop and right after for all shops
    When I update product "product1" for shop "shop2" with following values:
      | meta_title[en-US]       | cool magic staff meta title       |
      | meta_description[en-US] | cool magic staff meta description |
      | link_rewrite[en-US]     | cool-magic-staff                  |
      | redirect_type           | 301-category                      |
      | redirect_target         |                                   |
    And I update product "product1" for all shops with following values:
      | meta_title[en-US] | weird magic staff meta title |
    Then product "product1" localized "meta_title" for shops "shop1,shop2" should be:
      | locale | value                        |
      | en-US  | weird magic staff meta title |
    And product "product1" localized "meta_description" for shops "shop1" should be:
      | locale | value                        |
      | en-US  | magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop1" should be:
      | locale | value       |
      | en-US  | magic-staff |
    And product "product1" should have following seo options for shops "shop1":
      | redirect_type   | 404 |
    And product "product1" localized "meta_description" for shops "shop2" should be:
      | locale | value                             |
      | en-US  | cool magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop2" should be:
      | locale | value            |
      | en-US  | cool-magic-staff |
    And product "product1" should have following seo options for shops "shop2":
      | redirect_type   | 301-category |
    And product product1 is not associated to shops "shop3,shop4"

  Scenario: I update product SEO options for all shops and right after for single shops
    When I update product "product1" for all shops with following values:
      | meta_title[en-US]       | cool magic staff meta title       |
      | meta_description[en-US] | cool magic staff meta description |
      | link_rewrite[en-US]     | cool-magic-staff                  |
      | redirect_type           | 301-category                      |
      | redirect_target         |                                   |
    And I update product "product1" for shop "shop2" with following values:
      | meta_title[en-US] | weird magic staff meta title |
    Then product "product1" localized "meta_title" for shops "shop1" should be:
      | locale | value                       |
      | en-US  | cool magic staff meta title |
    And product "product1" localized "meta_title" for shops "shop2" should be:
      | locale | value                        |
      | en-US  | weird magic staff meta title |
    And product "product1" localized "meta_description" for shops "shop1,shop2" should be:
      | locale | value                             |
      | en-US  | cool magic staff meta description |
    And product "product1" localized "link_rewrite" for shops "shop1,shop2" should be:
      | locale | value            |
      | en-US  | cool-magic-staff |
    And product "product1" should have following seo options for shops "shop1,shop2":
      | redirect_type   | 301-category |
    And product product1 is not associated to shop "shop3,shop4"
