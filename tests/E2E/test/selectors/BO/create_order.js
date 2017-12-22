module.exports = {
  CreateOrder:{
    order_quantity: '//*[@id="orderProducts"]/tbody/tr[1]/td[4]/span[1]',
    new_order_button: '//*[@id="page-header-desc-order-new_order"]',
    customer_search_input: '//*[@id="customer"]',
    choose_customer_button: '//*[@id="customers"]/div/div/div[2]/button',
    product_search_input: '//*[@id="product"]',
    quantity_input: '//*[@id="qty"]',
    add_to_cart_button: '//*[@id="submitAddProduct"]',
    order_message_textarea: '//*[@id="order_message"]',
    delivery_option: '//*[@id="delivery_option"]',
    payment: '//*[@id="payment_module_name"]',
    total_shipping:'//*[@id="total_shipping"]',
    create_order_button: '//*[@id="summary_part"]/div[4]/div/div[5]/div/button',
    product_combination: '//*[@id="ipa_2"]',
    basic_price_value: '//*[@id="customer_cart"]/tbody/tr/td[4]/input',
    product_select:'//*[@id="id_product"]',
  }
};
