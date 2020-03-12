# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@reset-database-before-feature
Feature: Language

  Scenario: Add new language
    When I add new language "Esperanto" with following details:
      | Name               | Esperanto   |
      | ISO code           | esp         |
      | Language code      | esp-LT      |
      | Date format        | esp-LT      |
      | Date format (full) | Y-m-d H:i:s |
      | Flag               | lt-flag.jpg |
      | "No-picture" image | no-pict.jpg |
      | Is RTL language    | false       |
      | Status             | true        |
    Then I should be able to see "Esperanto" language edit form with following details:
      | Name               | Esperanto   |
      | ISO code           | esp         |
      | Language code      | esp-LT      |
      | Date format        | esp-LT      |
      | Date format (full) | Y-m-d H:i:s |
      | Flag               | lt-flag.jpg |
      | "No-picture" image | no-pict.jpg |
      | Is RTL language    | false       |
      | Status             | true        |

