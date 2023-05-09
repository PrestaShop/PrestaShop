@restore-all-tables-before-feature
Feature: Cart calculation with tax
  As a customer
  I must be able to have correct cart total when using taxes

  Scenario: empty cart
    Given I have an empty default cart
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    When I select address "address1" in my cart
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included
    Then my cart total should be 0.0 tax excluded
    Then my cart total using previous calculation method should be 0.0 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 0.0 tax included
    Then my cart total using previous calculation method should be 0.0 tax included
    Then my cart total should be 0.0 tax excluded
    Then my cart total using previous calculation method should be 0.0 tax excluded

  Scenario: tax #1: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    When I add 1 items of product "product1" in my cart
    When I select address "address1" in my cart
    Then my cart total should be 20.60448 tax included
    Then my cart total using previous calculation method should be 20.60448 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded

  Scenario: tax #2: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 6.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product5" belongs to tax group "taxrule1"
    When I add 1 items of product "product5" in my cart
    When I select address "address1" in my cart
    Then my cart total should be 21.00072 tax included
    Then my cart total using previous calculation method should be 21.00072 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded

  Scenario: tax #3: one product in cart, quantity 1
    Given I have an empty default cart
    Given there is a product in the catalog named "product5" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 0.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product5" belongs to tax group "taxrule1"
    When I add 1 items of product "product5" in my cart
    When I select address "address1" in my cart
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 19.812 tax included
    Then my cart total using previous calculation method should be 19.812 tax included
    Then my cart total should be 19.812 tax excluded
    Then my cart total using previous calculation method should be 19.812 tax excluded

  Scenario: tax #1: one product in cart, quantity 3
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    When I add 3 items of product "product1" in my cart
    When I select address "address1" in my cart
    Then my cart total should be 61.81344 tax included
    Then my cart total using previous calculation method should be 61.81344 tax included
    Then my cart total should be 59.436 tax excluded
    Then my cart total using previous calculation method should be 59.436 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 59.436 tax included
    Then my cart total using previous calculation method should be 59.436 tax included
    Then my cart total should be 59.436 tax excluded
    Then my cart total using previous calculation method should be 59.436 tax excluded

  Scenario: multiple taxes, multiple products, several quantities
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given there is a tax named "tax2" and rate 6.0%
    Given there is a tax rule named "taxrule2" in country "country1" and state "state1" where tax "tax2" is applied
    Given product "product2" belongs to tax group "taxrule2"
    Given there is a tax named "tax3" and rate 10.0%
    Given there is a tax rule named "taxrule3" in country "country1" and state "state1" where tax "tax3" is applied
    Given product "product3" belongs to tax group "taxrule3"
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product2" in my cart
    When I add 2 items of product "product3" in my cart
    When I select address "address1" in my cart
    Then my cart total should be 164.75832 tax included
    Then my cart total using previous calculation method should be 164.75832 tax included
    Then my cart total should be 154.2 tax excluded
    Then my cart total using previous calculation method should be 154.2 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 154.2 tax included
    Then my cart total using previous calculation method should be 154.2 tax included
    Then my cart total should be 154.2 tax excluded
    Then my cart total using previous calculation method should be 154.2 tax excluded

  Scenario: multiple taxes, multiple products, several quantities, one tax is null
    Given I have an empty default cart
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1" in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given there is a tax named "tax2" and rate 0.0%
    Given there is a tax rule named "taxrule2" in country "country1" and state "state1" where tax "tax2" is applied
    Given product "product2" belongs to tax group "taxrule2"
    Given there is a tax named "tax3" and rate 10.0%
    Given there is a tax rule named "taxrule3" in country "country1" and state "state1" where tax "tax3" is applied
    Given product "product3" belongs to tax group "taxrule3"
    When I add 3 items of product "product1" in my cart
    When I add 1 items of product "product2" in my cart
    When I add 2 items of product "product3" in my cart
    When I select address "address1" in my cart
    Then my cart total should be 162.81504 tax included
    Then my cart total using previous calculation method should be 162.81504 tax included
    Then my cart total should be 154.2 tax excluded
    Then my cart total using previous calculation method should be 154.2 tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    Then my cart total should be 154.2 tax included
    Then my cart total using previous calculation method should be 154.2 tax included
    Then my cart total should be 154.2 tax excluded
    Then my cart total using previous calculation method should be 154.2 tax excluded
