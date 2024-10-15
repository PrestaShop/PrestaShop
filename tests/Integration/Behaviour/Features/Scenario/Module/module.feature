# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags module
@reset-database-before-feature
@module
Feature: Module
  PrestaShop allows BO users to manage modules
  As a BO user
  I must be able to enable/disable modules

  Scenario: Bulk Status
    Given module ps_featuredproducts has following infos:
      | technical_name | ps_featuredproducts |
      | version        | 1.0.0               |
      | enabled        | true                |
      | installed      | true                |
    Given module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | version        | 1.0.0                |
      | enabled        | true                 |
      | installed      | true                 |
    When I bulk disable modules: "ps_featuredproducts,ps_emailsubscription"
    Then module ps_featuredproducts has following infos:
      | enabled | false |
    And module ps_emailsubscription has following infos:
      | enabled | false |
    When I bulk enable modules: "ps_emailsubscription"
    Then module ps_featuredproducts has following infos:
      | enabled | false |
    And module ps_emailsubscription has following infos:
      | enabled | true |
    When I bulk enable modules: "ps_featuredproducts"
    Then module ps_featuredproducts has following infos:
      | enabled | true |
    And module ps_emailsubscription has following infos:
      | enabled | true |

  Scenario: Update module status
    Given module ps_featuredproducts has following infos:
      | enabled | true |
    When I disable module "ps_featuredproducts"
    Then module ps_featuredproducts has following infos:
      | enabled | false |
    When I enable module "ps_featuredproducts"
    Given module ps_featuredproducts has following infos:
      | enabled | true |

  Scenario: Get module infos
    Then module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | version        | 1.0.0                |
      | enabled        | true                 |
      | installed      | true                 |

  Scenario: Get module not present
    When module ps_notthere has following infos:
      | technical_name | ps_notthere |
      | version        | 1.0.0       |
      | enabled        | true        |
      | installed      | true        |
    Then I should have an exception that module is not found
