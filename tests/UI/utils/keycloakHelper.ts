import KcAdminClient from '@keycloak/keycloak-admin-client';
import type ClientRepresentation from '@keycloak/keycloak-admin-client/lib/defs/clientRepresentation.js';

const kcAdminClient: KcAdminClient = new KcAdminClient();

/**
 * @description Helper to use Keycloak
 */
export default {
  /**
   * Create a client
   * @param {string} clientId
   * @param {string} name
   * @param {boolean} publicClient
   * @param {boolean} authorizationServicesEnabled
   * @return {Promise<ClientRepresentation|undefined>}
   */
  async createClient(
    clientId: string,
    name: string,
    publicClient: boolean,
    authorizationServicesEnabled: boolean,
  ): Promise<ClientRepresentation|undefined> {
    await kcAdminClient.auth({
      username: global.keycloakConfig.keycloakAdminUser,
      password: global.keycloakConfig.keycloakAdminPass,
      grantType: 'password',
      clientId: 'admin-cli',
    });

    const client: {id: string} = await kcAdminClient.clients.create({
      realm: 'master',
      clientId,
      name,
      publicClient,
      authorizationServicesEnabled,
    });

    return kcAdminClient.clients.findOne(client);
  },
};
