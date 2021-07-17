require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddOrderReturnStatus extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Statuses > Add new •';
    this.pageTitleEdit = 'Statuses > Edit •';

    // Form selectors
    this.nameInput = '#name_1';
    this.colorInput = '#color_0';
    this.saveButton = '#order_return_state_form_submit_btn';
  }

  /* Methods */

  /**
   * Fill order return status form in create or edit page and save
   * @param page
   * @param orderReturnStatusData
   * @return {Promise<string>}
   */
  async setOrderReturnStatus(page, orderReturnStatusData) {
    await this.setValue(page, this.nameInput, orderReturnStatusData.name);

    // Set color
    await this.setValue(page, this.colorInput, orderReturnStatusData.color);

    // Save order return status
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddOrderReturnStatus();
