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
    #@todo: finish up implementing
