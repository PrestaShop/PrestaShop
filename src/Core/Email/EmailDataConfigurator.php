<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EmailDataConfigurator is responsible for configuring email data.
 */
final class EmailDataConfigurator extends AbstractMultistoreConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'send_emails_to' => $this->configuration->get('PS_MAIL_EMAIL_MESSAGE', false, $shopConstraint),
            'mail_method' => (int) $this->configuration->get('PS_MAIL_METHOD', false, $shopConstraint),
            'subject_prefix' => (bool) $this->configuration->get('PS_MAIL_SUBJECT_PREFIX', false, $shopConstraint),
            'mail_type' => (int) $this->configuration->get('PS_MAIL_TYPE', false, $shopConstraint),
            'log_emails' => (bool) $this->configuration->get('PS_LOG_EMAILS', false, $shopConstraint),
            'smtp_config' => [
                'domain' => (string) $this->configuration->get('PS_MAIL_DOMAIN', false, $shopConstraint),
                'server' => $this->configuration->get('PS_MAIL_SERVER', false, $shopConstraint),
                'username' => (string) $this->configuration->get('PS_MAIL_USER', false, $shopConstraint),
                'password' => (string) $this->configuration->get('PS_MAIL_PASSWD', false, $shopConstraint),
                'encryption' => (string) $this->configuration->get('PS_MAIL_SMTP_ENCRYPTION', false, $shopConstraint),
                'port' => $this->configuration->get('PS_MAIL_SMTP_PORT', false, $shopConstraint),
            ],
            'dkim_enable' => (bool) $this->configuration->get('PS_MAIL_DKIM_ENABLE', false, $shopConstraint),
            'dkim_config' => [
                'domain' => (string) $this->configuration->get('PS_MAIL_DKIM_DOMAIN', false, $shopConstraint),
                'selector' => (string) $this->configuration->get('PS_MAIL_DKIM_SELECTOR', false, $shopConstraint),
                'key' => (string) $this->configuration->get('PS_MAIL_DKIM_KEY', false, $shopConstraint),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();
            $config = $this->normalizeMultistoreFlags($config);
            $smtpConfig = $this->normalizeMultistoreFlags($config['smtp_config'] ?? []);
            $dkimConfig = $this->normalizeMultistoreFlags($config['dkim_config'] ?? []);
            $isScoped = $this->multistoreFeature->isUsed() && !$this->shopContext->isAllShopContext();

            if ($this->shouldPreserveExistingPassword($smtpConfig, $isScoped, $shopConstraint)) {
                unset($smtpConfig['password']);
            }

            $this->updateConfigurationValue('PS_MAIL_EMAIL_MESSAGE', 'send_emails_to', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_METHOD', 'mail_method', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SUBJECT_PREFIX', 'subject_prefix', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_TYPE', 'mail_type', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LOG_EMAILS', 'log_emails', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_ENABLE', 'dkim_enable', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_DOMAIN', 'domain', $dkimConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_SELECTOR', 'selector', $dkimConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_KEY', 'key', $dkimConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DOMAIN', 'domain', $smtpConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SERVER', 'server', $smtpConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_USER', 'username', $smtpConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SMTP_ENCRYPTION', 'encryption', $smtpConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SMTP_PORT', 'port', $smtpConfig, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_PASSWD', 'password', $smtpConfig, $shopConstraint);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config): bool
    {
        parent::validateConfiguration($config);

        $flagResolver = new OptionsResolver();
        $flags = [];
        foreach ($config as $field => $value) {
            if (str_starts_with($field, MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX)) {
                $flagResolver->setDefined($field)->setAllowedTypes($field, ['bool', 'null']);
                $flags[$field] = $value;
            }
        }
        $flagResolver->resolve($flags);

        if (isset($config['smtp_config'])) {
            $this->buildNestedResolver([
                'domain' => 'string',
                'server' => ['string', 'bool', 'null'],
                'username' => 'string',
                'password' => 'string',
                'encryption' => 'string',
                'port' => ['string', 'int', 'bool', 'null'],
            ])->resolve($config['smtp_config']);
        }

        if (isset($config['dkim_config'])) {
            $this->buildNestedResolver([
                'domain' => 'string',
                'selector' => 'string',
                'key' => 'string',
            ])->resolve($config['dkim_config']);
        }

        return true;
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(['send_emails_to', 'mail_method', 'subject_prefix', 'mail_type', 'log_emails', 'dkim_enable', 'smtp_config', 'dkim_config'])
            ->setAllowedTypes('send_emails_to', ['string', 'int', 'bool'])
            ->setAllowedTypes('mail_method', 'int')
            ->setAllowedTypes('subject_prefix', 'bool')
            ->setAllowedTypes('mail_type', 'int')
            ->setAllowedTypes('log_emails', 'bool')
            ->setAllowedTypes('dkim_enable', 'bool')
            ->setAllowedTypes('smtp_config', 'array')
            ->setAllowedTypes('dkim_config', 'array');
    }

    private function shouldPreserveExistingPassword(array $smtpConfig, bool $isScoped, ?ShopConstraint $shopConstraint): bool
    {
        if (!array_key_exists('password', $smtpConfig) || '' !== $smtpConfig['password']) {
            return false;
        }

        if ($isScoped) {
            // Preserve only an active existing override; unchecked and new overrides must reach the writer.
            $passwordOverride = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'password';

            return isset($smtpConfig[$passwordOverride])
                && $this->configuration->has('PS_MAIL_PASSWD', $shopConstraint->clone(true));
        }

        return '' !== (string) $this->configuration->get('PS_MAIL_PASSWD', false, $shopConstraint);
    }

    /**
     * @param array<string, string|string[]> $fieldTypes Allowed types for fields within one subform
     */
    private function buildNestedResolver(array $fieldTypes): OptionsResolver
    {
        $resolver = new OptionsResolver();
        foreach ($fieldTypes as $field => $types) {
            $resolver->setDefined($field)->setAllowedTypes($field, $types);

            if ($this->multistoreFeature->isUsed() && !$this->shopContext->isAllShopContext()) {
                $flag = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $field;
                $resolver->setDefined($flag)->setAllowedTypes($flag, ['bool', 'null']);
            } else {
                $resolver->setRequired($field);
            }
        }

        return $resolver;
    }

    private function normalizeMultistoreFlags(array $config): array
    {
        foreach ($config as $field => $value) {
            if (str_starts_with($field, MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX) && false === $value) {
                unset($config[$field]);
            }
        }

        return $config;
    }
}
