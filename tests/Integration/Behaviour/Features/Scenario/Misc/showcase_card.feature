# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s misc
@reset-database-before-feature
Feature: Showcase card
  In order to hide not relevant information in the Back Office
  As a BO user
  I should be able to close showcase card

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Employee closes showcase card in single shop context
    Given single shop context is loaded
    And I Add new employee "Tadas" to shop "shop1" with the following details:
      | First name         | Tadas                        |
      | Last name          | Davidsonas                   |
      | Email address      | tadas.davidsonas@invertus.eu |
      | Password           | secretpassword               |
      | Default page       | Dashboard                    |
      | Language           | English (English)            |
    # Allow or disallow this employee to log in to the Admin panel. true = YES; false = NO
      | Active             | true                         |
      | Permission profile | SuperAdmin                   |
    When employee "Tadas" closes showcase card "seo-urls_card"
    Then employee "Tadas" should not see showcase card "seo-urls_card"

# todo: remove after resolving the issue
# Showcase card reappear after closing it in multistore context: https://github.com/PrestaShop/PrestaShop/issues/12390
  Scenario: Employee closes showcase card in multiple shops context
    Given multiple shop context is loaded
    And I Add new employee "TadasTwo" to shop "shop1" with the following details:
      | First name         | TadasTwo                      |
      | Last name          | Davidsonas                    |
      | Email address      | tadas.davidsonas2@invertus.eu |
      | Password           | secretpassword                |
      | Default page       | Dashboard                     |
      | Language           | English (English)             |
    # Allow or disallow this employee to log in to the Admin panel. true = YES; false = NO
      | Active             | true                           |
      | Permission profile | SuperAdmin                    |
    When employee "TadasTwo" closes showcase card "seo-urls_card"
    Then employee "TadasTwo" should not see showcase card "seo-urls_card"
