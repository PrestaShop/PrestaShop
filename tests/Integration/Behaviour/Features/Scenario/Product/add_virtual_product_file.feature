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
    And product "product1" should not have a file
    And product product1 type should be virtual
    When I add virtual product file "file1" to "product1" with following details:
      | display name | puffin-logo.png |
      | file name    | app_icon.png    |
    Then product "product1" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png     |
      | access days          | 0                   |
      | download times limit | 0                   |
      | expiration date      | 0000-00-00 00:00:00 |

  Scenario: I add virtual product file with limited access days, downloads and expiration date
    And I add product "product2" with following information:
      | name       | en-US:puffin icon 2 |
      | is_virtual | true              |
    And product "product2" should not have a file
    And product product2 type should be virtual
    When I add virtual product file "file2" to "product2" with following details:
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
    And I add product "product3" with following information:
      | name       | en-US:puffin icon 3 |
      | is_virtual | true              |
    And product "product3" should not have a file
    And product product3 type should be virtual
    When I add virtual product file "file3" to "product3" with following details:
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

  Scenario: I should not be able to add file to a product which is not virtual
    Given I add product product4 with following information:
      | name       | en-US:puffin icon |
      | is_virtual | false             |
    And product product4 type should be standard
    And product "product4" should not have a file
    When I add virtual product file "file4" to "product4" with following details:
      | file name            | dummy_zip.zip       |
      | display name         | zipped files pack   |
    Then I should get error that only virtual product can have file
    And product "product4" should not have a file
