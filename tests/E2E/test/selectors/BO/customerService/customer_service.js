module.exports = {
  CustomerServicePage: {
    email_filter_input: '//*[@id="table-customer_thread"]//input[@name="customer_threadFilter_a!email"]',
    search_button: '//*[@id="submitFilterButtoncustomer_thread"]',
    reset_button: '//*[@id="table-customer_thread"]//button[@name="submitResetcustomer_thread"]',
    dropdown_button: '//*[@id="table-customer_thread"]//button[@data-toggle="dropdown"]',
    view_button: '//*[@id="table-customer_thread"]//a[@title="View"]',
    delete_button: '//*[@id="table-customer_thread"]//a[@title="Delete"]',
    success_panel: '//*[@id="content"]//div[contains(@class,"alert-success") and not(contains(@class,"hide"))]',
    email_sender_text: '//*[@id="content"]//div[contains(@class, "media-body")]//h2',
    email_receive_text: '//*[@id="content"]//div[contains(@class, "media-body")]//span[@class="badge"]',
    message_text: '//*[@id="content"]//div[@class="message-body"]//p'
  }
};