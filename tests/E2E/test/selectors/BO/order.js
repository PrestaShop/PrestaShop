/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
module.exports = {
  OrderPage: {
    orders_subtab: '#subtab-AdminParentOrders',
    form: '#form-order',
    view_order_button: '//*[@id="form-order"]//tr[%NUMBER]//div[contains(@class,"pull-right")]//a',
    order_state_select: '#id_order_state',
    update_status_button: '//*[@id="status"]/form//button[@name="submitState"]',
    order_quantity: '//*[@id="orderProducts"]//tr[%NUMBER]/td[4]',
    order_status: '//*[@id="status"]//table[contains(@class,"history-status")]//tr[1]/td[2]',
    shipping_cost: '//*[@id="shipping_table"]//tr[1]//td[5]/span',
    message_order: '//*[@id="content"]//div[@class="message-body"]//p[@class="message-item-text"]',
    product_Url: '//*[@id="orderProducts"]//tr[1]/td[2]/a',
    edit_product_button: '//*[@id="orderProducts"]//button[contains(@class,"edit_product_change_link")]',
    product_basic_price: '//*[@id="orderProducts"]//tr[%NUMBER]//input[contains(@class,"edit_product_price_tax_incl")]',
    product_basic_price_TTC: '(//*[@id="orderProducts"]//span[@class="product_price_show"])[%NUMBER]',
    customer_name: '//*[@id="content"]//div[@class="message-body"]//h4[@class="message-item-heading"]',
    order_submenu: '//*[@id="subtab-AdminOrders"]/a',
    document_submenu: '//*[@id="tabOrder"]//a[@href="#documents"]',
    download_invoice_button: '(//*[@id="documents_table"]//a[contains(@href, "generateInvoicePDF")])[1]',
    download_delivery_button: '//*[@id="documents_table"]//tr[3]/td[3]/a',
    product_name: '//*[@id="orderProducts"]//tr[%NUMBER]//span[@class="productName"]',
    total_price: '//*[@id="total_products"]/td[contains(@class,"amount")]',
    shipping_method: '//*[@id="shipping_table"]//tr/td[3]',
    search_by_reference_input: '//*[@id="form-order"]//table[contains(@class,"order")]//input[@name="orderFilter_reference"]',
    search_order_button: '//*[@id="submitFilterButtonorder"]',
    first_order: '//*[@id="form-order"]//table//tbody//tr[1]//a',
    transform_guest_customer_button: '//*[@id="content"]//input[@name="submitGuestToCustomer"]',
    check_order_id: '(//*[@id="table-cart"]//td[3]/span)[1]',
    dropdown_button: '(//*[@id="table-cart"]/tbody//button[@data-toggle="dropdown"])[1]',
    delete_button: '(//*[@id="table-cart"]//a[@class="delete"])[1]',
    stock_product: '//*[@id="orderProducts"]//tr[%NUMBER]/td[5]',
    total_order: '//*[@id="total_order"]/td[@class="amount text-right nowrap"]/strong',
    total_product_price: '//*[@id="orderProducts"]//tr[%NUMBER]/td[6]',
    first_shopping_cart_checkbox: '//*[@id="table-cart"]//tr[1]//input[@name="cartBox[]"]',
    first_dropdown_button: '//*[@id="table-cart"]/tbody//tr[1]//button[@data-toggle="dropdown"]',
    total_price_tax_included: '//*[@id="orderProducts"]//td[@class="total_product"]',
    order_view_button: '//*[@id="form-order"]//table//tr[%ORDERNumber]//td[12]//a',
    partial_refund: '//*[@id="desc-order-partial_refund"]',
    product_total: '//*[@id="documents"]//tr[contains(@id,"orderslip")]/td[4]',
    re_stock_product: '//label[@for="reinjectQuantitiesRefund"]',
    generate_credit_slip_checkbox: '//label[@for="generateCreditSlip"]',
    refund_products_button: '//button[@name="partialRefund"]',
    success_msg: '(//*[@id="main"]//div[contains(@class,"alert-success")])[1]',
    order_date: '(//*[@id="content"]//div[@class="kpi-content"])[1]/span[2]',
    order_ref: '(//*[@id="content"]//span[@class="badge"])[1]',
    product_information: '//*[@id="orderProducts"]//tr[1]/td[2]/a',
    empty_page_logo: '//*[@id="documents_table"]//div[@class="list-empty-msg"]',
    pdf_icon: '//*[@id="table-order"]//tr[%ORDER]//td[11]//i',
    invoice_document: '//table//td[3]/a',
    product_quantity: '//*[@id="orderProducts"]//span[contains(@class,"product_quantity_show")]',
    product_name_tab: '//*[@id="orderProducts"]//span[contains(@class,"productName")]',
    product_unit_price: '//*[@id="orderProducts"]//input[@name="product_price_tax_excl"]',
    product_unit_price_tax_included: '//*[@id="orderProducts"]//span[@class="product_price_show"]',
    product_price: '//*[@id="orderProducts"]//input[@name="product_price_tax_incl"]',
    total_order_price: '//*[@id="total_order"]/td[2]/strong',
    total_product: '//*[@id="total_products"]/td[2]',
    shipping_cost_price: '//*[@id="total_shipping"]/td[2]',
    total: '//*[@id="total_order"]/td[2]/strong',
    total_tax: '//*[@id="total_taxes"]/td[2]',
    carrier: '//*[@id="shipping_table"]//tr[1]/td[3]',
    payment_method: '//*[@id="formAddPayment"]//tr[1]/td[2]',
    order_id: '(//*[@id="content"]//span[@class="badge"])[2]',
    credit_slip_document_name: '//*[@id="documents_table"]//tr[3]//a',
    quantity_refund: '//*[@id="orderProducts"]//div[@class="input-group"]/input[@onchange="checkPartialRefundProductQuantity(this)"]',
    documents_tab: '//*[@id="tabOrder"]/li[2]/a',
    generate_invoice_button: '//*[@id="documents_table"]/tbody/tr/td/a',
    awaiting_bank_wire_payment_option: '//*[@id="form_generate_by_status_order_states"]//label/span[contains(text(),"Awaiting bank wire payment")]',
    awaiting_check_payment: '//*[@id="form_generate_by_status_order_states"]//label/span[contains(text(),"Awaiting check payment")]',
    cancelled_option: '//*[@id="form_generate_by_status_order_states"]//label/span[contains(text(),"Canceled")]'
  },

  CreateOrder: {
    new_order_button: '//*[@id="page-header-desc-order-new_order"]',
    customer_search_input: '//*[@id="customer"]',
    choose_customer_button: '//*[@id="customers"]/div[1]/div/div[2]/button',
    product_search_input: '//*[@id="product"]',
    quantity_input: '//*[@id="qty"]',
    add_to_cart_button: '//*[@id="submitAddProduct"]',
    order_message_textarea: '//*[@id="order_message"]',
    delivery_option: '//*[@id="delivery_option"]',
    payment: '//*[@id="payment_module_name"]',
    total_shipping: '//*[@id="total_shipping"]',
    create_order_button: '//*[@id="summary_part"]//button[@name="submitAddOrder"]',
    product_combination: '//*[@class="id_product_attribute"]',
    basic_price_value: '//*[@id="customer_cart"]//tr[1]//input[@class="product_unit_price"]',
    product_select: '//*[@id="id_product"]'
  },

  ShoppingCart: {
    check_first_order_id: '//*[@id="table-cart"]//tr[1]/td[3]/span',
    check_order_customer: '//*[@id="table-cart"]//tr[1]/td[4]',
    view_order_button: '//*[@id="table-cart"]//td[%NUMBER]//a[@title="View"]',
    total_cart: '//*[@id="box-kpi-cart"]//span[@class="value"]',
    customer_name: '//*[@id="content"]//a[text()="%NAME"]',
    product_unit_price: '//*[@id="orderProducts"]//tr[%NUMBER]/td[3]',
    quantity_product: '//*[@id="orderProducts"]//tr[%NUMBER]/td[4]',
    stock_product: '//*[@id="orderProducts"]//tr[%NUMBER]/td[5]',
    total_product: '//*[@id="orderProducts"]//tr[%NUMBER]/td[6]',
    total_cart_summary: '//*[@id="orderProducts"]//td[2]/strong',
    order_page: '//*[@id="content"]//a[contains(@href,"%s")]',
    export_carts_button: '//*[@id="page-header-desc-cart-export_cart"]',
    search_input: '//*[@id="table-cart"]//thead//input[@name="cartFilter_%searchParam"]',
    search_button: '//*[@id="submitFilterButtoncart"]',
    id: '//*[@id="table-cart"]//tr[%NUMBER]/td[1]',
    order_id: '//*[@id="table-cart"]//tr[%NUMBER]/td[2]',
    customer: '//*[@id="table-cart"]//tr[%NUMBER]/td[3]',
    total: '//*[@id="table-cart"]//tr[%NUMBER]/td[4]',
    carrier: '//*[@id="table-cart"]//tr[%NUMBER]/td[5]',
    date: '//*[@id="table-cart"]//tr[%NUMBER]/td[6]',
    customer_online: '//*[@id="table-cart"]//tr[%NUMBER]/td[7]',
    id_shopping_carts: 'table-cart',
    reset_button: '//*[@name="submitResetcart"]'
  },

  CreditSlip: {
    download_btn: '//*[@id="table-order_slip"]//td[contains(text(),"%ID")]/..//a',
    date_form: '//*[@id="date_from"]',
    date_to: '//*[@id="date_to"]',
    generate_button: '//*[@id="submitPrint"]',
    alerte_message: '//*[@id="content"]/div[4]/div',
    credit_slip_prefix_input: '//*[@id="conf_id_PS_CREDIT_SLIP_PREFIX"]//div[@class="translatable-field lang-1"]//input',
    save_button: '//*[@id="order_slip_fieldset_general"]//button[@type="submit"]',
    green_validation: '//*[@id="content"]//div[@class="alert alert-success"]',
    first_credit_slip_download_btn: '//*[@id="table-order_slip"]/tbody/tr[1]/td[4]/a'
  },

  Invoices: {
    generate_pdf_button: '//*[@id="main-div"]//button[contains(text(),"Generate PDF file by date")]',
    generate_pdf_by_status_button: '//*[@id="main-div"]//button[contains(text(),"Generate PDF file by status")]',
    from_input: '//*[@id="form_generate_by_date_date_from"]',
    to_input: '//*[@id="form_generate_by_date_date_to"]',
    no_invoice_alert: '//*[@id="main-div"]//div[@class="alert-text"]/p'
  }
};
