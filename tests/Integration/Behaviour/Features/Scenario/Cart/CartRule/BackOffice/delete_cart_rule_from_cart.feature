# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-delete-cart-rule
@restore-all-tables-before-feature
@bo-delete-cart-rule
Feature: Delete cart rule from cart in Back Office (BO)
  As a BO user I must be able to delete cart rules from cart
  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"

  Scenario: Delete cart rule with gift product
    Given I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And product "Mug The best is yet to come" quantity in cart "dummy_cart" should be 2 excluding gift products
    And I use a voucher "giftFoxNotebook" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart"
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart" should be 1
    When I delete voucher "giftFoxNotebook" from cart "dummy_cart"
    Then cart "dummy_cart" should not contain gift product "Mountain fox notebook"

  Scenario: Delete cart rule with gift product when same product as gift already exists in cart
    Given I create an empty cart "dummy_cart_2" for customer "testCustomer"
    And I add 3 products "Mountain fox notebook" to the cart "dummy_cart_2"
    And product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 3 excluding gift products
    And I use a voucher "giftFoxNotebook" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart_2"
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 1
    When I delete voucher "giftFoxNotebook" from cart "dummy_cart_2"
    Then product "Mountain fox notebook" quantity in cart "dummy_cart_2" should be 3 excluding gift products
    But cart "dummy_cart_2" should not contain gift product "Mountain fox notebook"

  # An automatic voucher (one without a code) is already attached to the cart by
  # CartRule::autoAddToCart() before the back office can add it explicitly.
  Scenario: Adding a cart rule that is already applied to the cart is not an error
    Given I create an empty cart "dummy_cart_3" for customer "testCustomer"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart_3"
    When I use an automatic voucher "autoGiftFoxNotebook" which provides a gift product "Mountain fox notebook" on the cart "dummy_cart_3"
    Then cart "dummy_cart_3" should contain gift product "Mountain fox notebook"
    And gifted product "Mountain fox notebook" quantity in cart "dummy_cart_3" should be 1

  # The escape above must be limited to the rule that is already applied: a voucher the cart
  # does not have and cannot use must still be refused.
  Scenario: Adding a cart rule the cart cannot use is still an error
    Given I create an empty cart "dummy_cart_4" for customer "testCustomer"
    And I add 1 products "Mug The best is yet to come" to the cart "dummy_cart_4"
    And there is a cart rule "expired_rule" with following properties:
      | name[en-US]     | Expired voucher  |
      | description     | Expired voucher  |
      | priority        | 2                |
      | code            | EXPIRED_VOUCHER  |
      | discount_amount | 2                |
      | valid_from      | 2020-01-01 00:00:00 |
      | valid_to        | 2020-01-02 00:00:00 |
    When I use a voucher "expired_rule" on the cart "dummy_cart_4"
    Then I should get a cart rule validity error saying "expired"
    And voucher "expired_rule" should not be applied to cart "dummy_cart_4"
