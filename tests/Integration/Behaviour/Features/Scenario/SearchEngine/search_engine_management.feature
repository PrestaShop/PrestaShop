#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s manufacturer
@reset-database-before-feature
Feature: Search engine management
  As an employee
  I must be able to add, edit and delete search engines

  Scenario: Adding new search engine
    When I add a new search engine "super-secret-search" with following properties:
      | server | secret |
      | getvar | qu     |
    Then the search engine "super-secret-search" server value should be "secret"
    And the search engine "super-secret-search" getvar value should be "qu"

  Scenario: Editing search engine
    When I edit the search engine "super-secret-search" with following properties:
      | server | super |
      | getvar | query |
    Then the search engine "super-secret-search" server value should be "super"
    And the search engine "super-secret-search" getvar value should be "query"

  Scenario: Deleting search engine
    When I delete the search engine "super-secret-search"
    Then the search engine "super-secret-search" should be deleted

  Scenario: Deleting search engines in bulk action
    When I add a new search engine "what" with following properties:
      | server | what |
      | getvar | abc  |
    And I add a new search engine "who" with following properties:
      | server | who  |
      | getvar | text |
    When I delete search engines: "what, who" using bulk action.
    Then search engines: "what, who" should be deleted.
