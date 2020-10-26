# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags duplicate-product
@reset-database-before-feature
@duplicate-product
Feature: Duplicate product from Back Office (BO).
  As an employee I want to be able to duplicate product

  Background:
    Given category "home" in default language named "Home" exists
    And category "men" in default language named "Men" exists
    And category "clothes" in default language named "Clothes" exists

  Scenario: I duplicate product
    Given I add product "product1" with following information:
      | name       | en-US:smart sunglasses |
      | is_virtual | false                  |
    And I add product "product2" with following information:
      | name       | en-US:Reading glasses |
      | is_virtual | false                 |
    And I update product "product1" basic information with following values:
      | description       | en-US:nice sunglasses          |
      | description_short | en-US:Simple & nice sunglasses |
    And I assign product product1 to following categories:
      | categories       | [home, men, clothes] |
      | default category | clothes              |
    And I update product "product1" options with following information:
      | visibility          | catalog           |
      | available_for_order | false             |
      | online_only         | true              |
      | show_price          | false             |
      | condition           | used              |
      | isbn                | 978-3-16-148410-0 |
      | upc                 | 72527273070       |
      | ean13               | 978020137962      |
      | mpn                 | mpn1              |
      | reference           | ref1              |
      | manufacturer        | studioDesign      |
    And I update product "product1" tags with following values:
      | tags | en-US:smart,glasses,sunglasses,men; |
    And I update product "product1" prices with following information:
      | price           | 100.00          |
      | ecotax          | 0               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 70              |
      | unit_price      | 900             |
      | unity           | bag of ten      |
    And I update product product1 SEO information with following values:
      | meta_title       | en-US:SUNGLASSES meta title         |
      | meta_description | en-US:Its so smart, almost magical. |
      | link_rewrite     | en-US:smart-sunglasses              |
      | redirect_type    | 404                                 |
    And carrier carrier1 named "ecoCarrier" exists
    And carrier carrier2 named "Fast carry" exists
    And I update product product1 shipping information with following values:
      | width                            | 10.5                       |
      | height                           | 6                          |
      | depth                            | 7                          |
      | weight                           | 0.5                        |
      | additional_shipping_cost         | 12                         |
      | delivery time notes type         | specific                   |
      | delivery time in stock notes     | en-US:product in stock     |
      | delivery time out of stock notes | en-US:product out of stock |
      | carriers                         | [carrier1,carrier2]        |
    And I add new supplier supplier1 with following properties:
      | name             | my supplier 1            |
      | address          | Donelaicio st. 1         |
      | city             | Kaunas                   |
      | country          | Lithuania                |
      | enabled          | true                     |
      | description      | en-US:just a supplier    |
      | meta title       | en-US:my supplier nr one |
      | meta description | en-US:                   |
      | meta keywords    | en-US:sup,1              |
      | shops            | [shop1]                  |
    And I set product product1 default supplier to supplier1 and following suppliers:
      | reference         | supplier reference | product supplier reference     | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1 | USD      | 10                 |
    And I set following related products to product product1:
      | product2 |
    And I update product product1 with following customization fields:
      | reference    | type | name                            | is required |
      | customField1 | text | en-US:text on top of left lense | true        |
    And I add new attachment "att1" with following properties:
      | description | en-US:puffin photo nr1 |
      | name        | en-US:puffin           |
      | file_name   | app_icon.png           |
    And I associate attachment "att1" with product product1
#todo: add specific prices & priorities, test combinations, packs
