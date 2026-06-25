# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s email_body_translation
@restore-all-tables-before-feature
Feature: Email body template management
  In order to customize transactional email content
  As a BO user
  I should be able to list and edit email body templates

  Scenario: List core email templates for English locale
    When I list email body templates for locale "en"
    Then I should see email body template "account" with source "core"
    And I should see email body template "order_conf" with source "core"
    And I should see email body template "contact" with source "core"

  Scenario: List includes module email templates
    When I list email body templates for locale "en"
    Then I should see email body template "new_order" with source "module" and module "ps_emailalerts"

  Scenario: Core email templates have both HTML and TXT versions
    When I list email body templates for locale "en"
    Then email body template "account" should have HTML version
    And email body template "account" should have TXT version

  Scenario: Get email template for editing
    When I get email body template "account" for locale "en" with source "core"
    Then I should get an editable email body template with non-empty HTML content
    And I should get an editable email body template with non-empty TXT content

  Scenario: Edit email template content
    When I edit email body template "account" for locale "en" with source "core" with:
      | html_content | <p>Updated HTML content for testing</p> |
      | txt_content  | Updated TXT content for testing         |
    Then email body template "account" for locale "en" with source "core" HTML content should contain "Updated HTML content for testing"
    And email body template "account" for locale "en" with source "core" TXT content should contain "Updated TXT content for testing"

  Scenario: Edit with HTML containing JavaScript should fail
    When I edit email body template "account" for locale "en" with source "core" with:
      | html_content | <script>alert('xss')</script> |
      | txt_content  | Valid text content             |
    Then I should get an email template constraint error

  Scenario: Get non-existing template should fail
    When I get email body template "non_existing_template_xyz" for locale "en" with source "core"
    Then I should get an email template not found error

  Scenario: Create EmailTemplateName with empty name should fail
    When I create email template name with value ""
    Then I should get an email template constraint error

  Scenario: Create EmailTemplateName with invalid characters should fail
    When I create email template name with value "template with spaces"
    Then I should get an email template constraint error

  Scenario: Create EmailTemplateSource with invalid source should fail
    When I create email template source with value "invalid_source"
    Then I should get an email template constraint error

  Scenario: Create module EmailTemplateSource without module name should fail
    When I create module email template source without module name
    Then I should get an email template constraint error
