declare module 'maildev' {
  import type MailDevEmail from '@data/types/maildevEmail';
  import fs from 'fs';

  /**
   * Interface for {@link MailDev}.
   */
  declare class MailDev {
    /**
     * Constructor.
     *
     * @param options The options.
     */
    constructor(options: MailDevOptions);

    /**
     * Deletes a given email by identifier.
     *
     * @param  id        The email identifier.
     * @param  callback  The error callback.
     */
    deleteEmail(id: string, callback?: (error: Error) => void): void;

    /**
     * Deletes all email and their attachments.
     *
     * @param callback The error callback.
     */
    deleteAllEmail(callback?: (error: Error) => void): void;

    /**
     * Stops the SMTP server.
     *
     * @param callback The error callback.
     */
    close(callback?: (error: Error) => void): void;

    /**
     * Accepts e-mail identifier, returns email object.
     *
     * @param  id        The e-mail identifier.
     * @param  callback  The callback.
     */
    getEmail(id: string, callback?: (error: Error | null, email?: Mail) => void): void;

    /**
     * Returns the content type and a readable stream of the file.
     *
     * @param  id        The e-mail identifier.
     * @param  filename  The filename attachment
     * @param  callback  The callback.
     */
    getEmailAttachment(id: string, callback?: (error: Error) => void): void;

    /**
     * Returns the html of a given email.
     *
     * @param  id        The e-mail identifier.
     * @param  baseUrl        The e-mail identifier.
     * @param  callback  The callback.
     */
    getEmailHTML(id: string, baseUrl?: string, callback?: (error: Error) => void): void;

    /**
     * Download a given email.
     *
     * @param  id        The e-mail identifier.
     * @param  callback  The callback.
     */
    getEmailEml(id: string, callback?: (error: Error) => void): void;

    /**
     * Returns a readable stream of the raw e-mail.
     *
     * @param id The e-mail identifier.
     * @param callback The callback
     */
    getRawEmail(id: string, callback?: (error: Error, readStream: fs.ReadStream) => void): void;

    /**
     * Returns array of all e-mail.
     */
    getAllEmail(done: (error: Error, emails: Array<object>) => void): void;

    /**
     * Starts the SMTP server.
     *
     * @param callback The error callback.
     */
    listen(callback?: (error: Error) => void): void;

    /**
     * Event called when a new e-mail is received. Callback receives single mail object.
     *
     * @param  eventName The event name.
     * @param  email     The email.
     */
    on(eventName: string, callback: (email: MailDevEmail) => void): void;

    /**
     * Event called when a mail server has an error
     *
     * @param  callback     The error callback.
     */
    onSmtpError(callback: (err: Error) => void): void;

    /**
     * Relay the e-mail.
     *
     * @param idOrMailObject The identifier or mail object.
     * @param done The callback.
     */
    relayMail(idOrMailObject: string, done: (error: Error) => void): void;

    relayMail(idOrMailObject: string | Mail, isAutoRelay: boolean, done: (error: Error) => void): void;

    /**
     * Read all emails.
     */
    readAllEmail(done: (error: Error, emails: number) => void): void;

    /**
     * Setup outgoing.
     */
    setupOutgoing(host?: string, port?: number, user?: string, pass?: string, secure?: boolean): void;

    isOutgoingEnabled(): boolean;

    getOutgoingHost(): string;

    setAutoRelayMode(enabled: boolean, rules?: string | string[], emailAddress?: string): void;

    loadMailsFromDirectory(): void;
  }

  export = MailDev;
}
