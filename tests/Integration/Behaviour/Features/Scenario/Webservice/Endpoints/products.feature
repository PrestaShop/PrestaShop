# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-products
@restore-all-tables-before-feature
@webservice-endpoints-products
Feature: Webservice product listing
  PrestaShop exposes products over the webservice
  As a webservice consumer
  I must be able to filter a listing on the same value the listing reports

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore tables "webservice_account,webservice_account_shop,webservice_permission"
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And I add a new webservice key with specified properties:
      | key              | ENABLEDENABLEDENABLEDENABLEDENAB |
      | description      | Enabled key                      |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
    And I edit webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" with specified properties:
      | description    | Enabled key with Permissions |
      | permission_GET | products                     |

  # A shop scoped field lives in both `product` and `product_shop`. The listing joins the shop table
  # and reports its value, so a filter has to be qualified with the same table or it answers about a
  # different column. The two values below are set apart to tell the two tables from each other.
  Scenario: Filtering a shop scoped field matches the value the listing reports
    Given I set "date_upd" of Product 1 to "2001-01-01 00:00:00" in the base table and "2038-01-01 00:00:00" in the shop table
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "products" filtered by "date_upd" with value "2038-01-01 00:00:00"
    Then I should get 1 item of type "product"

  Scenario: Filtering a shop scoped field does not match the value only the base table holds
    Given I set "date_upd" of Product 1 to "2001-01-01 00:00:00" in the base table and "2038-01-01 00:00:00" in the shop table
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "products" filtered by "date_upd" with value "2001-01-01 00:00:00"
    Then I should get 0 items of type "product"

  # The non shop scoped fields must keep being filtered on the base table.
  Scenario: Filtering a field that is not shop scoped still works
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "products" filtered by "id" with value "1"
    Then I should get 1 item of type "product"
