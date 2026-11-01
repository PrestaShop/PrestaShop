# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags customer-group-shop-association
@restore-all-tables-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@customer-group-shop-association
Feature: Customer groups are limited to the groups of the customer's shop
  As a BO user
  I must not be able to give a customer a group that is not available in its shop

  Background:
    Given groups feature is activated
    And I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And I create a customer group "shop1_group" with the following details:
      | name[en-US]             | Shop 1 group |
      | reduction               | 0            |
      | displayPriceTaxExcluded | false        |
      | showPrice               | true         |
      | shopIds                 | shop1        |
    And I create a customer group "shop2_group" with the following details:
      | name[en-US]             | Shop 2 group |
      | reduction               | 0            |
      | displayPriceTaxExcluded | false        |
      | showPrice               | true         |
      | shopIds                 | shop2        |

  Scenario: A customer is created with the groups of its shop
    When I create a customer "CUST-1" with following properties:
      | firstName      | Jane                      |
      | lastName       | Doe                       |
      | email          | jane.doe@prestashop.com   |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group               |
    Then I query customer "CUST-1" I should get a Customer with properties:
      | defaultGroupId | shop1_group |
      | groupIds       | shop1_group |

  Scenario: A customer cannot be created with a group of another shop
    When I attempt to create a customer "CUST-2" with following properties:
      | firstName      | John                      |
      | lastName       | Doe                       |
      | email          | john.doe@prestashop.com   |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group,shop2_group   |
    Then I should get an error that the customer group is not available in the customer shop

  Scenario: A customer cannot be given a group of another shop
    When I create a customer "CUST-3" with following properties:
      | firstName      | Ann                       |
      | lastName       | Doe                       |
      | email          | ann.doe@prestashop.com    |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group               |
    And I attempt to edit customer "CUST-3" and I change the following properties:
      | groupIds | shop1_group,shop2_group |
    Then I should get an error that the customer group is not available in the customer shop
    When I attempt to edit customer "CUST-3" and I change the following properties:
      | defaultGroupId | shop2_group |
      | groupIds       | shop2_group |
    Then I should get an error that the customer group is not available in the customer shop
    And I query customer "CUST-3" I should get a Customer with properties:
      | defaultGroupId | shop1_group |
      | groupIds       | shop1_group |

  Scenario: A customer shared by its shop group can have the groups of the other shops
    Given Shop group "default_shop_group" shares its customers
    When I create a customer "CUST-4" with following properties:
      | firstName      | Max                       |
      | lastName       | Doe                       |
      | email          | max.doe@prestashop.com    |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group,shop2_group   |
    Then I query customer "CUST-4" I should get a Customer with properties:
      | groupIds | shop1_group,shop2_group |

  Scenario: A customer is edited with the groups of its shop
    When I create a customer "CUST-5" with following properties:
      | firstName      | Eve                       |
      | lastName       | Doe                       |
      | email          | eve.doe@prestashop.com    |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group               |
    And I edit customer "CUST-5" and I change the following properties:
      | defaultGroupId | Customer                |
      | groupIds       | shop1_group,Customer    |
    Then I query customer "CUST-5" I should get a Customer with properties:
      | defaultGroupId | Customer             |
      | groupIds       | shop1_group,Customer |

  Scenario: A customer shared by its shop group can be given the groups of the other shops
    Given Shop group "default_shop_group" shares its customers
    When I create a customer "CUST-6" with following properties:
      | firstName      | Tom                       |
      | lastName       | Doe                       |
      | email          | tom.doe@prestashop.com    |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group               |
    And I edit customer "CUST-6" and I change the following properties:
      | groupIds | shop1_group,shop2_group |
    Then I query customer "CUST-6" I should get a Customer with properties:
      | groupIds | shop1_group,shop2_group |

  Scenario: A customer keeps a group of another shop it already has
    Given Shop group "default_shop_group" shares its customers
    And I create a customer "CUST-7" with following properties:
      | firstName      | Liz                       |
      | lastName       | Doe                       |
      | email          | liz.doe@prestashop.com    |
      | password       | PrestaShopForever1_!      |
      | shopId         | shop1                     |
      | defaultGroupId | shop1_group               |
      | groupIds       | shop1_group,shop2_group   |
    And Shop group "default_shop_group" does not share its customers
    When I edit customer "CUST-7" and I change the following properties:
      | firstName | Elizabeth               |
      | groupIds  | shop1_group,shop2_group |
    Then I query customer "CUST-7" I should get a Customer with properties:
      | firstName | Elizabeth               |
      | groupIds  | shop1_group,shop2_group |
