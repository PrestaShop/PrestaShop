# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags virtual-product-file
@reset-database-before-feature
@virtual-product-file
@clear-downloads-after-feature
Feature: Add virtual product file from BO (Back Office).
  As an employee I want to be able to add file of virtual product.

  Scenario: I add virtual product file
    Given date and time now is "2020-11-09 10:00:00"
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And I add product "product1" with following information:
      | name       | en-US:puffin icon; |
      | is_virtual | true               |
    And virtual product "product1" should not have a file
    When I add new virtual product "product1" file "file1" with following details:
      | display name         | puffin-logo.png     |
      | file name            | app_icon.png        |
      | access days          | 3                   |
      | download times limit | 3                   |
      | expiration date      | 2020-11-20 10:00:00 |
    Then virtual product "product1" should have a file "file1" with following details:
      | display name         | puffin-logo.png     |
      | file name            | app_icon.png        |
      | access days          | 3                   |
      | download times limit | 3                   |
      | expiration date      | 2020-11-20 10:00:00 |
