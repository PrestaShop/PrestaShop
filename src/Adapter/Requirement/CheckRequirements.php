<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter\Requirement;

use ConfigurationTest;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Check system requirements of a PrestaShop website.
 */
class CheckRequirements
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns a summary of all system requirements.
     *
     * @return array
     */
    public function getSummary()
    {
        $paramsRequiredResults = ConfigurationTest::check(ConfigurationTest::getDefaultTests());

        $isHostMode = defined('_PS_HOST_MODE_');

        if (!$isHostMode) {
            $paramsOptionalResults = ConfigurationTest::check(ConfigurationTest::getDefaultTestsOp());
        }

        $failRequired = in_array('fail', $paramsRequiredResults);

        $testsErrors = $this->getErrorMessages();

        if ($failRequired && 'ok' !== $paramsRequiredResults['files']) {
            $tmp = ConfigurationTest::test_files(true);
            if (is_array($tmp) && count($tmp)) {
                $testsErrors['files'] = $testsErrors['files'] . '<br/>(' . implode(', ', $tmp) . ')';
            }
        }

        $testsErrors = $this->fillMissingDescriptions($testsErrors, $paramsRequiredResults);

        $results = array(
            'failRequired' => $failRequired,
            'testsErrors' => $testsErrors,
            'testsRequired' => $paramsRequiredResults,
        );

        if (!$isHostMode) {
            $results = array_merge($results, array(
                'testsErrors' => $this->fillMissingDescriptions($testsErrors, $paramsOptionalResults),
                'failOptional' => in_array('fail', $paramsOptionalResults),
                'testsOptional' => $paramsOptionalResults,
            ));
        }

        return $results;
    }

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        return array(
            'phpversion' => $this->translator->trans('Update your PHP version.', array(), 'Admin.Advparameters.Notification'),
            'upload' => $this->translator->trans('Configure your server to allow file uploads.', array(), 'Admin.Advparameters.Notification'),
            'system' => $this->translator->trans('Configure your server to allow the creation of directories and files with write permissions.', array(), 'Admin.Advparameters.Notification'),
            'curl' => $this->translator->trans('Enable the CURL extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'dom' => $this->translator->trans('Enable the DOM extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'fileinfo' => $this->translator->trans('Enable the Fileinfo extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'gd' => $this->translator->trans('Enable the GD library on your server.', array(), 'Admin.Advparameters.Notification'),
            'json' => $this->translator->trans('Enable the JSON extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'mbstring' => $this->translator->trans('Enable the Mbstring extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'openssl' => $this->translator->trans('Enable the OpenSSL extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'pdo_mysql' => $this->translator->trans('Enable the PDO Mysql extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'simplexml' => $this->translator->trans('Enable the XML extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'zip' => $this->translator->trans('Enable the ZIP extension on your server.', array(), 'Admin.Advparameters.Notification'),
            'mysql_support' => $this->translator->trans('Enable the MySQL support on your server.', array(), 'Admin.Advparameters.Notification'),
            'config_dir' => $this->translator->trans('Set write permissions for the "config" folder.', array(), 'Admin.Advparameters.Notification'),
            'cache_dir' => $this->translator->trans('Set write permissions for the "cache" folder.', array(), 'Admin.Advparameters.Notification'),
            'sitemap' => $this->translator->trans('Set write permissions for the "sitemap.xml" file.', array(), 'Admin.Advparameters.Notification'),
            'img_dir' => $this->translator->trans('Set write permissions for the "img" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'log_dir' => $this->translator->trans('Set write permissions for the "log" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'mails_dir' => $this->translator->trans('Set write permissions for the "mails" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'module_dir' => $this->translator->trans('Set write permissions for the "modules" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'theme_cache_dir' => $this->translator->trans('Set write permissions for the "themes/%s/cache/" folder and subfolders, recursively.', array('%s' => _THEME_NAME_), 'Admin.Advparameters.Notification'),
            'theme_lang_dir' => $this->translator->trans('Set write permissions for the "themes/%s/lang/" folder and subfolders, recursively.', array('%s' => _THEME_NAME_), 'Admin.Advparameters.Notification'),
            'theme_pdf_lang_dir' => $this->translator->trans('Set write permissions for the "themes/%s/pdf/lang/" folder and subfolders, recursively.', array('%s' => _THEME_NAME_), 'Admin.Advparameters.Notification'),
            'config_sf2_dir' => $this->translator->trans('Set write permissions for the "app/config/" folder and subfolders, recursively.', array(), 'Admin.Advparameters.Notification'),
            'translations_sf2' => $this->translator->trans('Set write permissions for the "app/Resources/translations/" folder and subfolders, recursively.', array(), 'Admin.Advparameters.Notification'),
            'translations_dir' => $this->translator->trans('Set write permissions for the "translations" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'customizable_products_dir' => $this->translator->trans('Set write permissions for the "upload" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'virtual_products_dir' => $this->translator->trans('Set write permissions for the "download" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'fopen' => $this->translator->trans('Allow the PHP fopen() function on your server.', array(), 'Admin.Advparameters.Notification'),
            'gz' => $this->translator->trans('Enable GZIP compression on your server.', array(), 'Admin.Advparameters.Notification'),
            'files' => $this->translator->trans('Some PrestaShop files are missing from your server.', array(), 'Admin.Advparameters.Notification'),
            'new_phpversion' => $this->translator->trans('You are using PHP %s version. Soon, the latest PHP version supported by PrestaShop will be PHP 5.6. To make sure you’re ready for the future, we recommend you to upgrade to PHP 5.6 now!', array('%s' => phpversion()), 'Admin.Advparameters.Notification'),
            'apache_mod_rewrite' => $this->translator->trans('Enable the Apache mod_rewrite module', array(), 'Admin.Advparameters.Notification'),
        );
    }

    /**
     * Add default message on missing check descriptions
     *
     * @param array $errorMessages
     * @param array $checks
     *
     * @return array Error messages with fallback for missing entries
     */
    private function fillMissingDescriptions($errorMessages, $checks)
    {
        foreach (array_keys(array_diff_key($checks, $errorMessages)) as $key) {
            $errorMessages[$key] = $this->translator->trans('%key% (missing description)', array('%key%' => $key), 'Admin.Advparameters.Feature');
        }
        return $errorMessages;
    }
}
