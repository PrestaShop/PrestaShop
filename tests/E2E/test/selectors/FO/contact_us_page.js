module.exports = {
  ContactUsPageFO: {
    contact_us_button: '//*[@id="contact-link"]/a',
    subject_select: '//*[@id="content"]//select[@name="id_contact"]',
    subject_select_option: '//*[@id="content"]//select[@name="id_contact"]/option[@value="%V"]',
    email_address_input: '//*[@id="content"]//input[@name="from"]',
    attachment_file: '//*[@id="filestyle-0"]',
    message_textarea: '//*[@id="content"]//textarea[@name="message"]',
    send_button: '//*[@id="content"]//input[@type="submit"]',
    success_panel: '//*[@id="content"]//div[contains(@class,"alert-success")]'
  }
};