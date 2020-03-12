# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@reset-database-before-feature
Feature: Language

  Scenario: Add selected languages
    When I select language "Lietuvių kalba (Lithuanian)" and press add button
    Then I should be able to modify "Lietuvių kalba (Lithuanian)" translations

