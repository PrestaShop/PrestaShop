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
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
    When I update alias "alias1" with following values:
      | aliases | bluse  |
      | search  | blouse |
    Then following aliases should exist:
      | id reference | alias | search |
      | alias3       | bluse | blouse |
    When I update alias "alias3" with following values:
      | aliases | bluse,dress |
      | search  | blouse      |
    Then following aliases should exist:
      | id reference | alias | search |
      | alias4       | bluse | blouse |
      | alias5       | dress | blouse |

  Scenario: I edit existing alias with different search term that does not exist
    Given following aliases should exist:
      | id reference | alias | search |
      | alias4       | bluse | blouse |
      | alias5       | dress | blouse |
    When I update alias "alias4" with following values:
      | aliases | black |
      | search  | dark  |
    Then following aliases should exist:
      | id reference | alias | search |
      | alias6       | black | dark   |

  Scenario: I edit existing alias with different search term that does exist
    Given I add alias with following information:
      | alias  | dress  |
      | search | blouse |
    And following aliases should exist:
      | id reference | alias | search |
      | alias6       | black | dark   |
      | alias7       | dress | blouse |
    When I update alias "alias6" with following values:
      | aliases | bluse  |
      | search  | blouse |
    Then following aliases should exist:
      | id reference | alias | search |
      | alias7       | dress | blouse |
      | alias8       | bluse | blouse |
    Given I add alias with following information:
      | alias  | white  |
      | search | bright |
    Then following aliases should exist:
      | id reference | alias | search |
      | alias7       | dress | blouse |
      | alias8       | bluse | blouse |
      | alias9       | white | bright |
    When I update alias "alias7" with following values:
      | aliases | cyan,yellow |
      | search  | bright      |
    Then following aliases should exist:
      | id reference | alias  | search |
      | alias9       | white  | bright |
      | alias10      | cyan   | bright |
      | alias11      | yellow | bright |
