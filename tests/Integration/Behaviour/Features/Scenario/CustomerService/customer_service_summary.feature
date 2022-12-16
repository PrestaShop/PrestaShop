# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-management
@restore-all-tables-before-feature
@customer-management
Feature: Customer service summary

  Scenario: Add customer thread
    When I create a contact "CONTACT-1" with following properties:
      | localisedTitles        | Support |
      | isMessageSavingEnabled | true    |
    And I create a contact "CONTACT-2" with following properties:
      | localisedTitles        | Support |
      | isMessageSavingEnabled | true   |
    And I add new customer thread "thread1" with following properties:
      | message          | test message2 |
      | contactReference | CONTACT-2     |
    Then contact "CONTACT-1" should have 0 threads
    And contact "CONTACT-2" should have 1 threads
