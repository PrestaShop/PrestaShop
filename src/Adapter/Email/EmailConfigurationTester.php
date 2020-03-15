<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Email;

use PrestaShop\PrestaShop\Adapter\Entity\Mail;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Email\EmailConfigurationTesterInterface;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use Symfony\Component\Translation\TranslatorInterface;

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
     * {@inheritdoc}
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
            Tools::htmlentitiesUTF8($content),
            Tools::htmlentitiesUTF8($subject),
            Tools::htmlentitiesUTF8('text/html'),
            Tools::htmlentitiesUTF8($config['send_email_to']),
            Tools::htmlentitiesUTF8($this->configuration->get('PS_SHOP_EMAIL')),
            Tools::htmlentitiesUTF8($config['smtp_username']),
            $password,
            Tools::htmlentitiesUTF8($config['smtp_port']),
            Tools::htmlentitiesUTF8($config['smtp_encryption'])
        );

        $errors = [];

        if (false === $result || is_string($result)) {
            $errors[] =
                $this->translator->trans('Error: Please check your configuration', [], 'Admin.Advparameters.Feature');
        }

        if (is_string($result)) {
            $errors[] = $result;
        }

        return $errors;
    }
}
