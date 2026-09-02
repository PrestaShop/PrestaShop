<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class will manage Logs configuration for a Shop.
 */
class LogsConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Validate
     */
    private $validate;

    public function __construct(ConfigurationInterface $configuration, TranslatorInterface $translator, Validate $validate)
    {
        $this->configuration = $configuration;
        $this->translator = $translator;
        $this->validate = $validate;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'logs_by_email' => $this->configuration->get('PS_LOGS_BY_EMAIL'),
            'logs_email_receivers' => $this->configuration->get('PS_LOGS_EMAIL_RECEIVERS'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $checkEmails = explode(',', $configuration['logs_email_receivers']);
            $errors = [];
            $invalidEmails = [];

            foreach ($checkEmails as $email) {
                if (!$this->validate->isEmail($email)) {
                    $invalidEmails[] = $email;
                }
            }

            if (!empty($invalidEmails)) {
                $nbInvalidEmails = count($invalidEmails);

                if ($nbInvalidEmails > 1) {
                    $errors[] = $this->translator->trans(
                        'Invalid emails: %invalid_emails%.',
                        ['%invalid_emails%' => implode(',', $invalidEmails)],
                        'Admin.Notifications.Error'
                    );
                } else {
                    $errors[] = $this->translator->trans(
                        'Invalid email: %invalid_email%.',
                        ['%invalid_email%' => implode(',', $invalidEmails)],
                        'Admin.Notifications.Error'
                    );
                }
            }

            if ($errors) {
                return $errors;
            }

            $this->configuration->set('PS_LOGS_BY_EMAIL', $configuration['logs_by_email']);
            $this->configuration->set('PS_LOGS_EMAIL_RECEIVERS', $configuration['logs_email_receivers']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(['logs_by_email', 'logs_email_receivers'])
            ->resolve($configuration);

        return true;
    }
}
