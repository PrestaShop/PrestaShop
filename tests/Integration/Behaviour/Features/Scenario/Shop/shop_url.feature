# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop --tags shop-url
@restore-all-tables-before-feature
@clear-cache-before-feature
@shop-url
Feature: Shop url management (BO)
  As a BO user
  I want to add, edit, enable, disable and delete shop urls

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop group "default_group" with name "Default" exists

  Scenario: Add a secondary url to a shop
    Given the main url of shop "shop1" is "main_url"
    When I add a shop url "secondary_url" with the following properties:
      | shop         | shop1          |
      | domain       | secondary.test |
      | domain_ssl   | secure.test    |
      | physical_uri | store          |
      | virtual_uri  | shoes          |
    Then shop url "secondary_url" should have the following properties:
      | shop         | shop1          |
      | domain       | secondary.test |
      | domain_ssl   | secure.test    |
      | physical_uri | /store/        |
      | virtual_uri  | shoes/         |
      | main         | false          |
      | active       | true           |
    And shop url "main_url" should have the following properties:
      | main | true |

  Scenario: A url already used by another shop url is refused
    When I add a shop url "duplicate_url" with the following properties:
      | shop         | shop1          |
      | domain       | secondary.test |
      | physical_uri | /store/        |
      | virtual_uri  | shoes/         |
    Then I should get error that shop url is already used

  Scenario: Reserved or malformed virtual uris are refused
    When I add a shop url "img_url" with the following properties:
      | shop        | shop1        |
      | domain      | virtual.test |
      | virtual_uri | img          |
    Then I should get error that shop url virtual uri is invalid
    When I add a shop url "numeric_url" with the following properties:
      | shop        | shop1        |
      | domain      | virtual.test |
      | virtual_uri | 42           |
    Then I should get error that shop url virtual uri is invalid
    When I add a shop url "malformed_url" with the following properties:
      | shop        | shop1        |
      | domain      | virtual.test |
      | virtual_uri | bad uri!     |
    Then I should get error that shop url virtual uri is invalid

  Scenario: The domain is required
    When I add a shop url "no_domain_url" with the following properties:
      | shop       | shop1        |
      | domain     |              |
      | domain_ssl | virtual.test |
    Then I should get error that shop url domain is invalid

  Scenario: A url cannot be added to a shop that does not exist
    Given shop "unknown_shop" does not exist
    When I add a shop url "orphan_url" with the following properties:
      | shop   | unknown_shop |
      | domain | orphan.test  |
    Then I should get error that shop was not found

  Scenario: A main url cannot be added disabled
    When I add a shop url "disabled_main_url" with the following properties:
      | shop   | shop1              |
      | domain | disabled-main.test |
      | main   | true               |
      | active | false              |
    Then I should get error that main shop url must be active

  Scenario: Edit a shop url
    When I edit shop url "secondary_url" with the following properties:
      | domain      | edited.test |
      | domain_ssl  | edited.test |
      | virtual_uri | boots       |
    Then shop url "secondary_url" should have the following properties:
      | domain       | edited.test |
      | domain_ssl   | edited.test |
      | physical_uri | /store/     |
      | virtual_uri  | boots/      |
      | main         | false       |

  Scenario: Toggle the status of a secondary url
    When I toggle the status of shop url "secondary_url"
    Then shop url "secondary_url" should have the following properties:
      | active | false |
    When I toggle the status of shop url "secondary_url"
    Then shop url "secondary_url" should have the following properties:
      | active | true |

  Scenario: The main url cannot be disabled
    When I toggle the status of shop url "main_url"
    Then I should get error that main shop url must be active
    When I edit shop url "main_url" with the following properties:
      | active | false |
    Then I should get error that main shop url must be active
    And shop url "main_url" should have the following properties:
      | active | true |

  Scenario: The main url cannot be turned into a secondary url
    When I edit shop url "main_url" with the following properties:
      | main | false |
    Then I should get error that main shop url cannot be unset
    And shop url "main_url" should have the following properties:
      | main | true |

  Scenario: Setting a url as main demotes the previous main url
    When I edit shop url "secondary_url" with the following properties:
      | main | true |
    Then shop url "secondary_url" should have the following properties:
      | main | true |
    And shop url "main_url" should have the following properties:
      | main | false |

  Scenario: The main url cannot be deleted
    When I delete shop url "secondary_url"
    Then I should get error that main shop url cannot be deleted
    And shop url "secondary_url" should have the following properties:
      | main | true |

  Scenario: Delete a secondary url
    When I delete shop url "main_url"
    Then shop url "main_url" should not exist

  Scenario: Deleting a shop url that does not exist
    Given shop url "unknown_url" does not exist
    When I delete shop url "unknown_url"
    Then I should get error that shop url was not found

  Scenario: The first url of a shop becomes its main url
    Given I add a shop "shop2" with name "Second shop" and color "red" for the group "default_group"
    When I add a shop url "shop2_first_url" with the following properties:
      | shop   | shop2            |
      | domain | second-shop.test |
      | main   | false            |
    Then shop url "shop2_first_url" should have the following properties:
      | main | true |

  Scenario: Adding a main url demotes the previous main url of the shop
    When I add a shop url "shop2_new_main_url" with the following properties:
      | shop   | shop2                |
      | domain | second-shop-new.test |
      | main   | true                 |
    Then shop url "shop2_new_main_url" should have the following properties:
      | main | true |
    And shop url "shop2_first_url" should have the following properties:
      | main | false |

  Scenario: Toggling the main flag of a main url is refused
    When I toggle the main flag of shop url "shop2_new_main_url"
    Then I should get error that main shop url cannot be unset
    And shop url "shop2_new_main_url" should have the following properties:
      | main | true |

  Scenario: Setting a disabled url as main also enables it
    When I toggle the status of shop url "shop2_first_url"
    Then shop url "shop2_first_url" should have the following properties:
      | main   | false |
      | active | false |
    When I toggle the main flag of shop url "shop2_first_url"
    Then shop url "shop2_first_url" should have the following properties:
      | main   | true |
      | active | true |
    And shop url "shop2_new_main_url" should have the following properties:
      | main | false |
    When I toggle the status of shop url "shop2_first_url"
    Then I should get error that main shop url must be active

  Scenario: A main url cannot be moved to another shop
    When I edit shop url "shop2_first_url" with the following properties:
      | shop | shop1 |
    Then I should get error that main shop url cannot be unset
    And shop url "shop2_first_url" should have the following properties:
      | shop | shop2 |
      | main | true  |

  Scenario: A secondary url can be moved to another shop
    When I edit shop url "shop2_new_main_url" with the following properties:
      | shop | shop1 |
    Then shop url "shop2_new_main_url" should have the following properties:
      | shop | shop1 |
      | main | false |
    And shop url "shop2_first_url" should have the following properties:
      | shop | shop2 |
      | main | true  |
