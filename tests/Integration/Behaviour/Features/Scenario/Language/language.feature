# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@reset-database-before-feature
Feature: Language

  Scenario: Add new language
    When I add new language "Esperanto" with following details:
    | name | Esperanto |
    Then I should be able to see "Esperanto" language edit from with following details:
    | name | Esperanto |

