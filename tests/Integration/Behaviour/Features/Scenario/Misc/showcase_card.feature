# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s misc
@reset-database-before-feature
Feature: Showcase card
  In order to hide not relevant information in the Back Office
  As a BO user
  I should be able to close showcase card

  Scenario: close showcase card in single shop context
    Given single shop context is loaded
    When I close "seo-urls_card" showcase card as employee 1
    Then showcase card "seo-urls_card" for employee 1 should be closed

# todo: remove after resolving the issue
# Showcase card reappear after closing it in multistore context: https://github.com/PrestaShop/PrestaShop/issues/12390
  Scenario: close showcase card in multiple shops context
    Given multiple shop context is loaded
    When I close "seo-urls_card" showcase card as employee 1
    Then showcase card "seo-urls_card" for employee 1 should be closed
