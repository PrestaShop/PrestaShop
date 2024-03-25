/* eslint-disable vars-on-top, no-var */

type GlobalInstall = {
  URL: string
  ENABLE_SSL: boolean
  LANGUAGE: string
  COUNTRY: string
  DB_SERVER: string
  DB_NAME: string
  DB_USER: string
  DB_PASSWD: string
  DB_PREFIX: string
  SHOP_NAME: string
}

type GlobalFO = {
  URL: string
}

type GlobalBO = {
  URL: string
  EMAIL: string
  PASSWD: string
  FIRSTNAME: string
  LASTNAME: string
}

type GlobalAPI = {
  URL: string
}

type GlobalBrowserConfig = {
  headless: any
  timeout: number
  slowMo: number
  channel?: string
  args?: Array<string>
}

type GlobalBrowser = {
  name: string
  lang: string
  width: number
  height: number
  sandboxArgs: Array<string>
  acceptDownloads: boolean
  config: GlobalBrowserConfig
  interceptErrors: any
}

type GlobalPSConfig = {
  parametersFile: string
}

type GlobalBrowserErrors = {
  responses: Array<any>
  js: Array<any>
  console: Array<any>
}

type GlobalScreenshot = {
  FOLDER: string
  AFTER_FAIL: any
}

type GlobalMaildevConfig = {
  smtpPort: number
  smtpServer: string
  silent: boolean
}

type GlobalKeycloakConfig = {
  keycloakExternalUrl: string
  keycloakInternalUrl: string
  keycloakAdminUser: string
  keycloakAdminPass: string
  keycloakClientId: string
}

declare global {
  var INSTALL: GlobalInstall;
  var URLHasPort: boolean;
  var FO: GlobalFO;
  var BO: GlobalBO;

  var API: GlobalAPI;
  var PSConfig: GlobalPSConfig;
  var BROWSER: GlobalBrowser;
  var GENERATE_FAILED_STEPS: any;
  var SCREENSHOT: GlobalScreenshot;
  var maildevConfig: GlobalMaildevConfig;
  var keycloakConfig: GlobalKeycloakConfig;
  var browserErrors: GlobalBrowserErrors;
}

export {};
