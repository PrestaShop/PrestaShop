# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags uninstall-module
@reset-test-modules-before-feature
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-test-modules-after-feature
@uninstall-module
Feature: Module
  PrestaShop allows BO users to manage modules
  As a BO user
  I must be able to uninstall modules

  Scenario: Uninstall modules
    Given module ps_featuredproducts has following infos:
      | technical_name    | ps_featuredproducts |
      | installed_version | 1.0.0               |
      | module_version    | 1.0.0               |
      | enabled           | true                |
      | installed         | true                |
    Given module ps_emailsubscription has following infos:
      | technical_name    | ps_emailsubscription |
      | installed_version | 1.0.0                |
      | module_version    | 1.0.0                |
      | enabled           | true                 |
      | installed         | true                 |
    When I bulk uninstall modules: "ps_featuredproducts,ps_emailsubscription" with deleteFiles false
    Then module ps_featuredproducts has following infos:
      | technical_name    | ps_featuredproducts |
      | installed_version |                     |
      | module_version    | 1.0.0               |
      | enabled           | false               |
      | installed         | false               |
    And module ps_emailsubscription has following infos:
      | technical_name    | ps_emailsubscription |
      | installed_version |                      |
      | module_version    | 1.0.0                |
      | enabled           | false                |
      | installed         | false                |

  Scenario: Bulk uninstall on uninstalled or not existing modules is not allowed
    Given module ps_emailsubscription has following infos:
      | enabled   | false |
      | installed | false |
    Given module ps_banner has following infos:
      | enabled   | true |
      | installed | true |
    When I bulk uninstall modules: "ps_emailsubscription,ps_banner" with deleteFiles false
    Then I should have an exception that module is not installed
    # The modules have not been modified
    And module ps_emailsubscription has following infos:
      | enabled   | false |
      | installed | false |
    And module ps_banner has following infos:
      | enabled   | true |
      | installed | true |
    When I bulk uninstall modules: "ps_banner,ps_emailsubscription" with deleteFiles false
    Then I should have an exception that module is not installed
    # The result is the same regardless of the order
    And module ps_emailsubscription has following infos:
      | enabled   | false |
      | installed | false |
    And module ps_banner has following infos:
      | enabled   | true |
      | installed | true |
    # First module error triggers error
    When I bulk uninstall modules: "ps_banner,ps_emailsubscription,ps_notthere" with deleteFiles false
    Then I should have an exception that module is not installed
    When I bulk uninstall modules: "ps_notthere,ps_banner,ps_emailsubscription" with deleteFiles false
    Then I should have an exception that module is not found

  Scenario: Bulk toggle status on uninstalled or not existing modules is not allowed
    Given module ps_emailsubscription has following infos:
      | enabled   | false |
      | installed | false |
    Given module ps_banner has following infos:
      | enabled   | true |
      | installed | true |
    When I bulk disable modules: "ps_emailsubscription,ps_banner"
    Then I should have an exception that module is not installed
    # The modules have not been modified
    And module ps_emailsubscription has following infos:
      | enabled   | false |
      | installed | false |
    And module ps_banner has following infos:
      | enabled   | true |
      | installed | true |
    When I bulk disable modules: "ps_banner,ps_emailsubscription"
    Then I should have an exception that module is not installed
    # The result is the same regardless of the order
    And module ps_emailsubscription has following infos:
      | enabled   | false |
      | installed | false |
    And module ps_banner has following infos:
      | enabled   | true |
      | installed | true |
    # First module error triggers error
    When I bulk disable modules: "ps_banner,ps_emailsubscription,ps_notthere"
    Then I should have an exception that module is not installed
    When I bulk disable modules: "ps_notthere,ps_banner,ps_emailsubscription"
    Then I should have an exception that module is not found

  Scenario: Reset uninstalled the module is installed afterward
    Given module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | enabled        | false                |
      | installed      | false                |
    When I reset module "ps_featuredproducts"
    Then I should have an exception that module is not installed
    And module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | enabled        | false                |
      | installed      | false                |

  Scenario: Update module status when uninstalled is not allowed
    Given module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | enabled        | false                |
      | installed      | false                |
    When I disable module "ps_emailsubscription"
    Then I should have an exception that module is not installed
    When I enable module "ps_emailsubscription"
    Then I should have an exception that module is not installed
    And module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | enabled        | false                |
      | installed      | false                |

  Scenario: Uninstall module
    Given module bankwire has following infos:
      | technical_name    | bankwire |
      | installed_version | 2.0.0    |
      | module_version    | 2.0.0    |
      | enabled           | true     |
      | installed         | true     |
    When I uninstall module "bankwire" with deleteFiles true
    # Since the module is completely removed and is not present it cannot be found anymore
    And module bankwire has following infos:
      | installed | false |
    Then I should have an exception that module is not found

  Scenario: Uninstall module that is not installed
    Given module ps_featuredproducts has following infos:
      | technical_name    | ps_featuredproducts |
      | installed_version |                     |
      | module_version    | 1.0.0               |
      | enabled           | false               |
      | installed         | false               |
    When I uninstall module "ps_featuredproducts" with deleteFiles false
    Then I should have an exception that module is not installed

  Scenario: Uninstall module that is not installed possible only to remove files
    Given I upload module from "zip" "test_install_cqrs_command.zip" that should have the following infos:
      | technical_name    | test_install_cqrs_command |
      | installed_version |                           |
      | module_version    | 1.0.0                     |
      | enabled           | false                     |
      | installed         | false                     |
    When I uninstall module "test_install_cqrs_command" with deleteFiles true
    # Since the module is completely removed and is not present it cannot be found anymore
    Then module test_install_cqrs_command has following infos:
      | installed | false |
    Then I should have an exception that module is not found
