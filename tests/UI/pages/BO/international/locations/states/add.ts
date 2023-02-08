import BOBasePage from '@pages/BO/BObasePage';

import type StateData from '@data/faker/state';

import type {Page} from 'playwright';

/**
 * Add state page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddState extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly nameInput: string;

  private readonly isoCodeInput: string;

  private readonly countrySelect: string;

  private readonly zoneSelect: string;

  private readonly statusToggle: (toggle: string) => string;

  private readonly saveStateButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add state page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'States > Add new •';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#name';
    this.isoCodeInput = '#iso_code';
    this.countrySelect = '#id_country';
    this.zoneSelect = '#id_zone';
    this.statusToggle = (toggle: string) => `#active_${toggle}`;
    this.saveStateButton = '#state_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit state
   * @param page {Page} Browser tab
   * @param stateData {StateData} Data to set on new/edit state form
   * @returns {Promise<string>}
   */
  async createEditState(page: Page, stateData: StateData): Promise<string> {
    // Fill form
    await this.setValue(page, this.nameInput, stateData.name);
    await this.setValue(page, this.isoCodeInput, stateData.isoCode);
    await this.selectByVisibleText(page, this.countrySelect, stateData.country);
    await this.selectByVisibleText(page, this.zoneSelect, stateData.zone);
    await this.setChecked(page, this.statusToggle(stateData.status ? 'on' : 'off'));

    // Save zone
    await this.clickAndWaitForURL(page, this.saveStateButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AddState();
