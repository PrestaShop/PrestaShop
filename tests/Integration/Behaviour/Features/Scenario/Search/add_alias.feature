# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags add
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@add
Feature: Add basic alias from Back Office (BO)
  As a BO user
  I need to be able to add new alias with basic information from the BO

  Scenario: I add multiple aliases for one search term with basic information
    Given following aliases should exist:
      | search | alias  | active |
      | blouse | bloose | true |
      | blouse | blues  | true |
    When I add a search term "big" with following aliases:
      | alias | active |
      | large | true   |
      | small | false  |
    Then following aliases should exist:
      | search | alias  | active  |
      | blouse | bloose | true    |
      | blouse | blues  | true    |
      | big    | large  | true    |
      | big    | small  | false   |
    Then I should have the following aliases for search term "big":
      | alias | active |
      | large | true   |
      | small | false  |

  Scenario: I add aliases for one search term for the second time
    Given following aliases should exist:
      | search | alias  | active |
      | big    | large  | true   |
      | big    | small  | false  |
    When I add a search term "big" with following aliases:
      | alias     | active |
      | so biig   | true   |
    Then following aliases should exist:
      | search  | alias   | active |
      | big     | large   | true   |
      | big     | small   | false  |
      | big     | so biig | true   |
    Then I should have the following aliases for search term "big":
      | alias   | active |
      | large   | true   |
      | small   | false  |
      | so biig | true   |

  Scenario: I add aliases for one search term for the second time with different active status
    Given following aliases should exist:
      | search | alias   | active |
      | big    | large   | true   |
      | big    | small   | false  |
      | big    | so biig | true   |
    When I add a search term "big" with following aliases:
      | alias     | active |
      | so biig   | false  |
    Then I should have the following aliases for search term "big":
      | alias   | active |
      | large   | true   |
      | small   | false  |
      | so biig | false  |

  Scenario: I add alias already used by another search term
    Given following aliases should exist:
      | search | alias  | active |
      | big    | large  | true   |
    When I add a search term "geant" with following aliases:
      | alias  | active |
      | large  | true   |
    Then I should get error that alias is already used by another search term

  Scenario: I add alias with empty alias field
    When I add a search term "noalias" with following aliases:
      | alias | active |
    Then I should get error that alias cannot be empty

  Scenario: I add alias with empty search field
    When I add a search term "" with following aliases:
      | alias    | active |
      | new_alias | true |
    Then I should get error that search term cannot be empty
