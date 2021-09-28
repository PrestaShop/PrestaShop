const MailDev = require('maildev');

/**
 * @module MailDevHelper
 * @description Helper to wrap Maildev functions
 */
module.exports = {

  /**
   * Create a Maildev server instance (the default smtp port is 1025)
   * @param config {{smtpPort: string, smtpServer: string, silent:boolean}} Maildev config to start listening
   * @returns {Object}
   */
  createMailListener(config = global.maildevConfig) {
    return new MailDev({
      smtp: config.smtpPort,
      silent: config.silent,
    });
  },
  /**
   * Start the maildev listener (listen on 1025 smtp port)
   * @param mailListener {Object} Maildev server instance
   */
  startListener(mailListener) {
    mailListener.listen((err) => {
      if (err) {
        throw new Error(err);
      }
    });
  },
  /**
   * Stop the maildev listener
   * @param mailListener {Object} Maildev server instance
   */
  stopListener(mailListener) {
    mailListener.close((err) => {
      if (err) {
        throw new Error(err);
      }
    });
  },
};
