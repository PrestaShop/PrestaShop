# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags search-alias-for-association
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@search-alias-for-association
Feature: Search alias search terms to associate them in the BO
  As a BO user
  I need to be able to search for alias search terms in the BO to associate them

  Scenario: I disable multiple aliases
    Given I add a search term "blouse" with following aliases:
      | alias  | active |
      | bloose | true   |
      | blues  | true   |
    And I add a search term "big" with following aliases:
      | alias  | active |
      | large  | true   |
    And I add a search term "blossom" with following aliases:
      | alias  | active |
      | bloom  | true   |
      | blooom | false  |
    And following aliases should exist:
      | search  | alias  | active |
      | blouse  | bloose | true   |
      | blouse  | blues  | true   |
      | big     | large  | true   |
      | blossom | bloom  | true   |
      | blossom | blooom | false  |
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
