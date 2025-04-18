# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags delete
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@delete
Feature: Delete aliases from Back Office (BO)
  As a BO user
  I need to be able to delete alias and multiple aliases at once from BO

  Scenario: I delete alias
    When I add a search term "big" with following aliases:
      | alias | active |
      | large | true   |
      | small | false  |
    Then following aliases should exist:
      | search | alias | active |
      | big    | large | true   |
      | big    | small | false  |
    And I delete search term "big"
    Then following aliases shouldn't exist:
      | search | alias | active |
      | big    | large | true   |
      | big    | small | false  |

  Scenario: I bulk delete aliases
    When I add a search term "toDelete1" with following aliases:
      | alias   | active |
      | delete1 | true   |
      | delete2 | true   |
    And I add a search term "toDelete2" with following aliases:
      | alias   | active |
      | delete3 | true   |
      | delete4 | true   |
    Then following aliases should exist:
      | search    | alias   | active |
      | toDelete1 | delete1 | true   |
      | toDelete1 | delete2 | true   |
      | toDelete2 | delete3 | true   |
      | toDelete2 | delete4 | true   |
    And I bulk delete search terms "toDelete1,toDelete2"
    Then following aliases shouldn't exist:
      | search    | alias   | active |
      | toDelete1 | delete1 | true   |
      | toDelete1 | delete2 | true   |
      | toDelete2 | delete3 | true   |
      | toDelete2 | delete4 | true   |
