const MailDev = require('maildev');

module.exports = {
  /**
   *
   * @param config
   * @returns {*}
   */
  createMailListener(config = global.maildevConfig) {
    return new MailDev({
      smtp: config.smtpPort,
    });
  },
  /**
   *
   * @param mailListener
   */
  startListener(mailListener) {
    mailListener.listen((err) => {
      if (err) {
        throw new Error(err);
      }
    });
  },
  /**
   *
   * @param mailListener
   */
  stopListener(mailListener) {
    mailListener.close((err) => {
      if (err) {
        throw new Error(err);
      }
    });
  },
};
