# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-seo
@reset-database-before-feature
@update-seo
Feature: Update product SEO options from Back Office (BO)
  As an employee
  I need to be able to update product SEO options from Back Office

  Scenario: I update product SEO
    Given I add product "product1" with following information:
      | name       | en-US: just boots         |
      | is_virtual | false                     |
    Given I add product "product2" with following information:
      | name       | en-US: just boots         |
      | is_virtual | false                     |
    And product product2 should have following values:
      | redirect_type    | 404                 |
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
    And product product2 should have following values:
      | redirect_type    | 301-product               |
    And product product2 redirect target should be product1
