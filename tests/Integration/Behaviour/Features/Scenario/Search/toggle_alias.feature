# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags update_alias_status_feature
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@update_alias_status_feature
Feature: Update alias status from Back Office (BO)
  As a BO user
  I need to be able to enable or disable alias from the BO

  Scenario: I disable aliases
    When I add a search term "big" with following aliases:
      | alias | active |
      | large | true   |
      | small | true   |
    Then following aliases should exist:
      | search | alias  | active |
      | big    | large  | true   |
      | big    | small  | true   |
    When I update search term "big" with following aliases:
      | alias | active |
      | large | true   |
      | small | false  |
    Then following aliases should exist:
      | search | alias  | active |
      | big    | large  | true   |
      | big    | small  | false  |

  Scenario: I enable aliases
    When I add a search term "geant" with following aliases:
      | alias | active |
      | biig  | false  |
      | megaa | false  |
    Then following aliases should exist:
      | search | alias  | active |
      | geant  | biig   | false  |
      | geant  | megaa  | false  |
    When I update search term "geant" with following aliases:
      | alias | active |
      | biig  | true   |
      | megaa | true   |
    Then following aliases should exist:
      | search | alias  | active |
      | geant  | biig   | true   |
      | geant  | megaa  | true   |

