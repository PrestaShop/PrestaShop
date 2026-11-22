<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Email\EmailConfigurationInheritance;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;

/**
 * Class EmailConfigurationFormDataProvider.
 */
final class EmailConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $emailDataConfigurator;

    /**
     * @param DataConfigurationInterface $emailDataConfigurator
     * @param ShopConfigurationInterface $configuration
     * @param ShopContext $shopContext
     */
    public function __construct(
        DataConfigurationInterface $emailDataConfigurator,
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopContext $shopContext,
    ) {
        $this->emailDataConfigurator = $emailDataConfigurator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->emailDataConfigurator->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // Removing overrides can combine SMTP settings from different scopes; validate the resulting values.
        $errors = $this->checkSmtpConfiguration($this->getEffectiveSmtpConfiguration($data));
        if (!empty($errors)) {
            return $errors;
        }

        return $this->emailDataConfigurator->updateConfiguration($data);
    }

    /**
     * @return array{mail_method: mixed, server: mixed, port: mixed} Values after applying override changes
     */
    private function getEffectiveSmtpConfiguration(array $data): array
    {
        $effectiveConfiguration = [];
        $shopConstraint = $this->shopContext->getShopConstraint();
        $isScoped = $this->shopContext->isMultiShopUsed() && !$this->shopContext->isAllShopContext();
        $parentConstraint = $isScoped ? EmailConfigurationInheritance::getParentShopConstraint($this->shopContext) : null;
        $prefix = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX;

        foreach (['mail_method' => 'PS_MAIL_METHOD', 'server' => 'PS_MAIL_SERVER', 'port' => 'PS_MAIL_SMTP_PORT'] as $field => $configurationKey) {
            $input = 'mail_method' === $field ? $data : ($data['smtp_config'] ?? []);
            if (!array_key_exists($field, $input)) {
                $effectiveConfiguration[$field] = $this->configuration->get($configurationKey, null, $shopConstraint);
            } elseif ($isScoped && true !== ($input[$prefix . $field] ?? false)) {
                $effectiveConfiguration[$field] = $this->configuration->get($configurationKey, null, $parentConstraint);
            } else {
                $effectiveConfiguration[$field] = $input[$field];
            }
        }

        return $effectiveConfiguration;
    }

    /**
     * Check if SMTP is configured if SMTP mail method is selected.
     *
     * @param array $config
     *
     * @return array
     */
    private function checkSmtpConfiguration(array $config)
    {
        $errors = [];
        $isSmtpNotConfigured = empty($config['server']) || empty($config['port']);

        if (MailOption::METHOD_SMTP === (int) $config['mail_method'] && $isSmtpNotConfigured) {
            $errors[] = [
                'key' => 'You must define an SMTP server and an SMTP port. If you do not know it, use the PHP mail() function instead.',
                'parameters' => [],
                'domain' => 'Admin.Shopparameters.Notification',
            ];
        }

        return $errors;
    }
}
