# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags delete
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@delete
Feature: Delete aliases from Back Office (BO)
  As a BO user
  I need to be able to delete alias and multiple aliases at once from BO

  Scenario: I delete alias
    Given following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
    When I add alias with following information:
      | alias   | large |
      | search  | big   |
    Then following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
      | alias3       | large  | big    |
    And I delete alias "alias3"
    Then following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |

#  Scenario: I bulk delete aliases
#    Given I add alias "alias1" with following information:
#      | alias   | alias1  |
#      | search  | alias 1 |
#    Given I add alias "alias2" with following information:
#      | alias   | alias2  |
#      | search  | alias 2 |
#    Given I add alias "alias3" with following information:
#      | alias   | alias3  |
#      | search  | alias 3 |
#    When I bulk delete following aliases:
#      | reference |
#      | alias1  |
#      | alias2  |
#    Then alias alias1 should not exist anymore
#    And alias alias2 should not exist anymore
#    And alias "alias3" should have the following details:
#      | alias  | alias3  |
#      | search | alias 3 |
