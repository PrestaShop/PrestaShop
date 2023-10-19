type CustomerServiceOptionsCreator = {
  imapUrl?: string
  imapPort?: string
  imapUser?: string
  imapPassword?: string
  deleteMessage?: boolean
  createNewThreads?: boolean
  imapOptionsPop3?: boolean
  imapOptionsNoRsh?: boolean
  imapOptionsSsl?: boolean
  imapOptionsValidateCert?: boolean
  imapOptionsTls?: boolean
  imapOptionsNoTls?: boolean
};

export default CustomerServiceOptionsCreator;
