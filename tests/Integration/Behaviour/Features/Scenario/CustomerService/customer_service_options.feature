# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-service-options
@restore-all-tables-before-feature
@customer-service-options
Feature: Customer service options

  Scenario: Save the contact options panel (file upload + signature)
    When I update customer service options with:
      | file_upload          | true  |
      | signature_in_default | Hello |
    Then customer service file upload should be enabled
    And customer service signature in default language should be "Hello"

  Scenario: Toggle the contact-side file upload off
    When I update customer service options with:
      | file_upload          | false |
      | signature_in_default | Hi    |
    Then customer service file upload should be disabled

  Scenario: Save the IMAP options panel
    When I update IMAP options with:
      | imap_url            | mail.example.com |
      | imap_port           | 993              |
      | imap_user           | support          |
      | imap_password       | secret           |
      | imap_delete_msg     | true             |
      | imap_create_threads | false            |
      | imap_opt_pop3       | false            |
      | imap_opt_norsh      | false            |
      | imap_opt_ssl        | true             |
      | imap_opt_validate_cert   | true        |
      | imap_opt_novalidate_cert | false       |
      | imap_opt_tls        | false            |
      | imap_opt_notls      | false            |
    Then IMAP configuration "PS_SAV_IMAP_URL" should be "mail.example.com"
    And IMAP configuration "PS_SAV_IMAP_PORT" should be "993"
    And IMAP configuration "PS_SAV_IMAP_USER" should be "support"
    And IMAP configuration "PS_SAV_IMAP_PWD" should be "secret"
    And IMAP configuration "PS_SAV_IMAP_DELETE_MSG" should be enabled
    And IMAP configuration "PS_SAV_IMAP_CREATE_THREADS" should be disabled
    And IMAP configuration "PS_SAV_IMAP_OPT_SSL" should be enabled
    And IMAP configuration "PS_SAV_IMAP_OPT_VALIDATE-CERT" should be enabled
    And IMAP configuration "PS_SAV_IMAP_OPT_NOVALIDATE-CERT" should be disabled

  Scenario: Keep the saved IMAP password when the password field is left empty
    When I update IMAP options with:
      | imap_password | |
    Then IMAP configuration "PS_SAV_IMAP_PWD" should be "secret"
