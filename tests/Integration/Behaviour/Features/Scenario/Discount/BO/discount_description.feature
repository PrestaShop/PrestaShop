# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-description
@restore-all-tables-before-feature
@restore-languages-after-feature
@discount-description
Feature: Discount description field
    PrestaShop allows BO users to add descriptions to discounts
    As a BO user
    I must be able to create and update discount descriptions
    The description field is optional and for internal use only

    Background:
        Given shop "shop1" with name "test_shop" exists
        Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
        Given currency "usd" is the default one
        And language with iso code "en" is the default one

    Scenario: Create a discount with description
        When I create a "cart_level" discount "discount_with_description" with following properties:
            | name[en-US]       | Promotion with description                         |
            | active            | true                                               |
            | valid_from        | 2019-01-01 11:05:00                                |
            | valid_to          | 2019-12-01 00:00:00                                |
            | code              | PROMO_DESC_2019                                    |
            | reduction_percent | 10.0                                               |
            | description       | Internal note: This is a test discount for Q4 2019 |
        And discount "discount_with_description" should have the following properties:
            | name[en-US]       | Promotion with description                         |
            | type              | cart_level                                         |
            | active            | true                                               |
            | valid_from        | 2019-01-01 11:05:00                                |
            | valid_to          | 2019-12-01 00:00:00                                |
            | code              | PROMO_DESC_2019                                    |
            | reduction_percent | 10.0                                               |
            | description       | Internal note: This is a test discount for Q4 2019 |

    Scenario: Create a discount without description (optional field)
        When I create a "free_shipping" discount "discount_without_description" with following properties:
            | name[en-US] | Promotion without description |
            | active      | true                          |
            | valid_from  | 2019-01-01 11:05:00           |
            | valid_to    | 2019-12-01 00:00:00           |
            | code        | PROMO_NO_DESC_2019            |
        And discount "discount_without_description" should have the following properties:
            | name[en-US] | Promotion without description |
            | type        | free_shipping                 |
            | active      | true                          |
            | valid_from  | 2019-01-01 11:05:00           |
            | valid_to    | 2019-12-01 00:00:00           |
            | code        | PROMO_NO_DESC_2019            |
            | description |                               |

    Scenario: Update discount description
        When I create a "cart_level" discount "discount_to_update_desc" with following properties:
            | name[en-US]       | Original discount    |
            | active            | true                 |
            | valid_from        | 2019-01-01 11:05:00  |
            | valid_to          | 2019-12-01 00:00:00  |
            | code              | PROMO_UPDATE_DESC    |
            | reduction_percent | 20.0                 |
            | description       | Original description |
        And discount "discount_to_update_desc" should have the following properties:
            | description | Original description |
        When I update discount "discount_to_update_desc" with the following properties:
            | description | Updated description for internal use |
        Then discount "discount_to_update_desc" should have the following properties:
            | description | Updated description for internal use |

     Scenario: Update discount to remove description
         When I create a "cart_level" discount "discount_to_remove_desc" with following properties:
             | name[en-US]       | Discount with desc   |
             | active            | true                 |
             | valid_from        | 2019-01-01 11:05:00  |
             | valid_to          | 2019-12-01 00:00:00  |
             | code              | PROMO_REMOVE_DESC    |
             | reduction_percent | 25.0                 |
             | description       | This will be removed |
         And discount "discount_to_remove_desc" should have the following properties:
             | description | This will be removed |
         When I update discount "discount_to_remove_desc" with the following properties:
             | description |  |
         Then discount "discount_to_remove_desc" should have the following properties:
             | description |  |

     Scenario: Create a discount with description that exceeds maximum length
         When I create a "cart_level" discount "discount_too_long_desc" with a very large description
         Then I should get error that discount field description is invalid

