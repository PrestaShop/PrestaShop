# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete-virtual-product-file
@reset-database-before-feature
@virtual-product-file
@delete-virtual-product-file
@reset-downloads-after-feature
Feature: Delete virtual product file from BO (Back Office).
  As an employee I want to be able to delete file of virtual product.

  Scenario: I delete virtual product file
    Given I add product "product1" with following information:
      | name[en-US] | puffin icon |
      | is_virtual  | true        |
    And product "product1" should not have a file
    And product product1 type should be virtual
    And I add virtual product file "file1" to "product1" with following details:
      | display name | puffin-logo.png |
      | file name    | app_icon.png    |
    And product "product1" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    When I delete virtual product file "file1"
    And product "product1" should not have a file
