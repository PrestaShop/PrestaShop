# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags virtual-product-file
@reset-database-before-feature
@virtual-product-file
@clear-downloads-after-feature
Feature: Add virtual product file from BO (Back Office).
  As an employee I want to be able to add file of virtual product.

  Scenario: I add virtual product file without limiting access days, download times and without expiration date
    And I add product "product1" with following information:
      | name       | en-US:puffin icon |
      | is_virtual | true              |
    And virtual product "product1" should not have a file
    When I add new virtual product "product1" file "file1" with following details:
      | display name | puffin-logo.png |
      | file name    | app_icon.png    |
    Then virtual product "product1" should have a file "file1" with following details:
      | display name | puffin-logo.png |
      | access days          | 0                   |
      | download times limit | 0                   |
      | expiration date      | 0000-00-00 00:00:00 |

  Scenario: I add virtual product file with limited access days, downloads and expiration date
    When I add new virtual product "product1" file "file2" with following details:
      | display name         | puffin-logo2.png    |
      | access days          | 3                   |
      | download times limit | 3                   |
      | expiration date      | 2020-11-20 10:00:00 |
    Then virtual product "product1" should have a file "file2" with following details:
      | display name         | puffin-logo2.png    |
      | access days          | 3                   |
      | download times limit | 3                   |
      | expiration date      | 2020-11-20 10:00:00 |
