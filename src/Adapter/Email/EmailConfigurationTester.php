<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Email;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\Entity\Mail;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
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
     * @var ShopConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ShopConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     * @param ShopContext $shopContext
     */
    public function __construct(
        ShopConfigurationInterface $configuration,
        TranslatorInterface $translator,
        private readonly ShopContext $shopContext,
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * Multistore flags use '0' for inheritance, '1' for overrides, and '' when no checkbox is present.
     *
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

        $shopConstraint = $this->shopContext->getShopConstraint();
        $isScoped = $this->shopContext->isMultiShopUsed() && !$this->shopContext->isAllShopContext();
        $passwordIsInherited = $isScoped && '0' === ($config['multistore_smtp_password'] ?? '');

        // Unchecked overrides must use inherited values even before saving; the DOM may still hold local values.
        if ($isScoped) {
            foreach ([
                'mail_method' => 'PS_MAIL_METHOD',
                'smtp_server' => 'PS_MAIL_SERVER',
                'smtp_username' => 'PS_MAIL_USER',
                'smtp_password' => 'PS_MAIL_PASSWD',
                'smtp_port' => 'PS_MAIL_SMTP_PORT',
                'smtp_encryption' => 'PS_MAIL_SMTP_ENCRYPTION',
                'dkim_enable' => 'PS_MAIL_DKIM_ENABLE',
                'dkim_key' => 'PS_MAIL_DKIM_KEY',
                'dkim_domain' => 'PS_MAIL_DKIM_DOMAIN',
                'dkim_selector' => 'PS_MAIL_DKIM_SELECTOR',
            ] as $field => $configurationKey) {
                if ('0' === ($config['multistore_' . $field] ?? '')) {
                    // Explicit legacy scopes avoid the adapter falling back to the current context.
                    $config[$field] = $this->shopContext->isSingleShopContext()
                        ? LegacyConfiguration::get($configurationKey, null, $this->shopContext->getShopGroupId(), 0)
                        : LegacyConfiguration::getGlobalValue($configurationKey);
                }
            }
        }

        $password = (string) ($config['smtp_password'] ?? '');
        // A blank existing override keeps its password; a new override may intentionally use an empty password.
        if (!$passwordIsInherited && '' === $password
            && (!$isScoped
                || '1' !== ($config['multistore_smtp_password'] ?? '')
                || $this->configuration->has('PS_MAIL_PASSWD', $shopConstraint->clone(true)))
        ) {
            $password = (string) $this->configuration->get('PS_MAIL_PASSWD', '', $shopConstraint);
        }

        $smtpChecked = MailOption::METHOD_SMTP === (int) $config['mail_method'];
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
            (string) $config['send_email_to'],
            (string) $this->configuration->get('PS_SHOP_EMAIL', '', $shopConstraint),
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
