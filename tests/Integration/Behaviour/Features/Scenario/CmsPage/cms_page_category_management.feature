# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cms_page
@restore-all-tables-before-feature
Feature: CmsPageCategory Management
  As a BO user (or an Admin API client)
  I must be able to read the root category and get an error for an unknown parent

  Scenario: The root category can be read and has no parent
    Then the cms page category with id 1 should have no parent

  Scenario: Adding a category under an unknown parent is refused
    Given cms category with id 999999 does not exist
    When I add CMS page category "Orphan" with parent id 999999
    Then I should get an error that the cms page category was not found
