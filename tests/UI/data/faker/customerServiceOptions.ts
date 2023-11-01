import type CustomerServiceOptionsCreator from '@data/types/CustomerServiceOptions';

/**
 * CustomerServiceOptionsData to use in options form on BO
 * @class
 */
export default class CustomerServiceOptionsData {
  public readonly imapUrl: string;

  public readonly imapPort: string;

  public readonly imapUser: string;

  public readonly imapPassword: string;

  public readonly deleteMessage: boolean;

  public readonly createNewThreads: boolean;

  public readonly imapOptionsPop3: boolean;

  public readonly imapOptionsNoRsh: boolean;

  public readonly imapOptionsSsl: boolean;

  public readonly imapOptionsValidateCert: boolean;

  public readonly imapOptionsTls: boolean;

  public readonly imapOptionsNoTls: boolean;

  /**
   * Constructor for class customerServiceOptions
   * @param customerServiceOptions {CustomerServiceOptionsCreator} Could be used to force the value of some members
   */
  constructor(customerServiceOptions: CustomerServiceOptionsCreator = {}) {
    /** @type {string} Imap URL */
    this.imapUrl = customerServiceOptions.imapUrl || '';

    /** @type {string} Imap port */
    this.imapPort = customerServiceOptions.imapPort || '';

    /** @type {string} Imap user */
    this.imapUser = customerServiceOptions.imapUser || '';

    /** @type {string} Imap password */
    this.imapPassword = customerServiceOptions.imapPassword || '';

    /** @type {boolean} True if we need to delete message */
    this.deleteMessage = customerServiceOptions.deleteMessage || false;

    /** @type {boolean} True if we need to enable create new threads */
    this.createNewThreads = customerServiceOptions.createNewThreads || false;

    /** @type {boolean} True if we need to enable imap options pop3 */
    this.imapOptionsPop3 = customerServiceOptions.imapOptionsPop3 || false;

    /** @type {boolean} True if we need to enable imap options no rsh */
    this.imapOptionsNoRsh = customerServiceOptions.imapOptionsNoRsh || false;

    /** @type {boolean} True if we need to enable imap options ssl */
    this.imapOptionsSsl = customerServiceOptions.imapOptionsSsl || false;

    /** @type {boolean} True if we need to enable imap options validate cert */
    this.imapOptionsValidateCert = customerServiceOptions.imapOptionsValidateCert || false;

    /** @type {boolean} True if we need to enable imap options tls */
    this.imapOptionsTls = customerServiceOptions.imapOptionsTls || false;

    /** @type {boolean} True if we need to enable imap options no tls */
    this.imapOptionsNoTls = customerServiceOptions.imapOptionsNoTls || false;
  }
}
