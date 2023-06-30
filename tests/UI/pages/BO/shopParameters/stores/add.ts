import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';
import StoreData from '@data/faker/store';

/**
 * Add store page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddStore extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly storeForm: string;

  private readonly nameInput: string;

  private readonly address1Input: string;

  private readonly address2Input: string;

  private readonly postcodeInput: string;

  private readonly cityInput: string;

  private readonly countrySelect: string;

  private readonly stateSelect: string;

  private readonly latitudeInput: string;

  private readonly longitudeInput: string;

  private readonly phoneInput: string;

  private readonly faxInput: string;

  private readonly emailInput: string;

  private readonly noteTextarea: string;

  private readonly statusToggle: (toggle: string) => string;

  private readonly pictureInput: string;

  private readonly hoursInput: (pos: number, languageId: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add store page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Stores > Add new •';
    this.pageTitleEdit = 'Stores > Edit:';

    // Form selectors
    this.storeForm = '#store_form';
    this.nameInput = '#name';
    this.address1Input = '#address1';
    this.address2Input = '#address2';
    this.postcodeInput = '#postcode';
    this.cityInput = '#city';
    this.countrySelect = '#id_country';
    this.stateSelect = '#id_state';
    this.latitudeInput = '#latitude';
    this.longitudeInput = '#longitude';
    this.phoneInput = '#phone';
    this.faxInput = '#fax';
    this.emailInput = '#email';
    this.noteTextarea = '#note_1';
    this.statusToggle = (toggle: string) => `${this.storeForm} #active_${toggle}`;
    this.pictureInput = `${this.storeForm} #image`;
    this.hoursInput = (pos: number, languageId: string) => `input[name='hours[${pos}][${languageId}]']`;
    this.saveButton = '#store_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';
  }

  /* Methods */

  /**
   * Fill creation / edition form for store and save it
   * @param page {Page} Browser tab
   * @param storeData {StoreData} Data to set on store form
   * @return {Promise<string>}
   */
  async createEditStore(page: Page, storeData: StoreData): Promise<string> {
    // Set name
    await this.setValue(page, `${this.nameInput}_1`, storeData.name);

    // Set address inputs
    await this.setValue(page, `${this.address1Input}_1`, storeData.address1);
    await this.setValue(page, `${this.address2Input}_1`, storeData.address2);
    await this.setValue(page, this.postcodeInput, storeData.postcode);
    await this.setValue(page, this.cityInput, storeData.city);
    await this.selectByVisibleText(page, this.countrySelect, storeData.country);
    await this.setValue(page, this.latitudeInput, storeData.latitude);
    await this.setValue(page, this.longitudeInput, storeData.longitude);

    // Set phone inputs
    await this.setValue(page, this.phoneInput, storeData.phone);
    await this.setValue(page, this.faxInput, storeData.fax);

    // Set email and notes inputs
    await this.setValue(page, this.emailInput, storeData.email);
    await this.setValue(page, this.noteTextarea, storeData.note);

    // Set store status
    await this.setChecked(page, this.statusToggle(storeData.status ? 'on' : 'off'));

    // Set store picture
    if (storeData.picture) {
      await this.uploadFile(page, this.pictureInput, storeData.picture);
    }

    // Set opening hours
    for (let day:number = 1; day <= 7; day++) {
      await this.setValue(page, this.hoursInput(day, '1'), storeData.hours[day - 1]);
    }

    // Save store
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   *
   * @param page {Page}
   * @param inputName {string}
   * @param languageId {string | undefined}
   */
  async getInputValue(page: Page, inputName: string, languageId?: string): Promise<string> {
    let selector;

    switch (inputName) {
      case 'name':
        selector = `${this.nameInput}_${languageId}`;
        break;

      case 'address1':
        selector = `${this.address1Input}_${languageId}`;
        break;

      case 'address2':
        selector = `${this.address2Input}_${languageId}`;
        break;

      case 'postcode':
        selector = this.postcodeInput;
        break;

      case 'city':
        selector = this.cityInput;
        break;

      case 'latitude':
        selector = this.latitudeInput;
        break;

      case 'longitude':
        selector = this.longitudeInput;
        break;

      case 'phone':
        selector = this.phoneInput;
        break;

      case 'fax':
        selector = this.faxInput;
        break;

      case 'email':
        selector = this.emailInput;
        break;

      case 'monday':
        selector = this.hoursInput(1, languageId === undefined ? '1' : languageId);
        break;

      case 'tuesday':
        selector = this.hoursInput(2, languageId === undefined ? '1' : languageId);
        break;

      case 'wednesday':
        selector = this.hoursInput(3, languageId === undefined ? '1' : languageId);
        break;

      case 'thursday':
        selector = this.hoursInput(4, languageId === undefined ? '1' : languageId);
        break;

      case 'friday':
        selector = this.hoursInput(5, languageId === undefined ? '1' : languageId);
        break;

      case 'saturday':
        selector = this.hoursInput(6, languageId === undefined ? '1' : languageId);
        break;

      case 'sunday':
        selector = this.hoursInput(7, languageId === undefined ? '1' : languageId);
        break;
      default:
        throw new Error(`Column ${inputName} was not found`);
    }

    return this.getAttributeContent(page, selector, 'value');
  }

  /**
   *
   * @param page {Page}
   * @param name {string}
   */
  async getSelectValue(page: Page, name: string): Promise<string> {
    let selector: string;

    switch (name) {
      case 'id_country':
        selector = this.countrySelect;
        break;
      case 'id_state':
        selector = this.stateSelect;
        break;
      default:
        throw new Error(`Field ${name} was not found`);
    }

    return page.$eval(selector, (node: HTMLSelectElement) => node.value);
  }

  /**
   *
   * @param page {Page}
   * @param toggle {string}
   */
  async isActive(page: Page, toggle: string): Promise<boolean> {
    return this.isChecked(page, this.statusToggle(toggle));
  }
}

export default new AddStore();
