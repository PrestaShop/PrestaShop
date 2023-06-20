# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags search-alias-for-association
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@search-alias-for-association
Feature: Search alias search terms to associate them in the BO
  As a BO user
  I need to be able to search for alias search terms in the BO to associate them

  Scenario: I disable multiple aliases
    Given I add alias with following information:
      | alias  | large |
      | search | big   |
    And I add alias with following information:
      | alias  | bloom   |
      | search | blossom |
    And following aliases should exist:
      | id reference | alias  | search  |
      | alias1       | bloose | blouse  |
      | alias2       | blues  | blouse  |
      | alias3       | large  | big     |
      | alias4       | bloom  | blossom |
    When I search for alias search term matching "blou" I should get the following results:
      | searchTerm |
      | blouse     |
    When I search for alias search term matching "blouse" I should get the following results:
      | searchTerm |
      | blouse     |
    When I search for alias search term matching "big" I should get the following results:
      | searchTerm |
      | big        |
    When I search for alias search term matching "blo" I should get the following results:
      | searchTerm     |
      | blossom,blouse |
