const MailDev = require('maildev');

module.exports = {

  /**
   * create a maildev instance (the default smtp port is 1025)
   *
   * @param config
   * @returns {*}
   */
  createMailListener(config = global.maildevConfig) {
    return new MailDev({
      smtp: config.smtpPort,
      silent: true,
    });
  },
  /**
   * start the maildev listener (listen on 1025 smtp port)
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
   * stop the maildev listener
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
