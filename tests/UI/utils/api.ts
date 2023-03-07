import type {APIResponse} from 'playwright';
import crypto from 'crypto';
import {readFileSync} from 'fs';
import path from 'path';

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

function filterResponseHeader(headers: HttpHeader[], headerName: string): HttpHeader[] {
  return headers.filter((value: HttpHeader) => value.name === headerName);
}

function parseJwtHeader(token: string): JwtAccessHeader {
  const base64Payload: string = token.split('.')[0];
  const payload: Buffer = Buffer.from(base64Payload, 'base64');

  return JSON.parse(payload.toString());
}
function parseJwtPayload(token: string): JwtAccessToken {
  const base64Payload: string = token.split('.')[1];
  const payload: Buffer = Buffer.from(base64Payload, 'base64');

  return JSON.parse(payload.toString());
}

function extractAPIPrivateKey(): string {
  const parametersFile: string = path.resolve(__dirname, '../../../', 'app/config/parameters.php');
  const parametersData = readFileSync(parametersFile, 'utf8');
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
    const headerBase64: string = atob(headerString);
    const payloadBase64: string = atob(payloadString);

    // Sign the header & payload
    const signatureFunction: crypto.Sign = crypto.createSign('RSA-SHA256');
    signatureFunction.write(`${headerBase64}.${payloadBase64}`);
    signatureFunction.end();

    const privateKey: string = extractAPIPrivateKey();

    const signatureBase64 = signatureFunction.sign(privateKey, 'base64url');

    const headerBuffer: Buffer = Buffer.from(headerString);
    const payloadBuffer: Buffer = Buffer.from(payloadString);

    return `${headerBuffer.toString('base64')}.${payloadBuffer.toString('base64')}.${btoa(signatureBase64)}`;
  },
};
