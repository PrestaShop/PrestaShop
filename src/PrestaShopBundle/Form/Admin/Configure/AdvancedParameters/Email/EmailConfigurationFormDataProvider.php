<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

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
     */
    public function __construct(
        DataConfigurationInterface $emailDataConfigurator
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
        $errors = $this->checkSmtpConfiguration($data);
        if (!empty($errors)) {
            return $errors;
        }

        return $this->emailDataConfigurator->updateConfiguration($data);
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
        $isSmtpNotConfigured = empty($config['smtp_config']['server']) || empty($config['smtp_config']['port']);

        if (MailOption::METHOD_SMTP === $config['mail_method'] && $isSmtpNotConfigured) {
            $errors[] = [
                'key' => 'You must define an SMTP server and an SMTP port. If you do not know it, use the PHP mail() function instead.',
                'parameters' => [],
                'domain' => 'Admin.Shopparameters.Notification',
            ];
        }

        return $errors;
    }
}
