# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags module
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-test-modules-after-feature
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

  Scenario: Reset module status
    Given module ps_featuredproducts has following infos:
      | technical_name | ps_featuredproducts |
      | enabled        | true |
      | installed      | true |
    When I disable module "ps_featuredproducts"
    And I reset module "ps_featuredproducts"
    Then I should have an exception that disabled module cannot be reset
    When I enable module "ps_featuredproducts"
    When I reset module "ps_featuredproducts"
    Then module ps_featuredproducts has following infos:
      | technical_name | ps_featuredproducts |
      | enabled        | true |
      | installed      | true |

  Scenario: Get module infos
    Then module ps_emailsubscription has following infos:
      | technical_name | ps_emailsubscription |
      | version        | 1.0.0                |
      | enabled        | true                 |
      | installed      | true                 |

  Scenario: Uninstall modules
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
    When I bulk uninstall modules: "ps_featuredproducts,ps_emailsubscription" with deleteFile false
    Then module ps_featuredproducts has following infos:
      | installed      | false               |
    And module ps_emailsubscription has following infos:
      | installed      | false               |

  Scenario: Uninstall module
    Given module bankwire has following infos:
      | technical_name | bankwire            |
      | version        | 2.0.0               |
      | enabled        | true                |
      | installed      | true                |
    When I uninstall module "bankwire" with deleteFile true
    And module bankwire has following infos:
      | installed      | false               |
    Then I should have an exception that module is not found

  Scenario: Install module with files in folder modules
    When I install module "ps_featuredproducts" from "folder"
    Then module ps_featuredproducts has following infos:
      | technical_name | ps_featuredproducts  |
      | version        | 1.0.0                |
      | enabled        | true                 |
      | installed      | true                 |

  Scenario: Install already installed module
    When I install module "ps_featuredproducts" from "folder"
    Then I should have an exception that module is already installed

  Scenario: Install module with zip file on disk
    When I install module "test_cqrs_command" from "zip" "test_cqrs_command.zip"
    Then module test_cqrs_command has following infos:
      | technical_name | test_cqrs_command |
      | version        | 1.0.0                     |
      | enabled        | true                      |
      | installed      | true                      |

  Scenario: Install module with zip file on remote
    When I uninstall module "ps_featuredproducts" with deleteFile true
    And I install module "ps_featuredproducts" from "url" "https://github.com/PrestaShop/ps_featuredproducts/releases/download/v2.1.4/ps_featuredproducts.zip"
    Then module ps_featuredproducts has following infos:
      | technical_name | ps_featuredproducts |
      | version        | 2.1.5               |
      | enabled        | true                |
      | installed      | true                |

  Scenario: Update module with zip file on disk
    When I update module "test_cqrs_command" from "zip" "test_cqrs_command_1.0.1.zip"
    Then module test_cqrs_command has following infos:
      | technical_name | test_cqrs_command |
      | version        | 1.0.1                     |
      | enabled        | true                      |
      | installed      | true                      |

  Scenario: Update module with zip file on remote
    When I update module "ps_featuredproducts" from "url" "https://github.com/PrestaShop/ps_featuredproducts/releases/download/v2.1.5/ps_featuredproducts.zip"
    Then module ps_featuredproducts has following infos:
      | technical_name | ps_featuredproducts |
      | version        | 2.1.5               |
      | enabled        | true                |
      | installed      | true                |

  Scenario: Get module not present
    When module ps_notthere has following infos:
      | technical_name | ps_notthere |
      | version        | 1.0.0       |
      | enabled        | true        |
      | installed      | true        |
    Then I should have an exception that module is not found
