# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-seo
@reset-database-before-feature
@update-seo
Feature: Update product SEO options from Back Office (BO)
  As an employee
  I need to be able to update product SEO options from Back Office

  Scenario: I update product SEO
    Given I add product "product1" with following information:
      | name       | en-US: waterproof boots   |
      | is_virtual | false                     |
    And product product1 should have following values:
      | redirect_type    | 404                 |
    And product product1 should not have a redirect target
    And product "product1" localized "meta_title" is "en-US:"
    And product "product1" localized "meta_description" is "en-US:"
    And product "product1" localized "link_rewrite" is "en-US:"
