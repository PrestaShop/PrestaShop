<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Webservice;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Looks at server configuration in order to check PrestaShop Webservice can be enabled
 */
class WebserviceCanBeEnabledConfigurationChecker
{
    const ISSUE_NOT_APACHE_SERVER = 'not_apache_server';
    const ISSUE_CANNOT_CHECK_APACHE_MODULES = 'cannot_check_apache_modules';
    const ISSUE_APACHE_MOD_AUTH_BASIC_NOT_AVAILABLE = 'issue_apache_mod_auth_basic_not_available';
    const ISSUE_APACHE_MOD_AUTH_REWRITE_NOT_AVAILABLE = 'issue_apache_mod_auth_rewrite_not_available';
    const ISSUE_EXT_SIMPLEXML_NOT_AVAILABLE = 'issue_ext_simplexml_not_available';
    const ISSUE_HTTPS_NOT_AVAILABLE = 'issue_https_not_available';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param Configuration $configuration
     */
    public function __construct(TranslatorInterface $translator, Configuration $configuration)
    {
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    /**
     * Analyses the server configuration (apache configuration and php settings)
     * to check whether PrestaShop Webservice can be used or not.
     *
     * @param Request $request optional
     * @return array
     */
    public function analyseConfigurationForIssues(Request $request = null)
    {
        $issues = $this->lookForIssues($request);

        $allWarningMessages = $this->getWarningMessages();
        $selectedWarningMessages = [];

        foreach ($issues as $issue) {
            if (false === array_key_exists($issue, $allWarningMessages)) {
                throw new \RuntimeException(sprintf('Unexpected configuration issue %s', $issue));
            }

            $selectedWarningMessages[] = $allWarningMessages[$issue];
        }

        return $selectedWarningMessages;
    }

    /**
     * @param Request $request optional
     *
     * @return string[]
     */
    private function lookForIssues(Request $request = null)
    {
        $issues = [];

        if ($request !== null) {
            if (strpos($request->server->get('SERVER_SOFTWARE'), 'Apache') === false) {
                $issues[] = self::ISSUE_NOT_APACHE_SERVER;
            }
        }

        if (function_exists('apache_get_modules')) {
            $apache_modules = apache_get_modules();

            if (false === in_array('mod_auth_basic', $apache_modules)) {
                $issues[] = self::ISSUE_APACHE_MOD_AUTH_BASIC_NOT_AVAILABLE;
            }

            if (false === in_array('mod_rewrite', $apache_modules)) {
                $issues[] = self::ISSUE_APACHE_MOD_AUTH_REWRITE_NOT_AVAILABLE;
            }

        } else {
            $issues[] = self::ISSUE_CANNOT_CHECK_APACHE_MODULES;
        }

        if (false === extension_loaded('SimpleXML')) {
            $issues[] = self::ISSUE_EXT_SIMPLEXML_NOT_AVAILABLE;
        }

        if (false === $this->configuration->getBoolean('PS_SSL_ENABLED')) {
            $issues[] = self::ISSUE_HTTPS_NOT_AVAILABLE;
        }

        return $issues;
    }

    /**
     * @return string[]
     */
    private function getWarningMessages()
    {
        return [
            self::ISSUE_NOT_APACHE_SERVER => $this->translator->trans(
                'To avoid operating problems, please use an Apache server.',
                [],
                'Admin.Advparameters.Notification'
            ),
            self::ISSUE_CANNOT_CHECK_APACHE_MODULES => $this->translator->trans(
                'Please activate the \'mod_auth_basic\' Apache module to allow authentication of PrestaShop\'s webservice.',
                [],
                'Admin.Advparameters.Notification'
            ),
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