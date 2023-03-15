# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-virtual-product-file
@restore-products-before-feature
@virtual-product-file
@update-virtual-product-file
@reset-downloads-after-feature
Feature: Add virtual product file from BO (Back Office).
  As an employee I want to be able to add file of virtual product.

  Scenario: I update virtual product file
    Given I add product "product1" with following information:
      | name[en-US] | puffin icon |
      | type        | virtual     |
    And product "product1" should not have a file
    And product product1 type should be virtual
    And I add virtual product file "file1" to product "product1" with following details:
      | display name | puffin-logo.png |
      | file name    | app_icon.png    |
    And product "product1" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    And file "file1" for product "product1" should exist in system
    And file file1 for product product1 should have same file as app_icon.png
    When I update file "file1" with following details:
      | display name         | puffin-logo-updated1.png |
      | access days          | 7                        |
      | download times limit | 70                       |
      | expiration date      | 2020-10-10               |
    Then product "product1" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo-updated1.png |
      | access days          | 7                        |
      | download times limit | 70                       |
      | expiration date      | 2020-10-10               |
    And file "file1" for product "product1" should exist in system
    # details were modified but not the file itself
    And file file1 for product product1 should have same file as app_icon.png
    When I replace product "product1" file "file1" with a new file "file2" named "dummy_zip.zip" and following details:
      | display name         | puffin-logo-updated3.png |
      | access days          | 1                        |
      | download times limit | 5                        |
      | expiration date      | 2020-11-11               |
    Then product "product1" should have a virtual product file "file2" with following details:
      | display name         | puffin-logo-updated3.png |
      | access days          | 1                        |
      | download times limit | 5                        |
      | expiration date      | 2020-11-11               |
    And file "file2" for product "product1" should exist in system
    And file "file1" for product "product1" should not exist in system
    # Check that the new file matches the provided update file
    And file file2 for product product1 should have same file as dummy_zip.zip

  Scenario: I should not be able to update a file with invalid details
    Given product "product1" should have a virtual product file "file2" with following details:
      | display name         | puffin-logo-updated3.png |
      | access days          | 1                        |
      | download times limit | 5                        |
      | expiration date      | 2020-11-11               |
    And file "file2" for product "product1" should exist in system
    And file file2 for product product1 should have same file as dummy_zip.zip
    When I update file "file2" with following details:
      | display name | {logo} |
    Then I should get error that product file "display name" is invalid
    When I update file "file2" with following details:
      | display name | ok filename |
      | access days  | -1          |
    Then I should get error that product file "access days" is invalid
    When I update file "file2" with following details:
      | display name         | zipped files pack for second product |
      | download times limit | -10                                  |
    Then I should get error that product file "download times limit" is invalid
    And product "product1" should have a virtual product file "file2" with following details:
      | display name         | puffin-logo-updated3.png |
      | access days          | 1                        |
      | download times limit | 5                        |
      | expiration date      | 2020-11-11               |
    And file "file2" for product "product1" should exist in system
    # The file itself has never been modified
    And file file2 for product product1 should have same file as dummy_zip.zip
