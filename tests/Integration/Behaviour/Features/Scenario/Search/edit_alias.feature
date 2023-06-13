# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags update_alias_feature
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@update_alias_feature

Feature: Edit alias from Back Office (BO)
  As a BO user
  I need to be able to edit alias from the BO

  Scenario: I edit existing alias with same search term and active status
    Given following aliases should exist:
      | id reference | alias  | search | active |
      | alias1       | bloose | blouse | 1      |
      | alias2       | blues  | blouse | 1      |
    When I update alias "alias1" with following values:
      | aliases | bluse  |
      | search  | blouse |
    Then following aliases should exist:
      | id reference | alias  | search | active |
      | alias1       | bloose | blouse | 1      |
      | alias2       | blues  | blouse | 1      |
      | alias3       | bluse  | blouse | 1      |

  Scenario: I edit existing alias with different search term that does not exist
    When I update alias "alias1" with following values:
      | aliases | dark  |
      | search  | black |
    Then following aliases should exist:
      | id reference | alias | search | active |
      | alias3       | dark  | black  | 1      |

  Scenario: I edit existing alias with different search term that does exist
    When I add alias with following information:
      | alias  | large |
      | search | big   |
    Then following aliases should exist:
      | id reference | alias | search | active |
      | alias3       | dark  | black  | 1      |
      | alias4       | large | big    | 1      |
    When I update alias "alias3" with following values:
      | aliases | huge |
      | search  | big  |
    Then following aliases should exist:
      | id reference | alias | search | active |
      | alias4       | large | big    | 1      |
      | alias5       | huge  | big    | 1      |
