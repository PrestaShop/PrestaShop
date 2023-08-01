import crypto from 'crypto';
import {readFileSync} from 'fs';
import type {APIResponse} from 'playwright';

type HttpHeader = {
  name: string
  value: string
};

type JwtAccessHeader = {
  alg: string
  typ: string
};

type JwtAccessToken = {
  aud: string
  jti: string
  iat: number
  nbf: number
  exp: number
  sub: string
  scopes: string[]
};

/**
 * Filter headers for fetching a specific header
 * @param {HttpHeader[]} headers
 * @param {string} headerName
 * @return {HttpHeader[]}
 */
function filterResponseHeader(headers: HttpHeader[], headerName: string): HttpHeader[] {
  return headers.filter((value: HttpHeader) => value.name === headerName);
}

/**
 * Parse a JWT and extract the header part
 * @param {string} token
 * @return {JwtAccessHeader}
 */
function parseJwtHeader(token: string): JwtAccessHeader {
  const base64Payload: string = token.split('.')[0];
  const payload: Buffer = Buffer.from(base64Payload, 'base64');

  return JSON.parse(payload.toString());
}

/**
 * Parse a JWT and extract the payload part
 * @param {string} token
 * @return {JwtAccessToken}
 */
function parseJwtPayload(token: string): JwtAccessToken {
  const base64Payload: string = token.split('.')[1];
  const payload: Buffer = Buffer.from(base64Payload, 'base64');

  return JSON.parse(payload.toString());
}

/**
 * Extract the API private key from the file `app/config/parameters.php`
 * @return {string}
 */
function extractAPIPrivateKey(): string {
  const parametersData = readFileSync(global.PSConfig.parametersFile, 'utf8');
  const regexPrivateKey: RegExp = /'api_private_key' => '([\S\s]+)'/;
  const regexMatch: RegExpMatchArray|null = parametersData.match(regexPrivateKey);

  if (regexMatch === null) {
    return '';
  }

  return regexMatch[1];
}

export default {
  /**
   * Returns if a header exists
   * @param {APIResponse} response
   * @param {string} headerName
   */
  hasResponseHeader(response: APIResponse, headerName: string): boolean {
    return filterResponseHeader(response.headersArray(), headerName).length === 1;
  },

  /**
   * Returns the value of a header
   * @param {APIResponse} response
   * @param {string} headerName
   */
  getResponseHeader(response: APIResponse, headerName: string): string {
    if (!this.hasResponseHeader(response, headerName)) {
      return '';
    }
    const header = filterResponseHeader(response.headersArray(), headerName);

    return header[0].value;
  },

  /**
   * Transform a valid access token in expired access token
   * @param {string} accessToken
   */
  setAccessTokenAsExpired(accessToken: string): string {
    // Extract the header
    const header: JwtAccessHeader = parseJwtHeader(accessToken);
    // Extract the payload
    const payload: JwtAccessToken = parseJwtPayload(accessToken);

    // Set the expired date equals to the created date
    payload.exp = payload.iat;

    // Transform JSON in string
    const headerString: string = JSON.stringify(header);
    const payloadString: string = JSON.stringify(payload);

    // Transform string in base64
    const headerBase64: string = Buffer.from(headerString, 'base64').toString();
    const payloadBase64: string = Buffer.from(payloadString, 'base64').toString();

    // Sign the header & payload
    const signatureFunction: crypto.Sign = crypto.createSign('RSA-SHA256');
    signatureFunction.write(`${headerBase64}.${payloadBase64}`);
    signatureFunction.end();

    const privateKey: string = extractAPIPrivateKey();

    const signatureBase64 = signatureFunction.sign(privateKey, 'base64url');

    const headerBuffer: Buffer = Buffer.from(headerString);
    const payloadBuffer: Buffer = Buffer.from(payloadString);

    return `${headerBuffer.toString('base64')}.${payloadBuffer.toString('base64')}.${signatureBase64.toString()}`;
  },
};
