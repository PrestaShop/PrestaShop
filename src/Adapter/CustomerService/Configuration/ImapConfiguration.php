<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Persists the "Customer service options" IMAP panel that drives the
 * inbox synchronization performed when the Customer Service listing is loaded.
 *
 * Thirteen settings: connection (URL / port / user / password), behaviour
 * flags (delete after sync, create new threads), and the seven IMAP option
 * toggles native to the PHP imap_open mailbox string. The legacy
 * `PS_SAV_IMAP_OPT` concatenated value is intentionally not written: it has
 * no consumers and `syncImap` rebuilds the option string at call time from
 * the individual toggles.
 */
final class ImapConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * Form key => Configuration key. Most are 1:1 except the two `*-CERT`
     * legacy keys that contain a literal dash.
     *
     * @var array<string, string>
     */
    private const FIELD_TO_CONFIG_KEY = [
        'imap_url' => 'PS_SAV_IMAP_URL',
        'imap_port' => 'PS_SAV_IMAP_PORT',
        'imap_user' => 'PS_SAV_IMAP_USER',
        'imap_password' => 'PS_SAV_IMAP_PWD',
        'imap_delete_msg' => 'PS_SAV_IMAP_DELETE_MSG',
        'imap_create_threads' => 'PS_SAV_IMAP_CREATE_THREADS',
        'imap_opt_pop3' => 'PS_SAV_IMAP_OPT_POP3',
        'imap_opt_norsh' => 'PS_SAV_IMAP_OPT_NORSH',
        'imap_opt_ssl' => 'PS_SAV_IMAP_OPT_SSL',
        'imap_opt_validate_cert' => 'PS_SAV_IMAP_OPT_VALIDATE-CERT',
        'imap_opt_novalidate_cert' => 'PS_SAV_IMAP_OPT_NOVALIDATE-CERT',
        'imap_opt_tls' => 'PS_SAV_IMAP_OPT_TLS',
        'imap_opt_notls' => 'PS_SAV_IMAP_OPT_NOTLS',
    ];

    private const STRING_FIELDS = [
        'imap_url',
        'imap_port',
        'imap_user',
        'imap_password',
    ];

    private const BOOLEAN_FIELDS = [
        'imap_delete_msg',
        'imap_create_threads',
        'imap_opt_pop3',
        'imap_opt_norsh',
        'imap_opt_ssl',
        'imap_opt_validate_cert',
        'imap_opt_novalidate_cert',
        'imap_opt_tls',
        'imap_opt_notls',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();
        $values = [
            'imap_url' => (string) $this->configuration->get('PS_SAV_IMAP_URL', '', $shopConstraint),
            'imap_port' => (string) $this->configuration->get('PS_SAV_IMAP_PORT', '143', $shopConstraint),
            'imap_user' => (string) $this->configuration->get('PS_SAV_IMAP_USER', '', $shopConstraint),
            'imap_password' => (string) $this->configuration->get('PS_SAV_IMAP_PWD', '', $shopConstraint),
        ];

        foreach (self::BOOLEAN_FIELDS as $field) {
            $values[$field] = (bool) $this->configuration->get(self::FIELD_TO_CONFIG_KEY[$field], false, $shopConstraint);
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        // IMAP synchronization is optional: the browser submits `null` for
        // empty text/password inputs, but leaving every connection field
        // blank (sync disabled) must remain a valid, savable state.
        foreach (self::STRING_FIELDS as $field) {
            if (array_key_exists($field, $configuration)) {
                $configuration[$field] = (string) ($configuration[$field] ?? '');
            }
        }

        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            foreach (self::FIELD_TO_CONFIG_KEY as $field => $configKey) {
                // The password field renders empty in the form when the
                // stored value is masked; persisting it as-is would wipe the
                // previously saved credentials. Skip it when blank so existing
                // credentials are preserved.
                if ($field === 'imap_password' && ($configuration[$field] ?? '') === '') {
                    continue;
                }

                $this->updateConfigurationValue($configKey, $field, $configuration, $shopConstraint);
            }
        }

        return [];
    }

    /**
     * Raw connection settings needed to open the IMAP mailbox, keyed by their
     * underlying `Configuration` key rather than the form field name, and
     * correctly scoped to the current shop like every other value this class
     * exposes. Used by `SyncCustomerServiceImapMailboxHandler` so the sync
     * logic and the options form always read the exact same shop-scoped
     * settings instead of maintaining a second, hand-kept mapping.
     *
     * @return array<string, string|bool>
     */
    public function getConnectionSettings(): array
    {
        $shopConstraint = $this->getShopConstraint();
        $settings = [];

        foreach (self::FIELD_TO_CONFIG_KEY as $field => $configKey) {
            $settings[$configKey] = in_array($field, self::BOOLEAN_FIELDS, true)
                ? (bool) $this->configuration->get($configKey, false, $shopConstraint)
                : (string) $this->configuration->get($configKey, '', $shopConstraint);
        }

        return $settings;
    }

    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(array_keys(self::FIELD_TO_CONFIG_KEY))
            ->setAllowedTypes('imap_url', 'string')
            ->setAllowedTypes('imap_port', 'string')
            ->setAllowedTypes('imap_user', 'string')
            ->setAllowedTypes('imap_password', 'string');

        foreach (self::BOOLEAN_FIELDS as $field) {
            $resolver->setAllowedTypes($field, 'bool');
        }

        return $resolver;
    }
}
