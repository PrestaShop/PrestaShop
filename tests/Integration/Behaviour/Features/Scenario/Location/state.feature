@reset-database-before-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s location --name='Manage locations states'
Feature: Manage locations states
  As an employee
  I must be able to add, edit states

  Scenario: Adding new state
    When I add new state with following details:
      | Name                | AA            |
      | ISO code            | AA            |
      | Country             | United States |
      | Zone                | North America |
      | Status              | true          |
    Then there is state with following details:
      | Name                | AA            |
      | ISO code            | AA            |
      | Country             | United States |
      | Zone                | North America |
      | Status              | true          |

