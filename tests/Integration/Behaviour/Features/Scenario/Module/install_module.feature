# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags install-module
@reset-test-modules-before-feature
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-test-modules-after-feature
@install-module
Feature: Module
  PrestaShop allows BO users to manage modules
  As a BO user
  I must be able to upload/install modules

  Scenario: Upload module with zip file on disk (not present initially)
    # Check that module is not present before we upload it
    Given module test_install_cqrs_command has following infos:
      | installed | false |
    Then I should have an exception that module is not found
    # I cannot install a module not present
    When I install module "test_install_cqrs_command"
    Then I should have an exception that module is not found
    # Now I upload based on a local zip file
    When I upload module from "zip" "test_install_cqrs_command.zip" that should have the following infos:
      | technical_name    | test_install_cqrs_command |
      | installed_version |                           |
      | module_version    | 1.0.0                     |
      | enabled           | false                     |
      | installed         | false                     |
    And I install module "test_install_cqrs_command"
    Then module test_install_cqrs_command has following infos:
      | technical_name    | test_install_cqrs_command |
      | installed_version | 1.0.0                     |
      | module_version    | 1.0.0                     |
      | enabled           | true                      |
      | installed         | true                      |
    When I install module "test_install_cqrs_command"
    Then I should have an exception that module is already installed

  Scenario: Upload module with zip file from remote (not present initially)
    # Check that module is not present before we upload it
    Given module dashactivity has following infos:
      | installed | false |
    Then I should have an exception that module is not found
    # I cannot install a module not present
    When I install module "dashactivity"
    Then I should have an exception that module is not found
    # Now I can upload module from external zip file
    And I upload module from "url" "https://github.com/PrestaShop/dashactivity/releases/download/v2.1.0/dashactivity.zip" that should have the following infos:
      | technical_name    | dashactivity |
      | installed_version |              |
      | module_version    | 2.1.0        |
      | enabled           | false        |
      | installed         | false        |
    And I install module "dashactivity"
    Then module dashactivity has following infos:
      | technical_name    | dashactivity |
      | installed_version | 2.1.0        |
      | module_version    | 2.1.0        |
      | enabled           | true         |
      | installed         | true         |
