@reset-database-before-feature
Feature: Generating invoice for Order
  As a merchant
  I must be able to generate invoice for Order

  Scenario: Generating invoice for Order that does not have any invoices
    Given there is order with reference "XKBKNABJK"
    And order "XKBKNABJK" does not have any invoices
    When I generate invoice for "XKBKNABJK" order
    Then order "XKBKNABJK" should have invoice
