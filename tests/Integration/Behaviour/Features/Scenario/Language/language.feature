# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s language
@reset-database-before-feature
Feature: Language

  Scenario: Add new language
    When I add new language "Esperanto" with following details:
      | Name               | Esperanto          |
      | ISO code           | es                 |
      | Language code      | en-US              |
      | Date format        | Y-m-d              |
      | Date format (full) | Y-m-d H:i:s        |
      | Flag               | flagImgPath.jpg    |
      | "No-picture" image | no-pictImgPath.jpg |
      | Is RTL language    | false              |
      | Status             | true               |
    Then I should be able to see "Esperanto" language edit form with following details:
      | Name               | Esperanto          |
      | ISO code           | es                 |
      | Language code      | en-US              |
      | Date format        | Y-m-d              |
      | Date format (full) | Y-m-d H:i:s        |
      | Flag               | flagImgPath.jpg    |
      | "No-picture" image | no-pictImgPath.jpg |
      | Is RTL language    | false              |
      | Status             | true               |

