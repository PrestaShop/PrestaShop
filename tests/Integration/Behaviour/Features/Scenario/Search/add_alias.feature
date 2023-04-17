# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags add
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@add
Feature: Add basic alias from Back Office (BO)
  As a BO user
  I need to be able to add new alias with basic information from the BO

  Scenario: I add an alias with basic information
    When I add alias "alias1" with following information:
      | alias1   | alias1  |
      | search  | alias 1 |
    Then alias "alias1" should have the following details:
      | alias   | alias1  |
      | search  | alias 1 |
      | enabled | true    |

  Scenario: I add a multiple aliases with basic information
    When I add alias "alias2" with following information:
      | alias2 | alias2,aliases2 |
      | search | alias 2         |
    Then alias "alias2" should have the following details:
      | alias2  | alias2  |
      | search  | alias 2 |
      | enabled | true    |
    And alias "alias2" should have the following details:
      | alias2  | aliases2 |
      | search  | alias 2  |
      | enabled | true     |
