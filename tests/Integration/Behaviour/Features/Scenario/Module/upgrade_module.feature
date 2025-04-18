# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags upgrade-module
@reset-test-modules-before-feature
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-test-modules-after-feature
@upgrade-module
Feature: Module
  PrestaShop allows BO users to manage modules
  As a BO user
  I must be able to upload/upgrade modules

  Scenario: Upgrade module with zip file from remote (not present initially)
    # Check that module is not present before we upload it
    Given module dashproducts has following infos:
      | installed | false |
    Then I should have an exception that module is not found
    # I cannot install a module not present
    When I upload module from "url" "https://github.com/PrestaShop/dashproducts/releases/download/v2.1.2/dashproducts.zip" that should have the following infos:
      | technical_name    | dashproducts |
      | installed_version |              |
      | module_version    | 2.1.2        |
      | enabled           | false        |
      | installed         | false        |
    And I install module "dashproducts"
    Then module dashproducts has following infos:
      | technical_name    | dashproducts |
      | installed_version | 2.1.2        |
      | module_version    | 2.1.2        |
      | enabled           | true         |
      | installed         | true         |
    # Now I can upload module from external zip file
    When I upload module from "url" "https://github.com/PrestaShop/dashproducts/releases/download/v2.1.3/dashproducts.zip" that should have the following infos:
      | technical_name    | dashproducts |
      | installed_version | 2.1.2        |
      | module_version    | 2.1.3        |
      | enabled           | true        |
      | installed         | true         |
    And I upgrade module dashproducts
    Then module dashproducts has following infos:
      | technical_name    | dashproducts |
      | installed_version | 2.1.3        |
      | module_version    | 2.1.3        |
      | enabled           | true         |
      | installed         | true         |
    # I try to upgrade the module while it's already up to date
    When I upgrade module dashproducts
    Then I should have an exception that module is already up to date
    And module dashproducts has following infos:
      | technical_name    | dashproducts |
      | installed_version | 2.1.3        |
      | module_version    | 2.1.3        |
      | enabled           | true         |
      | installed         | true         |
