module.exports = {
  OrderPage:{
    orders_subtab: '#subtab-AdminParentOrders',
    form: '#form-order',
    order_product_name_span: '.productName',
    order_product_quantity_span: '.product_quantity_show',
    order_product_total: '#total_order > td.amount.text-right.nowrap',
    order_reference_span: '((//div[@class="panel-heading"])[1]/span)[1]',
    first_order: '//*[@id="form-order"]/div/div[2]/table/tbody/tr[1]/td[12]/div/a',
    order_state_select: '//*[@id="id_order_state"]',
    update_status_button: '//*[@id="status"]/form/div/div[2]/button',
    order_quantity: '//*[@id="orderProducts"]/tbody/tr[1]/td[4]/span[1]'
  }
};
