# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-virtual-product-file
@reset-database-before-feature
@virtual-product-file
@add-virtual-product-file
@reset-downloads-after-feature
Feature: Add virtual product file from BO (Back Office).
  As an employee I want to be able to add file of virtual product.

  Scenario: I add virtual product file without limiting access days, download times and without expiration date
    Given I add product "product1" with following information:
      | name[en-US] | puffin icon |
      | type        | virtual     |
    And product "product1" should not have a file
    And product product1 type should be virtual
    When I add virtual product file "file1" to product "product1" with following details:
      | display name | puffin-logo.png |
      | file name    | app_icon.png    |
    Then product "product1" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    And file "file1" for product "product1" should exist in system

  Scenario: I add virtual product file with limited access days, downloads and expiration date
    Given I add product "product2" with following information:
      | name[en-US] | puffin icon 2 |
      | type        | virtual     |
    And product "product2" should not have a file
    And product product2 type should be virtual
    When I add virtual product file "file2" to product "product2" with following details:
      | file name            | app_icon.png        |
      | display name         | puffin-logo2.png    |
      | access days          | 3                   |
      | download times limit | 3                   |
      | expiration date      | 2020-11-20 10:00:00 |
    Then product "product2" should have a virtual product file "file2" with following details:
      | display name         | puffin-logo2.png    |
      | access days          | 3                   |
      | download times limit | 3                   |
      | expiration date      | 2020-11-20 10:00:00 |

  Scenario: I add zip type virtual product file
    Given I add product "product3" with following information:
      | name[en-US] | puffin icon 3 |
      | type        | virtual     |
    And product "product3" should not have a file
    And product product3 type should be virtual
    When I add virtual product file "file3" to product "product3" with following details:
      | file name            | dummy_zip.zip       |
      | display name         | zipped files pack   |
      | access days          | 5                   |
      | download times limit | 100                 |
      | expiration date      | 2000-01-20 09:01:01 |
    Then product "product3" should have a virtual product file "file3" with following details:
      | display name         | zipped files pack   |
      | access days          | 5                   |
      | download times limit | 100                 |
      | expiration date      | 2000-01-20 09:01:01 |
    And file "file3" for product "product3" should exist in system

  Scenario: I should not be able to add file to a product which is not virtual
    Given I add product product4 with following information:
      | name[en-US] | puffin icon 4 |
      | type        | standard      |
    And product product4 type should be standard
    And product "product4" should not have a file
    When I add virtual product file "file4" to product "product4" with following details:
      | file name    | dummy_zip.zip     |
      | display name | zipped files pack |
    Then I should get error that this action is allowed for virtual product only
    And product "product4" should not have a file

  Scenario: I should not be able to add file to a product which already has a file
    Given I add product product5 with following information:
      | name[en-US] | puffin icon 5 |
      | type        | virtual       |
    And product product5 type should be virtual
    And product "product5" should not have a file
    When I add virtual product file "file5" to product "product5" with following details:
      | file name    | dummy_zip.zip                      |
      | display name | zipped files pack for fith product |
    Then product "product5" should have a virtual product file "file5" with following details:
      | display name         | zipped files pack for fith product |
      | access days          | 0                                  |
      | download times limit | 0                                  |
      | expiration date      |                                    |
    When I add virtual product file "file5-5" to product "product5" with following details:
      | file name    | app_icon.png     |
      | display name | puffin-logo2.png |
    Then I should get error that product already has a file
    And product "product5" should have a virtual product file "file5" with following details:
      | display name         | zipped files pack for fith product |
      | access days          | 0                                  |
      | download times limit | 0                                  |
      | expiration date      |                                    |
    And file "file5" for product "product5" should exist in system

  Scenario: I should not be able to add a file with invalid details
    Given I add product product6 with following information:
      | name[en-US] | puffin zip 6 |
      | type        | virtual      |
    And product product6 type should be virtual
    And product "product6" should not have a file
    When I add virtual product file "file6" to product "product6" with following details:
      | file name    | dummy_zip.zip |
      | display name | {my filename} |
    Then I should get error that product file "display name" is invalid
    When I add virtual product file "file6" to product "product6" with following details:
      | file name    | dummy_zip.zip                       |
      | display name | zipped files pack for sixth product |
      | access days  | -1                                  |
    Then I should get error that product file "access days" is invalid
    When I add virtual product file "file6" to product "product6" with following details:
      | file name            | dummy_zip.zip                       |
      | display name         | zipped files pack for sixth product |
      | download times limit | -10                                 |
    Then I should get error that product file "download times limit" is invalid
    And product "product6" should not have a file
