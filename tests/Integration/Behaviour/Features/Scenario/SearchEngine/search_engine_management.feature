#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s manufacturer
@restore-all-tables-before-feature
Feature: Search engine management
  As an employee
  I must be able to add, edit and delete search engines

  Scenario: Adding new search engine
    When I add a new search engine "super-secret-search" with following properties:
      | server   | secret |
      | queryKey | qu     |
    Then the search engine "super-secret-search" server value should be "secret"
    And the search engine "super-secret-search" query key value should be "qu"

  Scenario: Editing search engine
    When I edit the search engine "super-secret-search" with following properties:
      | server   | super |
      | queryKey | query |
    Then the search engine "super-secret-search" server value should be "super"
    And the search engine "super-secret-search" query key value should be "query"

  Scenario: Deleting search engine
    When I delete the search engine "super-secret-search"
    Then the search engine "super-secret-search" should be deleted

  Scenario: Deleting search engines in bulk action
    When I add a new search engine "what" with following properties:
      | server   | what |
      | queryKey | abc  |
    And I add a new search engine "who" with following properties:
      | server   | who  |
      | queryKey | text |
    When I delete search engines: "what, who" using bulk action.
    Then search engines: "what, who" should be deleted.
