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

  Scenario: I bulk delete aliases
    Given following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
    When I add alias with following information:
      | alias   | large |
      | search  | big   |
    And I add alias with following information:
      | alias   | huge |
      | search  | big   |
    Then following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
      | alias3       | large  | big    |
      | alias4       | huge   | big    |
    And I bulk delete aliases "alias3,alias4"
    Given following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
