import MailDev from 'maildev';

/**
 * @module MailDevHelper
 * @description Helper to wrap Maildev functions
 */
export default {

  /**
   * Create a Maildev server instance (the default smtp port is 1026)
   * @param config {{smtpPort: string, smtpServer: string, silent:boolean}} Maildev config to start listening
   * @returns {Object}
   */
  createMailListener(config = global.maildevConfig): MailDev {
    return new MailDev({
      web: 1081,
      smtp: config.smtpPort,
      silent: config.silent,
    });
  },
  /**
   * Start the maildev listener (listen on 1026 smtp port)
   * @param mailListener {MailDev} Maildev server instance
   */
  startListener(mailListener: MailDev): void {
    mailListener.listen((err: Error): void => {
      if (err) {
        throw err;
      }
    });
  },
  /**
   * Stop the maildev listener
   * @param mailListener {MailDev} Maildev server instance
   */
  stopListener(mailListener: MailDev): void {
    mailListener.close((err: Error): void => {
      if (err) {
        throw err;
      }
    });
  },
};
