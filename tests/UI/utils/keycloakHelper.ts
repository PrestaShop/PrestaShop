import {KeycloakAdminClient} from '@s3pweb/keycloak-admin-client-cjs';
import type ClientRepresentation from '@keycloak/keycloak-admin-client/lib/defs/clientRepresentation.js';

const kcAdminClient: KeycloakAdminClient = new KeycloakAdminClient({
  baseUrl: global.keycloakConfig.keycloakExternalUrl,
  realmName: 'master',
});

/**
 * @description Helper to use Keycloak
 */
export default {
  /**
   * Auth for Keycloak
   * @return {Promise<void>}
   */
  async auth(): Promise<void> {
    await kcAdminClient.auth({
      username: global.keycloakConfig.keycloakAdminUser,
      password: global.keycloakConfig.keycloakAdminPass,
      grantType: 'password',
      clientId: 'admin-cli',
      clientSecret: 'Psip5UvTO1EXUEwzb15nxLWnwdU1Nlcg',
    });
  },

  /**
   * Create a client (and returns the access token)
   * @param {string} kcClientId
   * @param {string} kcClientName
   * @param {boolean} publicClient : If false, needs a client authentication
   * @param {boolean} authorizationServicesEnabled : If true, enable fine-grained authorization support
   * @return {Promise<string>}
   */
  async createClient(
    kcClientId: string,
    kcClientName: string,
    publicClient: boolean,
    authorizationServicesEnabled: boolean,
  ): Promise<string> {
    await this.auth();

    const idClient: {id: string} = await kcAdminClient.clients.create({
      enabled: true,
      protocol: 'openid-connect',
      clientId: kcClientId,
      name: kcClientName,
      publicClient,
      authorizationServicesEnabled,
      standardFlowEnabled: true,
      directAccessGrantsEnabled: true,
      serviceAccountsEnabled: true,
    });

    const client: ClientRepresentation|undefined = await kcAdminClient.clients.findOne(idClient);

    if (!client) {
      return '';
    }
    return client.secret ?? '';
  },

  /**
   * Remove a client
   * @param {string} kcClientId
   * @return {Promise<boolean>}
   */
  async removeClient(kcClientId: string): Promise<boolean> {
    await this.auth();

    const clients: ClientRepresentation[] = await kcAdminClient.clients.find({
      clientId: kcClientId,
    });

    if (!clients[0].id) {
      return false;
    }
    await kcAdminClient.clients.del({
      id: clients[0].id,
    });

    return true;
  },
};
