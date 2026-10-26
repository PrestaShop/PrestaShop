# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-customers-multishop-scope
@restore-all-tables-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@webservice-endpoints-customers-multishop-scope
Feature: Webservice multishop scope
  Webservice keys must respect multishop boundaries and the sharing rules of shop groups.

  Background:
    Given I enable multishop feature
    And I restore tables "webservice_account,webservice_account_shop,webservice_permission"
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "Shop Two" and color "#00ff00" for the group "default_shop_group"
    And I add a shop group "other_shop_group" with name "Other group"
    And I add a shop "shop3" with name "Shop Three" and color "#0000ff" for the group "other_shop_group"
    And Shop group "default_shop_group" does not share its customers
    And Shop group "default_shop_group" does not share its orders

  # First scenario: the Background adds new shops on each run, and id_shop=all needs a key on every shop
  Scenario: id_shop=all writes to every shop when the key is allowed on all of them
    Given I add a new webservice key with specified properties:
      | key              | ALLSHOPSALLSHOPSALLSHOPSALLSHOPS |
      | description      | All shops                        |
      | is_enabled       | 1                                |
      | shop_association | shop1,shop2,shop3                |
      | permission_GET   | customers,products               |
      | permission_PUT   | products                         |
    And shop context "test_shop" is loaded
    And there is a customer named "customer_all_shop1" whose email is "customer-all-shop1@example.com"
    And shop context "Shop Three" is loaded
    And there is a customer named "customer_all_shop3" whose email is "customer-all-shop3@example.com"
    When I use Webservice with key "ALLSHOPSALLSHOPSALLSHOPSALLSHOPS" to list "customers?display=full" with "id_shop" value "all"
    Then I should get 0 errors
    And webservice response should contain text "customer-all-shop1@example.com"
    And webservice response should contain text "customer-all-shop3@example.com"
    Given shop context "test_shop" is loaded
    And I add product "product1" to shop "shop1" with following information:
      | name[en-US] | multishop webservice product |
      | type        | standard                     |
    And I set following shops for product "product1":
      | source shop | shop1             |
      | shops       | shop1,shop2,shop3 |
    When I use Webservice with key "ALLSHOPSALLSHOPSALLSHOPSALLSHOPS" to update "products" for reference "product1" with "id_shop" value "all"
      | key   | value |
      | price | 77    |
    Then I should get 0 errors
    And product "product1" should have following prices information for shops "shop1,shop2,shop3":
      | price | 77 |

  Scenario: A single-shop key cannot reach another shop, nor a customer of another shop
    Given I add a new webservice key with specified properties:
      | key               | SHOPONEONLYSHOPONEONLYSHOPONEONL |
      | description       | Shop one only                    |
      | is_enabled        | 1                                |
      | shop_association  | shop1                            |
      | permission_GET    | customers                        |
      | permission_PUT    | customers                        |
      | permission_POST   | customers                        |
      | permission_DELETE | customers                        |
    And shop context "test_shop" is loaded
    And there is a customer named "customer_shop1" whose email is "customer-shop1@example.com"
    And shop context "Shop Three" is loaded
    And there is a customer named "customer_shop3" whose email is "customer-shop3@example.com"
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to view "customers" for reference "customer_shop3" with "id_shop" from shop reference "shop3"
    Then I should get 1 error
    And I should get an error with code 132
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to list "customers?display=full" with "id_shop" value "all"
    Then I should get 1 error
    And I should get an error with code 132
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to list "customers?display=full" with "id_shop_group" from group reference "default_shop_group"
    Then I should get 1 error
    And I should get an error with code 132
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to view "customers" for reference "customer_shop3" with "id_shop" from shop reference "shop1"
    Then I should get 1 error
    And I should get an error with code 131
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to update "customers" for reference "customer_shop3" with "id_shop" from shop reference "shop1"
      | key       | value                      |
      | firstname | Jane                       |
      | lastname  | Doe                        |
      | email     | customer-shop3@example.com |
    Then I should get 1 error
    And I should get an error with code 131
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to remove "customers" with reference "customer_shop3" and "id_shop" from shop reference "shop1"
    Then I should get 1 error
    And I should get an error with code 87
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to view "customers" for reference "customer_shop3" with "id_shop" from shop reference "shop1"
    Then I should get an error with code 131
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to view "customers" for reference "customer_shop1" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to add "customers" for reference "customer_posted_shop3" with "id_shop" from shop reference "shop1"
      | key       | value                             | reference |
      | id_shop   |                                   | shop3     |
      | firstname | Jane                              |           |
      | lastname  | Doe                               |           |
      | email     | customer-posted-shop3@example.com |           |
      | passwd    | Pr3st4Sh0P-passwd                 |           |
    Then I should get 1 error
    And I should get an error with code 132
    When I use Webservice with key "SHOPONEONLYSHOPONEONLYSHOPONEONL" to update "customers" for reference "customer_shop1" with "id_shop" from shop reference "shop1"
      | key       | value                      | reference |
      | id_shop   |                            | shop3     |
      | firstname | Jane                       |           |
      | lastname  | Doe                        |           |
      | email     | customer-shop1@example.com |           |
      | passwd    | Pr3st4Sh0P-passwd          |           |
    Then I should get 1 error
    And I should get an error with code 132

  Scenario: Customers of the other shops of a group are reachable only when the group shares its customers
    Given I add a new webservice key with specified properties:
      | key              | SHAREDCUSTOMERSSHAREDCUSTOMERSSH |
      | description      | Shop one, shared customers       |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
      | permission_GET   | customers                        |
    And shop context "Shop Two" is loaded
    And there is a customer named "customer_shop2" whose email is "customer-shop2@example.com"
    When I use Webservice with key "SHAREDCUSTOMERSSHAREDCUSTOMERSSH" to view "customers" for reference "customer_shop2" with "id_shop" from shop reference "shop1"
    Then I should get 1 error
    And I should get an error with code 131
    Given Shop group "default_shop_group" shares its customers
    When I use Webservice with key "SHAREDCUSTOMERSSHAREDCUSTOMERSSH" to view "customers" for reference "customer_shop2" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
    And Shop group "default_shop_group" does not share its customers

  Scenario: Sharing customers does not expose the carts of the other shops of the group
    Given I add a new webservice key with specified properties:
      | key              | SHAREDORDERSSHAREDORDERSSHAREDOR |
      | description      | Shop one, carts                  |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
      | permission_GET   | carts                            |
    And Shop group "default_shop_group" shares its customers
    And shop context "Shop Two" is loaded
    And there is a customer named "cart_owner_shop2" whose email is "cart-owner-shop2@example.com"
    And I create an empty cart "cart_shop2" for customer "cart_owner_shop2"
    When I use Webservice with key "SHAREDORDERSSHAREDORDERSSHAREDOR" to view "carts" for reference "cart_shop2" with "id_shop" from shop reference "shop1"
    Then I should get 1 error
    And I should get an error with code 131
    When I use Webservice with key "SHAREDORDERSSHAREDORDERSSHAREDOR" to list "carts?display=full" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
    And webservice list should not contain the record with reference "cart_shop2"
    Given Shop group "default_shop_group" shares its stock
    And Shop group "default_shop_group" shares its orders
    When I use Webservice with key "SHAREDORDERSSHAREDORDERSSHAREDOR" to view "carts" for reference "cart_shop2" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
    When I use Webservice with key "SHAREDORDERSSHAREDORDERSSHAREDOR" to list "carts?display=full" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
    And webservice list should contain the record with reference "cart_shop2"
    And Shop group "default_shop_group" does not share its customers
    And Shop group "default_shop_group" does not share its orders
    And Shop group "default_shop_group" does not share its stock

  Scenario: The details of an order of another shop of the group follow the order when the group shares its orders
    Given I add a new webservice key with specified properties:
      | key              | SHAREDDETAILSSHAREDDETAILSSHARED |
      | description      | Shop two, order details          |
      | is_enabled       | 1                                |
      | shop_association | shop2                            |
      | permission_GET   | orders,order_details             |
    And the order with reference "XKBKNABJK" is stored as "order_shop1" and its first detail as "order_detail_shop1"
    When I use Webservice with key "SHAREDDETAILSSHAREDDETAILSSHARED" to view "order_details" for reference "order_detail_shop1" with "id_shop" from shop reference "shop2"
    Then I should get 1 error
    And I should get an error with code 131
    When I use Webservice with key "SHAREDDETAILSSHAREDDETAILSSHARED" to list "order_details?display=full" with "id_shop" from shop reference "shop2"
    Then I should get 0 errors
    And webservice list should not contain the record with reference "order_detail_shop1"
    Given Shop group "default_shop_group" shares its customers
    And Shop group "default_shop_group" shares its stock
    And Shop group "default_shop_group" shares its orders
    When I use Webservice with key "SHAREDDETAILSSHAREDDETAILSSHARED" to view "orders" for reference "order_shop1" with "id_shop" from shop reference "shop2"
    Then I should get 0 errors
    When I use Webservice with key "SHAREDDETAILSSHAREDDETAILSSHARED" to view "order_details" for reference "order_detail_shop1" with "id_shop" from shop reference "shop2"
    Then I should get 0 errors
    When I use Webservice with key "SHAREDDETAILSSHAREDDETAILSSHARED" to list "order_details?display=full" with "id_shop" from shop reference "shop2"
    Then I should get 0 errors
    And webservice list should contain the record with reference "order_detail_shop1"
    And Shop group "default_shop_group" does not share its customers
    And Shop group "default_shop_group" does not share its orders
    And Shop group "default_shop_group" does not share its stock

  Scenario: A single-shop key cannot reach the addresses of the customers of another shop
    Given I add a new webservice key with specified properties:
      | key               | ADDRESSESONLYADDRESSESONLYADDRES |
      | description       | Shop one addresses               |
      | is_enabled        | 1                                |
      | shop_association  | shop1                            |
      | permission_GET    | addresses                        |
      | permission_PUT    | addresses                        |
      | permission_POST   | addresses                        |
      | permission_DELETE | addresses                        |
    And shop context "test_shop" is loaded
    And there is a customer named "address_owner_shop1" whose email is "address-owner-shop1@example.com"
    And shop context "Shop Three" is loaded
    And there is a customer named "address_owner_shop3" whose email is "address-owner-shop3@example.com"
    And shop context "test_shop" is loaded
    And I add new address to customer "address_owner_shop1" with following details:
      | Address alias | address_shop1       |
      | First name    | John                |
      | Last name     | Doe                 |
      | Address       | 1 shop one street   |
      | City          | Paris               |
      | Country       | France              |
      | Postal code   | 75001               |
    And I add new address to customer "address_owner_shop3" with following details:
      | Address alias | address_shop3       |
      | First name    | Jane                |
      | Last name     | Doe                 |
      | Address       | 3 shop three street |
      | City          | Paris               |
      | Country       | France              |
      | Postal code   | 75003               |
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to view "addresses" for reference "address_shop3" with "id_shop" from shop reference "shop1"
    Then I should get 1 error
    And I should get an error with code 131
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to list "addresses?display=full" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
    And webservice response should contain text "1 shop one street"
    And webservice response should not contain text "3 shop three street"
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to update "addresses" for reference "address_shop3" with "id_shop" from shop reference "shop1"
      | key         | value             | reference           |
      | id_customer |                   | address_owner_shop3 |
      | id_country  | 8                 |                     |
      | alias       | address_shop3     |                     |
      | firstname   | Jane              |                     |
      | lastname    | Doe               |                     |
      | address1    | 3 rewritten       |                     |
      | city        | Paris             |                     |
      | postcode    | 75003             |                     |
    Then I should get 1 error
    And I should get an error with code 131
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to remove "addresses" with reference "address_shop3" and "id_shop" from shop reference "shop1"
    Then I should get 1 error
    And I should get an error with code 87
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to add "addresses" for reference "address_posted_shop3" with "id_shop" from shop reference "shop1"
      | key         | value             | reference           |
      | id_customer |                   | address_owner_shop3 |
      | id_country  | 8                 |                     |
      | alias       | posted            |                     |
      | firstname   | Jane              |                     |
      | lastname    | Doe               |                     |
      | address1    | 3 posted street   |                     |
      | city        | Paris             |                     |
      | postcode    | 75003             |                     |
    Then I should get 1 error
    And I should get an error with code 132
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to update "addresses" for reference "address_shop1" with "id_shop" from shop reference "shop1"
      | key         | value             | reference           |
      | id_customer |                   | address_owner_shop3 |
      | id_country  | 8                 |                     |
      | alias       | address_shop1     |                     |
      | firstname   | John              |                     |
      | lastname    | Doe               |                     |
      | address1    | 1 shop one street |                     |
      | city        | Paris             |                     |
      | postcode    | 75001             |                     |
    Then I should get 1 error
    And I should get an error with code 132
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to add "addresses" for reference "address_posted_shop1" with "id_shop" from shop reference "shop1"
      | key         | value             | reference           |
      | id_customer |                   | address_owner_shop1 |
      | id_country  | 8                 |                     |
      | alias       | posted            |                     |
      | firstname   | John              |                     |
      | lastname    | Doe               |                     |
      | address1    | 1 posted street   |                     |
      | city        | Paris             |                     |
      | postcode    | 75001             |                     |
    Then I should get 0 errors
    When I use Webservice with key "ADDRESSESONLYADDRESSESONLYADDRES" to view "addresses" for reference "address_shop1" with "id_shop" from shop reference "shop1"
    Then I should get 0 errors
