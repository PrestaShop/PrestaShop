<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Email;

use PrestaShop\PrestaShop\Adapter\Entity\Mail;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Email\EmailConfigurationTesterInterface;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmailConfigurationTester is responsible for sending test email.
 *
 * @internal
 */
final class EmailConfigurationTester implements EmailConfigurationTesterInterface
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
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * @param array $config
     *
     * @return array<int, string>
     */
    public function testConfiguration(array $config)
    {
        $content = $this->translator->trans(
            'This is a test message. Your server is now configured to send email.',
            [],
            'Admin.Advparameters.Feature'
        );
        $subject = $this->translator->trans('Test message -- Prestashop', [], 'Admin.Advparameters.Feature');

        $smtpChecked = MailOption::METHOD_SMTP === (int) $config['mail_method'];

        $password = !empty($config['smtp_password']) ?
            urldecode($config['smtp_password']) :
            $this->configuration->get('PS_MAIL_PASSWD');
        $password = str_replace(
            ['&lt;', '&gt;', '&quot;', '&amp;'],
            ['<', '>', '"', '&'],
            Tools::htmlentitiesUTF8($password)
        );

        $result = Mail::sendMailTest(
            Tools::htmlentitiesUTF8($smtpChecked),
            Tools::htmlentitiesUTF8($config['smtp_server']),
            Tools::htmlentitiesDecodeUTF8($content),
            Tools::htmlentitiesDecodeUTF8($subject),
            Tools::htmlentitiesUTF8('text/html'),
            Tools::htmlentitiesUTF8($config['send_email_to']),
            Tools::htmlentitiesUTF8($this->configuration->get('PS_SHOP_EMAIL')),
            Tools::htmlentitiesUTF8($config['smtp_username']),
            $password,
            Tools::htmlentitiesUTF8($config['smtp_port']),
            Tools::htmlentitiesUTF8($config['smtp_encryption']),
            (bool) $config['dkim_enable'],
            (string) $config['dkim_key'],
            (string) $config['dkim_domain'],
            (string) $config['dkim_selector']
        );

        $errors = [];

        if (false === $result || is_string($result)) {
            $errors[] = $this->translator->trans(
                'An error has occurred. Please check your configuration',
                [],
                'Admin.Advparameters.Feature'
            );
        }

        if (is_string($result)) {
            $errors[] = $result;
        }

        return $errors;
    }
}
