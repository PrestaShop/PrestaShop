<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Webservice;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation;
use PrestaShop\PrestaShop\Core\Configuration\PhpExtensionCheckerInterface;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Looks at server configuration in order to check if PrestaShop's Webservice feature can be enabled.
 */
final class ServerRequirementsChecker implements ServerRequirementsCheckerInterface
{
    public const ISSUE_APACHE_MOD_AUTH_BASIC_NOT_AVAILABLE = 'issue_apache_mod_auth_basic_not_available';
    public const ISSUE_APACHE_MOD_AUTH_REWRITE_NOT_AVAILABLE = 'issue_apache_mod_auth_rewrite_not_available';
    public const ISSUE_EXT_SIMPLEXML_NOT_AVAILABLE = 'issue_ext_simplexml_not_available';
    public const ISSUE_HTTPS_NOT_AVAILABLE = 'issue_https_not_available';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HostingInformation
     */
    private $hostingInformation;

    /**
     * @var PhpExtensionCheckerInterface
     */
    private $phpExtensionChecker;

    /**
     * @param TranslatorInterface $translator
     * @param Configuration $configuration
     * @param HostingInformation $hostingInformation
     * @param PhpExtensionCheckerInterface $phpExtensionChecker
     */
    public function __construct(
        TranslatorInterface $translator,
        Configuration $configuration,
        HostingInformation $hostingInformation,
        PhpExtensionCheckerInterface $phpExtensionChecker
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->hostingInformation = $hostingInformation;
        $this->phpExtensionChecker = $phpExtensionChecker;
    }

    /**
     * Analyses the server configuration (apache configuration and php settings)
     * to check whether PrestaShop Webservice can be used or not.
     *
     * @return array empty if no errors
     */
    public function checkForErrors(): array
    {
        $issues = $this->lookForIssues();

        if (empty($issues)) {
            return [];
        }

        $allWarningMessages = $this->getWarningMessages();
        $selectedWarningMessages = [];

        foreach ($issues as $issue) {
            if (false === array_key_exists($issue, $allWarningMessages)) {
                throw new RuntimeException(sprintf('Unexpected configuration issue "%s"', $issue));
            }

            $selectedWarningMessages[] = $allWarningMessages[$issue];
        }

        return $selectedWarningMessages;
    }

    /**
     * @return string[]
     */
    private function lookForIssues()
    {
        $issues = [];

        if (!$this->phpExtensionChecker->loaded('SimpleXML')) {
            $issues[] = self::ISSUE_EXT_SIMPLEXML_NOT_AVAILABLE;
        }

        if (false === $this->configuration->getBoolean('PS_SSL_ENABLED')) {
            $issues[] = self::ISSUE_HTTPS_NOT_AVAILABLE;
        }

        if (!str_contains($this->hostingInformation->getServerInformation()['version'], 'Apache')) {
            return $issues;
        }

        if (function_exists('apache_get_modules')) {
            $apache_modules = apache_get_modules();

            if (false === in_array('mod_auth_basic', $apache_modules)) {
                $issues[] = self::ISSUE_APACHE_MOD_AUTH_BASIC_NOT_AVAILABLE;
            }

            if (false === in_array('mod_rewrite', $apache_modules)) {
                $issues[] = self::ISSUE_APACHE_MOD_AUTH_REWRITE_NOT_AVAILABLE;
            }
        }

        return $issues;
    }

    /**
     * @return string[]
     */
    private function getWarningMessages()
    {
        return [
            self::ISSUE_APACHE_MOD_AUTH_BASIC_NOT_AVAILABLE => $this->translator->trans(
                'Please activate the \'mod_auth_basic\' Apache module to allow the use of the PrestaShop webservice.',
                [],
                'Install'
            ),
            self::ISSUE_APACHE_MOD_AUTH_REWRITE_NOT_AVAILABLE => $this->translator->trans(
                'We could not check to see if basic authentication and rewrite extensions have been activated. Please manually check if they\'ve been activated in order to use the PrestaShop webservice.',
                [],
                'Install'
            ),
            self::ISSUE_EXT_SIMPLEXML_NOT_AVAILABLE => $this->translator->trans(
                'Please activate the \'SimpleXML\' PHP extension to allow testing of PrestaShop\'s webservice.',
                [],
                'Install'
            ),
            self::ISSUE_HTTPS_NOT_AVAILABLE => $this->translator->trans(
                'It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.',
                [],
                'Install'
            ),
        ];
    }
}
