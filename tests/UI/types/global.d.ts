type GlobalInstall = {
  URL: string,
  LANGUAGE: string,
  COUNTRY: string,
  DB_SERVER: string,
  DB_NAME: string,
  DB_USER: string,
  DB_PASSWD: string,
  DB_PREFIX: string,
  SHOP_NAME: string
}

declare global {
  var INSTALL: GlobalInstall
}

export {}
