# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags update_alias_feature
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@update_alias_feature

Feature: Edit alias from Back Office (BO)
  As a BO user
  I need to be able to edit alias from the BO

  Scenario: change alias status
    Given following aliases should exist:
      | id reference | alias  | search | active |
      | alias1       | bloose | blouse | 1      |
      | alias2       | blues  | blouse | 1      |
    When I update alias "alias1" with following values:
      | aliases | dark  |
      | search  | black |
      | active  | true  |
    Then following aliases should exist:
      | id reference | alias  | search | active |
      | alias1       | bloose | blouse | 0      |
      | alias2       | blues  | blouse | 0      |
