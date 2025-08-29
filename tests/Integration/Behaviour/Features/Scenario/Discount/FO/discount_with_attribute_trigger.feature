# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-with-attribute-trigger
@restore-all-tables-before-feature
@discount-with-attribute-trigger
Feature: Add discount with attribute trigger on FO
  PrestaShop allows discounts with restricted products as the condition

  Background:
    Given there is a customer named "testCustomer" whose email is "pub@prestashop.com"
    Given language with iso code "en" is the default one
    And language "french" with locale "fr-FR" exists
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given attribute group "Size" named "Size" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Camel" named "Camel" in en language exists
    And attribute "Orange" named "Orange" in en language exists

  #
  # This is an initialisation step, but we do not do it in the background because background steps are repeated
  # for all scenarios, and we want to keep the same product data for more efficiency
  #
  Scenario: First create products that will be used in following scenarios
    # One product with multiple attributes but from one group
    Given I add product "metalTshirt" with following information:
      | name[en-US] | metal tshirt |
      | type        | combinations |
    When I generate combinations for product metalTshirt using following attributes:
      | Size | [S,M,L] |
    Then product "metalTshirt" should have following combinations:
      | id reference | combination name | reference | attributes | impact on price | quantity | is default |
      | metalTshirtS | Size - S         |           | [Size:S]   | 0               | 0        | true       |
      | metalTshirtM | Size - M         |           | [Size:M]   | 0               | 0        | false      |
      | metalTshirtL | Size - L         |           | [Size:L]   | 0               | 0        | false      |
    And I enable product "metalTshirt"
    And I update product "metalTshirt" with following values:
      | price | 12.00 |
    And I update combination "metalTshirtS" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalTshirtM" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalTshirtL" stock with following details:
      | delta quantity | 100 |
    Given I add product "metalSweat" with following information:
      | name[en-US] | metal sweat  |
      | type        | combinations |
    # One product with multiple attributes from multiple groups
    When I generate combinations for product metalSweat using following attributes:
      | Size  | [S,M,L]            |
      | Color | [Red,Black,Orange] |
    Then product "metalSweat" should have following combinations:
      | id reference      | combination name         | reference | attributes            | impact on price | quantity | is default |
      | metalSweatSRed    | Size - S, Color - Red    |           | [Size:S,Color:Red]    | 0               | 0        | true       |
      | metalSweatSBlack  | Size - S, Color - Black  |           | [Size:S,Color:Black]  | 0               | 0        | false      |
      | metalSweatSOrange | Size - S, Color - Orange |           | [Size:S,Color:Orange] | 0               | 0        | false      |
      | metalSweatMRed    | Size - M, Color - Red    |           | [Size:M,Color:Red]    | 0               | 0        | false      |
      | metalSweatMBlack  | Size - M, Color - Black  |           | [Size:M,Color:Black]  | 0               | 0        | false      |
      | metalSweatMOrange | Size - M, Color - Orange |           | [Size:M,Color:Orange] | 0               | 0        | false      |
      | metalSweatLRed    | Size - L, Color - Red    |           | [Size:L,Color:Red]    | 0               | 0        | false      |
      | metalSweatLBlack  | Size - L, Color - Black  |           | [Size:L,Color:Black]  | 0               | 0        | false      |
      | metalSweatLOrange | Size - L, Color - Orange |           | [Size:L,Color:Orange] | 0               | 0        | false      |
    And I enable product "metalSweat"
    And I update product "metalSweat" with following values:
      | price | 18.00 |
    And I update combination "metalSweatSRed" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatMRed" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatLRed" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatSBlack" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatMBlack" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatLBlack" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatSOrange" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatMOrange" stock with following details:
      | delta quantity | 100 |
    And I update combination "metalSweatLOrange" stock with following details:
      | delta quantity | 100 |

  Scenario: Test discount that gives a cart level discount when product matches two out of three attributes is selected
    When I create a "cart_level" discount "discount_with_two_attributes" with following properties:
      | name[en-US]        | Promotion                    |
      | name[fr-FR]        | Promotion                    |
      | code               | DISCOUNT_WITH_TWO_ATTRIBUTES |
      | active             | true                         |
      | reduction_amount   | 3.0                          |
      | reduction_currency | usd                          |
      | taxIncluded        | true                         |
    When I update discount "discount_with_two_attributes" with following conditions matching at least 1 products:
      | condition_type | items |
      | attributes     | S,L   |
    Then discount "discount_with_two_attributes" should have the following product conditions matching at least 1 products:
      | condition_type | items |
      | attributes     | S,L   |
    Given I create an empty cart "first_cart" for customer "testCustomer"
    When I add 1 combination "metalTshirtM" from product "metalTshirt" to the cart "first_cart"
    And cart "first_cart" total with tax included should be '$19.00'
    And my cart "first_cart" should have the following details:
      | total_products | $12.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $19.00 |
    # No combination matches S or L
    When I use a voucher "discount_with_two_attributes" on the cart "first_cart"
    Then I should get cart rule validation error
    And my cart "first_cart" should have the following details:
      | total_products | $12.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $19.00 |
    # Remove the M tshirt and replace it with an S
    Then I delete combination metalTshirtM from product metalTshirt from cart first_cart
    Then I add 1 combination "metalTshirtS" from product "metalTshirt" to the cart "first_cart"
    And cart "first_cart" total with tax included should be '$19.00'
    And my cart "first_cart" should have the following details:
      | total_products | $12.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $19.00 |
    # Now I re-try the discount and it should work
    When I use a voucher "discount_with_two_attributes" on the cart "first_cart"
    And cart "first_cart" total with tax included should be '$16.00'
    And my cart "first_cart" should have the following details:
      | total_products | $12.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $16.00 |
    #
    # Now update the discount so that it needs to match 2 products
    #
    When I update discount "discount_with_two_attributes" with following conditions matching at least 2 products:
      | condition_type | items |
      | attributes     | S,L   |
    Then discount "discount_with_two_attributes" should have the following product conditions matching at least 2 products:
      | condition_type | items |
      | attributes     | S,L   |
    # Create a new cart (we need to create a new one because the first cart was not updated and still has the discount applied
    # even if the quantity no longer matches)
    Given I create an empty cart "second_cart" for customer "testCustomer"
    # One product matching the condition is not enough
    And I add 1 combination "metalTshirtS" from product "metalTshirt" to the cart "second_cart"
    When I use a voucher "discount_with_two_attributes" on the cart "second_cart"
    Then I should get cart rule validation error
    # If I add a second product matching the other attribute then we're good
    Then I add 1 combination "metalTshirtL" from product "metalTshirt" to the cart "second_cart"
    When I use a voucher "discount_with_two_attributes" on the cart "second_cart"
    Then cart "second_cart" total with tax included should be '$28.00'
    And my cart "second_cart" should have the following details:
      | total_products | $24.00 |
      | shipping       | $7.00  |
      | total_discount | -$3.00 |
      | total          | $28.00 |

  # This scenario is not possible with the form right now, but we check its validity for a more generic validation of the feature
  # This kind of conditions could be created by alternative modules or via the Admin API
  Scenario: Test discount that gives a cart level discount when attributes match but from different groups
    When I create a "cart_level" discount "discount_with_different_groups" with following properties:
      | name[en-US]        | Promotion                      |
      | name[fr-FR]        | Promotion                      |
      | code               | DISCOUNT_WITH_DIFFERENT_GROUPS |
      | active             | true                           |
      | reduction_amount   | 4.0                            |
      | reduction_currency | usd                            |
      | taxIncluded        | true                           |
    When I update discount "discount_with_different_groups" with following conditions matching at least 2 products:
      | condition_type | items   |
      | attributes     | S,L,Red |
    Then discount "discount_with_different_groups" should have the following product conditions matching at least 2 products:
      | condition_type | items   |
      | attributes     | S,L,Red |
    Given I create an empty cart "third_cart" for customer "testCustomer"
    # It works with two combinations matching one of the attributes
    When I add 2 combination "metalTshirtL" from product "metalTshirt" to the cart "third_cart"
    And cart "third_cart" total with tax included should be '$31.00'
    Then my cart "third_cart" should have the following details:
      | total_products | $24.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $31.00 |
    # Apply the discount
    When I use a voucher "discount_with_different_groups" on the cart "third_cart"
    And cart "third_cart" total with tax included should be '$27.00'
    And my cart "third_cart" should have the following details:
      | total_products | $24.00 |
      | shipping       | $7.00  |
      | total_discount | -$4.00 |
      | total          | $27.00 |
    # Remove the L tshirt and replace it with one S and one redM sweat (match thanks to red not M)
    Then I delete combination metalTshirtL from product metalTshirt from cart third_cart
    And I add 1 combination "metalTshirtS" from product "metalTshirt" to the cart "third_cart"
    And I add 1 combination "metalSweatMRed" from product "metalSweat" to the cart "third_cart"
    # Discount has been removed (when the initial product was removed) and cart is updated
    And cart "third_cart" total with tax included should be '$37.00'
    And my cart "third_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $37.00 |
    # The condition works for two different attributes, even when they are from different groups
    When I use a voucher "discount_with_different_groups" on the cart "third_cart"
    Then cart "third_cart" total with tax included should be '$33.00'
    And my cart "third_cart" should have the following details:
      | total_products | $30.00 |
      | shipping       | $7.00  |
      | total_discount | -$4.00 |
      | total          | $33.00 |

  Scenario: Test discount that gives a cart level discount with a product L AND Red
    When I create a "cart_level" discount "discount_with_l_and_red" with following properties:
      | name[en-US]        | Promotion               |
      | name[fr-FR]        | Promotion               |
      | code               | DISCOUNT_WITH_L_AND_RED |
      | active             | true                    |
      | reduction_amount   | 5.0                     |
      | reduction_currency | usd                     |
      | taxIncluded        | true                    |
    When I update discount "discount_with_l_and_red" with following conditions matching at least 1 products:
      | condition_type | items |
      | attributes     | L     |
      | attributes     | Red   |
    Then discount "discount_with_l_and_red" should have the following product conditions matching at least 1 products:
      | condition_type | items |
      | attributes     | L     |
      | attributes     | Red   |
    Given I create an empty cart "fourth_cart" for customer "testCustomer"
    # Product with only L is not enough (tshirt has no color so it can never match with Red)
    When I add 1 combination "metalTshirtL" from product "metalTshirt" to the cart "fourth_cart"
    And cart "fourth_cart" total with tax included should be '$19.00'
    Then my cart "fourth_cart" should have the following details:
      | total_products | $12.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $19.00 |
    When I use a voucher "discount_with_l_and_red" on the cart "fourth_cart"
    Then I should get cart rule validation error
    # Product with only Red (but not L) is not enough
    Then I delete combination metalTshirtL from product metalTshirt from cart fourth_cart
    And I add 1 combination "metalSweatMRed" from product "metalSweat" to the cart "fourth_cart"
    Then cart "fourth_cart" total with tax included should be '$25.00'
    And my cart "fourth_cart" should have the following details:
      | total_products | $18.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $25.00 |
    When I use a voucher "discount_with_l_and_red" on the cart "fourth_cart"
    Then I should get cart rule validation error
    # Add another product Orange (so not Red) but L, it should not work because we expect the condition to
    # be met by one product specifically not by combining the attributes of all products
    And I add 1 combination "metalSweatLOrange" from product "metalSweat" to the cart "fourth_cart"
    Then cart "fourth_cart" total with tax included should be '$43.00'
    And my cart "fourth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "discount_with_l_and_red" on the cart "fourth_cart"
    Then I should get cart rule validation error
    # Now let's try with a product L AND Red
    Then I delete combination metalSweatMRed from product metalSweat from cart fourth_cart
    And I delete combination metalSweatLOrange from product metalSweat from cart fourth_cart
    And I add 1 combination "metalSweatLRed" from product "metalSweat" to the cart "fourth_cart"
    Then cart "fourth_cart" total with tax included should be '$25.00'
    And my cart "fourth_cart" should have the following details:
      | total_products | $18.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $25.00 |
    When I use a voucher "discount_with_l_and_red" on the cart "fourth_cart"
    Then cart "fourth_cart" total with tax included should be '$20.00'
    And my cart "fourth_cart" should have the following details:
      | total_products | $18.00 |
      | shipping       | $7.00  |
      | total_discount | -$5.00 |
      | total          | $20.00 |

  Scenario: Test discount that gives a cart level discount with 2 products that must be S/L AND Orange/Red
    When I create a "cart_level" discount "discount_with_s_or_l_and_orange_or_red" with following properties:
      | name[en-US]        | Promotion                              |
      | name[fr-FR]        | Promotion                              |
      | code               | DISCOUNT_WITH_S_OR_L_AND_ORANGE_OR_RED |
      | active             | true                                   |
      | reduction_amount   | 6.0                                    |
      | reduction_currency | usd                                    |
      | taxIncluded        | true                                   |
    When I update discount "discount_with_s_or_l_and_orange_or_red" with following conditions matching at least 2 products:
      | condition_type | items      |
      | attributes     | S,L        |
      | attributes     | Red,Orange |
    Then discount "discount_with_s_or_l_and_orange_or_red" should have the following product conditions matching at least 2 products:
      | condition_type | items      |
      | attributes     | S,L        |
      | attributes     | Red,Orange |
    Given I create an empty cart "fifth_cart" for customer "testCustomer"
    # Two products with only L but Black are not enough
    When I add 2 combinations "metalSweatLBlack" from product "metalSweat" to the cart "fifth_cart"
    Then cart "fifth_cart" total with tax included should be '$43.00'
    And my cart "fifth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "discount_with_s_or_l_and_orange_or_red" on the cart "fifth_cart"
    Then I should get cart rule validation error
    # One product with Orange (but M) and another with L (but Black) are not enough
    Then I delete combination metalSweatLBlack from product metalSweat from cart fifth_cart
    And I add 1 combination "metalSweatMOrange" from product "metalSweat" to the cart "fifth_cart"
    And I add 1 combination "metalSweatLBlack" from product "metalSweat" to the cart "fifth_cart"
    Then cart "fifth_cart" total with tax included should be '$43.00'
    And my cart "fifth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "discount_with_s_or_l_and_orange_or_red" on the cart "fifth_cart"
    Then I should get cart rule validation error
    # Two products L and Orange will work
    Then I delete combination metalSweatMOrange from product metalSweat from cart fifth_cart
    And I delete combination metalSweatLBlack from product metalSweat from cart fifth_cart
    And I add 2 combinations "metalSweatLOrange" from product "metalSweat" to the cart "fifth_cart"
    Then cart "fifth_cart" total with tax included should be '$43.00'
    And my cart "fifth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "discount_with_s_or_l_and_orange_or_red" on the cart "fifth_cart"
    Then cart "fifth_cart" total with tax included should be '$37.00'
    And my cart "fifth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | -$6.00 |
      | total          | $37.00 |
    # Remove the 2 LOrange, and replace with 1 SRed and 1 SOrange, it should work
    Then I delete combination metalSweatLOrange from product metalSweat from cart fifth_cart
    And I add 1 combination "metalSweatSRed" from product "metalSweat" to the cart "fifth_cart"
    And I add 1 combination "metalSweatSOrange" from product "metalSweat" to the cart "fifth_cart"
    Then cart "fifth_cart" total with tax included should be '$43.00'
    And my cart "fifth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "discount_with_s_or_l_and_orange_or_red" on the cart "fifth_cart"
    Then cart "fifth_cart" total with tax included should be '$37.00'
    And my cart "fifth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | -$6.00 |
      | total          | $37.00 |

  Scenario: Test discount that gives a cart level discount with 1 product that must Black and S or L
    When I create a "cart_level" discount "discount_with_black_and_s_or_l" with following properties:
      | name[en-US]        | Promotion                      |
      | name[fr-FR]        | Promotion                      |
      | code               | DISCOUNT_WITH_BLACK_AND_S_OR_L |
      | active             | true                           |
      | reduction_amount   | 7.0                            |
      | reduction_currency | usd                            |
      | taxIncluded        | true                           |
    When I update discount "discount_with_black_and_s_or_l" with following conditions matching at least 1 products:
      | condition_type | items |
      | attributes     | S,L   |
      | attributes     | Black |
    Then discount "discount_with_black_and_s_or_l" should have the following product conditions matching at least 1 products:
      | condition_type | items |
      | attributes     | S,L   |
      | attributes     | Black |
    Given I create an empty cart "sixth_cart" for customer "testCustomer"
    # Two products one with Black the other with S, should not work
    When I add 1 combination "metalSweatSOrange" from product "metalSweat" to the cart "sixth_cart"
    And I add 1 combination "metalSweatMBlack" from product "metalSweat" to the cart "sixth_cart"
    Then cart "sixth_cart" total with tax included should be '$43.00'
    And my cart "sixth_cart" should have the following details:
      | total_products | $36.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $43.00 |
    When I use a voucher "discount_with_black_and_s_or_l" on the cart "sixth_cart"
    Then I should get cart rule validation error
    # One product with Black AND L it should work
    Then I delete combination metalSweatSOrange from product metalSweat from cart sixth_cart
    And I delete combination metalSweatMBlack from product metalSweat from cart sixth_cart
    And I add 1 combination "metalSweatLBlack" from product "metalSweat" to the cart "sixth_cart"
    Then cart "sixth_cart" total with tax included should be '$25.00'
    And my cart "sixth_cart" should have the following details:
      | total_products | $18.00 |
      | shipping       | $7.00  |
      | total_discount | $0.00  |
      | total          | $25.00 |
    When I use a voucher "discount_with_black_and_s_or_l" on the cart "sixth_cart"
    Then cart "sixth_cart" total with tax included should be '$18.00'
    And my cart "sixth_cart" should have the following details:
      | total_products | $18.00 |
      | shipping       | $7.00  |
      | total_discount | -$7.00 |
      | total          | $18.00 |
