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
      | alias   | alias1  |
      | search  | alias 1 |
    Then alias "alias1" should have the following details:
      | alias   | alias1  |
      | search  | alias 1 |

  Scenario: I add a multiple aliases with basic information
    When I add alias "alias2" with following information:
      | alias  | alias2,aliases2 |
      | search | alias 2         |
    Then alias "alias2" should have the following details:
      | alias  | alias2,aliases2 |
      | search | alias 2         |

  Scenario: I add a multiple aliases with same search field and basic information
    When I add alias "alias3" with following information:
      | alias  | alias3,aliases3 |
      | search | alias 3         |
    And I add alias "alias4" with following information:
      | alias  | alias4,aliases4 |
      | search | alias 3         |
    Then alias "alias2" should have the following details:
      | alias  | alias3,aliases3,alias4,aliases4 |
      | search | alias 3                         |
