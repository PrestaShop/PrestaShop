<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Webservice;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation;
use PrestaShop\PrestaShop\Core\Configuration\PhpExtensionCheckerInterface;
use RuntimeException;
use Symfony\Component\Translation\TranslatorInterface;

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
    public function checkForErrors()
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

        if (false === strpos($this->hostingInformation->getServerInformation()['version'], 'Apache')) {
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
                'Please activate the \'mod_rewrite\' Apache module to allow the PrestaShop webservice.',
                [],
                'Admin.Advparameters.Notification'
            ),
            self::ISSUE_APACHE_MOD_AUTH_REWRITE_NOT_AVAILABLE => $this->translator->trans(
                'We could not check to see if basic authentication and rewrite extensions have been activated. Please manually check if they\'ve been activated in order to use the PrestaShop webservice.',
                [],
                'Admin.Advparameters.Notification'
            ),
            self::ISSUE_EXT_SIMPLEXML_NOT_AVAILABLE => $this->translator->trans(
                'Please activate the \'SimpleXML\' PHP extension to allow testing of PrestaShop\'s webservice.',
                [],
                'Admin.Advparameters.Notification'
            ),
            self::ISSUE_HTTPS_NOT_AVAILABLE => $this->translator->trans(
                'It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.',
                [],
                'Admin.Advparameters.Notification'
            ),
        ];
    }
}
