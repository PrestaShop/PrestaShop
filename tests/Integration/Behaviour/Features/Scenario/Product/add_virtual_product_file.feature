# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags virtual-product-file
@reset-database-before-feature
@virtual-product-file
@clear-downloads-after-feature
Feature: Add virtual product file from BO (Back Office).
  As an employee I want to be able to add file of virtual product.

  Scenario: I add virtual product file
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And I add product "product1" with following information:
      | name       | en-US:puffin icon; |
      | is_virtual | true               |
    And product "product1" should have no virtual product files
    When I add new virtual product file "file1" of product "product1" with following properties:
      | display name         | puffin-logo.png   |
      | file name            | app_icon.png      |
      | expiration date      | one week from now |
      | access days          | 3                 |
      | download times limit | 3                 |
    Then product "product1" should have a virtual product file "file1" with following properties:
      | display name         | puffin-logo.png   |
      | file name            | app_icon.png      |
      | expiration date      | one week from now |
      | access days          | 3                 |
      | download times limit | 3                 |
