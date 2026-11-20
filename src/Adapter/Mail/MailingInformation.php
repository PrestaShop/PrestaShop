<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Mail;

use Configuration;
use PrestaShop\PrestaShop\Core\Email\MailOption;

/**
 * Retrieve mailing information.
 */
class MailingInformation
{
    /**
     * @return bool
     */
    public function isNativeMailUsed()
    {
        return Configuration::get('PS_MAIL_METHOD') == 1;
    }

    /**
     * Check whether emails are sent using PHP's mail() function.
     */
    public function isPhpMailUsed(): bool
    {
        return (int) Configuration::get('PS_MAIL_METHOD') === MailOption::METHOD_PHP_MAIL;
    }

    /**
     * @return array
     */
    public function getSmtpInformation()
    {
        return [
            'server' => Configuration::get('PS_MAIL_SERVER'),
            'user' => Configuration::get('PS_MAIL_USER'),
            'password' => Configuration::get('PS_MAIL_PASSWD'),
            'encryption' => Configuration::get('PS_MAIL_SMTP_ENCRYPTION'),
            'port' => Configuration::get('PS_MAIL_SMTP_PORT'),
        ];
    }
}
