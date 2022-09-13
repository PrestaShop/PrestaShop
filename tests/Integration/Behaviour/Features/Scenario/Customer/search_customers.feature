# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags search-customers
@restore-all-tables-before-feature
@clear-cache-before-feature
@search-customers

Feature: Search customers given a search term (BO)
  As a BO user
  I want to get a list of customers for a given search term

  Scenario: I search for existing customers
    Given shop configuration for "PS_B2B_ENABLE" is set to 1
    When I create customer "CUST-1" with following details:
      | firstName   | Mathieu                    |
      | lastName    | Napoler                    |
      | email       | napoler.dev@prestashop.com |
      | password    | PrestaShopForever1_!       |
      | birthday    | 1996-05-04                 |
      | companyName | Test company               |
    When I create customer "CUST-2" with following details:
      | firstName   | Mathieu                   |
      | lastName    | Polarn                    |
      | email       | polarn.dev@prestashop.com |
      | password    | PrestaShopForever1_!      |
      | birthday    | 1998-10-12                |
      | companyName | Test company              |
    When I search for the phrases "mathieu napoler" I should get the following results:
      | firstName | lastName | email                      | birthday   | companyName  |
      | Mathieu   | Napoler  | napoler.dev@prestashop.com | 1996-05-04 | Test company |
    When I search for the phrases "mathieu polarn" I should get the following results:
      | firstName | lastName | email                     | birthday   | companyName  |
      | Mathieu   | Polarn   | polarn.dev@prestashop.com | 1998-10-12 | Test company |
    When I search for the phrases "test" I should get the following results:
      | firstName | lastName | email                      | birthday   | companyName  |
      | Mathieu   | Napoler  | napoler.dev@prestashop.com | 1996-05-04 | Test company |
      | Mathieu   | Polarn   | polarn.dev@prestashop.com  | 1998-10-12 | Test company |
    When I search for the phrases "TEST" I should get the following results:
      | firstName | lastName | email                      | birthday   | companyName  |
      | Mathieu   | Napoler  | napoler.dev@prestashop.com | 1996-05-04 | Test company |
      | Mathieu   | Polarn   | polarn.dev@prestashop.com  | 1998-10-12 | Test company |
    When I search for the phrases "no customers" I should not get any results
    When I search for the phrases "no companies" I should not get any results
    When I search for the phrases " " I should not get any results

    Given shop configuration for "PS_B2B_ENABLE" is set to 0
    When I search for the phrases "prestashop" I should get the following results:
      | firstName | lastName | email                      | birthday   |
      | Mathieu   | Napoler  | napoler.dev@prestashop.com | 1996-05-04 |
      | Mathieu   | Polarn   | polarn.dev@prestashop.com  | 1998-10-12 |
    When I search for the phrases "test" I should not get any results
