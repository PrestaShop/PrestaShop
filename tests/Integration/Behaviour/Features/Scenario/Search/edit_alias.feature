# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags update_alias_feature
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@update_alias_feature

Feature: Edit alias from Back Office (BO)
  As a BO user
  I need to be able to edit alias from the BO

  Scenario: I edit existing alias with same search term
    Given following aliases should exist:
      | search | alias  | active  |
      | blouse | bloose | true    |
      | blouse | blues  | true    |
    When I add a search term "big" with following aliases:
      | alias | active |
      | large | true   |
      | small | false  |
    Then I should have the following aliases for search term "big":
      | alias   | active |
      | large   | true   |
      | small   | false  |
    When I update search term "big" with following aliases:
      | alias | active |
      | large | true   |
      | biig  | true   |
    Then I should have the following aliases for search term "big":
      | alias   | active |
      | biig    | true   |
      | large   | true   |
    Then following aliases should exist:
      | search | alias  | active |
      | big    | large  | true   |
      | big    | biig   | true   |
    Then following aliases shouldn't exist:
      | search | alias  | active |
      | big    | small  | false  |

  Scenario: I edit with an alias already used by another search term
    Given following aliases should exist:
      | search | alias  | active  |
      | big    | large  | true   |
    When I add a search term "geant" with following aliases:
      | alias  | active |
      | biiig  | true   |
    Then I should have the following aliases for search term "geant":
      | alias | active |
      | biiig | true   |
    Then following aliases should exist:
      | search | alias  | active |
      | big    | large  | true   |
      | geant  | biiig  | true   |
    When I update search term "geant" with following aliases:
      | alias | active |
      | large | true   |
    Then I should get error that alias is already used by another search term

  Scenario: I edit the search term for a search term already exist.
    When I add a search term "tshirt" with following aliases:
      | alias     | active |
      | shirt     | true   |
      | tee shirt | true   |
    Then following aliases should exist:
      | search | alias     | active |
      | tshirt | shirt     | true   |
      | tshirt | tee shirt | true   |
    When I update search term "tshirt" by "t-shirt" with following aliases:
      | alias     | active |
      | shirt     | true   |
      | tee shirt | true   |
    Then following aliases should exist:
      | search  | alias     | active |
      | t-shirt | shirt     | true   |
      | t-shirt | tee shirt | true   |
    Then following aliases shouldn't exist:
      | search | alias     | active |
      | tshirt | shirt     | true   |
      | tshirt | tee shirt | true   |
