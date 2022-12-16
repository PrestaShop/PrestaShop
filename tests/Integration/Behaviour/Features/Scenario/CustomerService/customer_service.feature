# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-management
@restore-all-tables-before-feature
@customer-management
Feature: Customer service

  Scenario: Add customer thread
    When I create a contact "CONTACT-1" with following properties:
      | localisedTitles        | Support |
      | isMessageSavingEnabled | true    |
    And I add new customer thread "thread1" with following properties:
      | message          | test message1 |
      | contactReference | CONTACT-1    |
    Then customer thread "thread1" should have the latest message "test message1"

  Scenario: Response to thread
    When I respond to customer thread "thread1" with following properties:
      | reply_message | test message2 |
    Then customer thread "thread1" should have the latest message "test message2"

  Scenario: Update thread status to handled
    When I update thread "thread1" status to "closed"
    Then customer thread "thread1" should be "closed"

  Scenario: Update thread status to handled
    When I update thread "thread1" status to "pending1"
    Then customer thread "thread1" should be "pending1"

  Scenario: Update thread status to handled
    When I update thread "thread1" status to "pending2"
    Then customer thread "thread1" should be "pending2"

  Scenario: Update thread status to handled
    When I update thread "thread1" status to "open"
    Then customer thread "thread1" should be "open"

  Scenario: I delete thread
    When I delete thread "thread1"
    Then thread "thread1" should be deleted
