type GlobalInstall = {
  URL: string
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

type GlobalBrowserConfig = {
  headless: any
  timeout: number
  slowMo: number
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
  smtpPort: string
  smtpServer: string
  silent: boolean
}

declare global {
  let INSTALL: GlobalInstall;
  let URLHasPort: boolean;
  let FO: GlobalFO;
  let BO: GlobalBO;
  let BROWSER: GlobalBrowser;
  let GENERATE_FAILED_STEPS: any;
  let SCREENSHOT: GlobalScreenshot;
  let maildevConfig: GlobalMaildevConfig;
  let browserErrors: GlobalBrowserErrors;
}

export {};
