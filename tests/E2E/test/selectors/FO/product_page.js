module.exports = {
  productPage: {
    first_product: '(//*[@id="content"]//h1[@itemprop="name"])[1]',
    first_product_size: '//*[@id="group_1"]',
    first_product_quantity: '//*[@id="quantity_wanted"]',
    first_product_color: '//*[@id="group_2"]/li[2]/label/input',
    first_product_discount: '//*[@id="content"]/section/div/article[1]//span[contains(@class, "discount-percentage")]',
    product_name: '(//*[@id="main"]//h1[@itemprop="name"])[1]',
    product_price: '(//*[@id="main"]//span[@itemprop="price"])[1]',
    product_reference: '(//*[@id="main"]//span[@itemprop="sku"])[1]',
    product_quantity: '//*[@id="product-details"]/div[@class="product-quantities"]/span',
    pack_product_name: '//*[@id="add-to-cart-or-refresh"]//article[%P]//div[@class="pack-product-name"]/a',
    pack_product_price: '//*[@id="add-to-cart-or-refresh"]//article[%P]//div[@class="pack-product-price"]',
    pack_product_quantity: '//*[@id="add-to-cart-or-refresh"]//article[%P]//div[@class="pack-product-quantity"]',
    product_size: '//*[@id="group_1"]',
    product_color: '(//*[@id="group_3"]//span)[2]',
    see_all_products: '//*[@id="content"]//a[contains(@class, "all-product-link")]',
    first_product_all: '(//*[@id="js-product-list"]//article//a)[1]',
    pagination_next: '//*[@id="js-product-list"]//a[contains(@class, "next")]',
    product_discount_details: '//*[@id="main"]//span[contains(@class, "discount")]',
    quick_view_add_to_cart:'//*[@id="add-to-cart-or-refresh"]//button[contains(@data-button-action, "add-to-cart")]',
    //Get the number of products in front office after clicking on "SEE ALL PRODUCTS" link
    products_number: '//*[@id="js-product-list-top"]//p'
  }
};
