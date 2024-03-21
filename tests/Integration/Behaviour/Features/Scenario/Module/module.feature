# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags module
@reset-database-before-feature
@module
Feature: Module
  PrestaShop allows BO users to manage modules
  As a BO user
  I must be able to enable/disable modules

  Background:
    Given shop "shop1" with name "test_shop" exists
    And the module "ps_featuredproducts" is installed
    And the module "ps_emailsubscription" is installed

  Scenario: Bulk Status
    Given the module "ps_featuredproducts" is enabled
    And the module "ps_emailsubscription" is enabled
    When I bulk disable modules: "ps_featuredproducts,ps_emailsubscription"
    Then the module "ps_featuredproducts" is disabled
    And the module "ps_emailsubscription" is disabled
    When I bulk enable modules: "ps_emailsubscription"
    Then the module "ps_featuredproducts" is disabled
    And the module "ps_emailsubscription" is enabled
    When I bulk enable modules: "ps_featuredproducts"
    Then the module "ps_featuredproducts" is enabled
    And the module "ps_emailsubscription" is enabled

  Scenario: Get module infos
    Given the module with technical name ps_emailsubscription exists
    Then module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | version        | 2.8.2                |
      | enabled        | true                 |
