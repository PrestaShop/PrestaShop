# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags related-products
@reset-database-before-feature
@related-products
Feature: Update product related products from Back Office (BO)
  As an employee
  I need to be able to update related products of a product from Back Office

  Scenario: I set related products
    When I add product "product1" with following information:
      | name       | en-US:book of law;fr-Fr:livre de droit |
      | is_virtual | false                |
    Then product product1 should have no related products
