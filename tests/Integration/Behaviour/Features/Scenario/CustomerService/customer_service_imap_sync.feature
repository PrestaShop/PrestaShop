# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-service-imap-sync
@restore-all-tables-before-feature
@customer-service-imap-sync
Feature: Customer service IMAP sync

  Background:
    Given there are no customer threads

  Scenario: Syncing IMAP without configured credentials returns a configuration error
    Given IMAP configuration is unset
    When I synchronise customer service IMAP messages
    Then customer service IMAP sync should fail with errors:
      | IMAP configuration is not correct |

  Scenario: Syncing IMAP with a missing port returns a configuration error
    Given the IMAP server is configured with:
      | imap_url      | mail.example.com |
      | imap_port     |                  |
      | imap_user     | support          |
      | imap_password | secret           |
    When I synchronise customer service IMAP messages
    Then customer service IMAP sync should fail with errors:
      | IMAP configuration is not correct |
