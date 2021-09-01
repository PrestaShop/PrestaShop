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

use PrestaShopBundle\Install\System;

/**
 * Step 2 : check system configuration (permissions on folders, PHP version, etc.)
 */
class InstallControllerHttpSystem extends InstallControllerHttp implements HttpConfigureInterface
{
    public $tests = [];

    /**
     * @var System
     */
    public $model_system;

    /**
     * @see HttpConfigureInterface::init()
     */
    public function init()
    {
        $this->model_system = new System();
        $this->model_system->setTranslator($this->translator);
    }

    /**
     * @see HttpConfigureInterface::processNextStep()
     */
    public function processNextStep()
    {
    }

    /**
     * Required tests must be passed to validate this step
     *
     * @see HttpConfigureInterface::validate()
     */
    public function validate()
    {
        $this->tests['required'] = $this->model_system->checkRequiredTests();

        return $this->tests['required']['success'];
    }

    /**
     * Display system step
     */
    public function display()
    {
        if (!isset($this->tests['required'])) {
            $this->tests['required'] = $this->model_system->checkRequiredTests();
        }
        if (!isset($this->tests['optional'])) {
            $this->tests['optional'] = $this->model_system->checkOptionalTests();
        }

        $testsRequiredsf2 = $this->model_system->checkSf2Requirements();
        $testsOptionalsf2 = $this->model_system->checkSf2Recommendations();

        if (!is_callable('getenv') || !($user = @getenv('APACHE_RUN_USER'))) {
            $user = 'Apache';
        }

        // Generate display array
        $this->tests_render = [
            'required' => [
                [
                    'title' => $this->translator->trans('Required PHP parameters', [], 'Install'),
                    'success' => 1,
                    'checks' => [
                        'phpversion' => $this->translator->trans('PHP %version% or later is not enabled', ['%version%' => _PS_INSTALL_MINIMUM_PHP_VERSION_], 'Install'),
                        'upload' => $this->translator->trans('Cannot upload files', [], 'Install'),
                        'system' => $this->translator->trans('Cannot create new files and folders', [], 'Install'),
                        'curl' => $this->translator->trans('cURL extension is not enabled', [], 'Install'),
                        'gd' => $this->translator->trans('GD library is not installed', [], 'Install'),
                        'json' => $this->translator->trans('JSON extension is not loaded', [], 'Install'),
                        'openssl' => $this->translator->trans('PHP OpenSSL extension is not loaded', [], 'Install'),
                        'pdo_mysql' => $this->translator->trans('PDO MySQL extension is not loaded', [], 'Install'),
                        'simplexml' => $this->translator->trans('SimpleXML extension is not loaded', [], 'Install'),
                        'zip' => $this->translator->trans('ZIP extension is not enabled', [], 'Install'),
                        'fileinfo' => $this->translator->trans('Fileinfo extension is not enabled', [], 'Install'),
                        'intl' => $this->translator->trans('Intl extension is not loaded', [], 'Install'),
                        'memory_limit' => $this->translator->trans('PHP\'s config "memory_limit" must be to a minimum of 256M', [], 'Install'),
                    ],
                ],
                [
                    'title' => $this->translator->trans('Required Apache configuration', [], 'Install'),
                    'success' => 1,
                    'checks' => [
                        'apache_mod_rewrite' => $this->translator->trans('Enable the Apache mod_rewrite module', [], 'Install'),
                    ],
                ],
                [
                    'title' => $this->translator->trans('Files', [], 'Install'),
                    'success' => 1,
                    'checks' => [
                        'files' => $this->translator->trans('Not all files were successfully uploaded on your server', [], 'Install'),
                    ],
                ],
                [
                    'title' => $this->translator->trans('Permissions on files and folders', [], 'Install'),
                    'success' => 1,
                    'checks' => [
                        'config_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/config/'], 'Install'),
                        'cache_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/var/cache/'], 'Install'),
                        'log_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/var/logs/'], 'Install'),
                        'img_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/img/'], 'Install'),
                        'mails_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/mails/'], 'Install'),
                        'module_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/modules/'], 'Install'),
                        'theme_lang_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/themes/' . _THEME_NAME_ . '/lang/'], 'Install'),
                        'theme_pdf_lang_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/themes/' . _THEME_NAME_ . '/pdf/lang/'], 'Install'),
                        'theme_cache_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/themes/' . _THEME_NAME_ . '/cache/'], 'Install'),
                        'translations_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/translations/'], 'Install'),
                        'customizable_products_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/upload/'], 'Install'),
                        'virtual_products_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/download/'], 'Install'),
                        'config_sf2_dir' => $this->translator->trans('Write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/app/config/'], 'Install'),
                        'translations_sf2' => $this->translator->trans('Write permissions for %user% user on %folder%', ['%user%' => $user, '%folder%' => '~/app/Resources/translations/'], 'Install'),
                    ],
                ],
            ],
            'optional' => [
                [
                    'title' => $this->translator->trans('Recommended PHP parameters', [], 'Install'),
                    'success' => $this->tests['optional']['success'],
                    'checks' => [
                        'gz' => $this->translator->trans('GZIP compression is not activated', [], 'Install'),
                        'mbstring' => $this->translator->trans('Mbstring extension is not enabled', [], 'Install'),
                        'dom' => $this->translator->trans('Dom extension is not loaded', [], 'Install'),
                        'fopen' => $this->translator->trans('Cannot open external URLs (requires allow_url_fopen as On)', [], 'Install'),
                    ],
                ],
            ],
        ];

        //Inject Sf2 errors to test render required
        foreach ($testsRequiredsf2 as $error) {
            $this->tests_render['required'][2]['checks'][] = $this->translator->trans($error->getHelpHtml(), [], 'Install');
        }

        //Inject Sf2 optionnal config to test render optional
        foreach ($testsOptionalsf2 as $error) {
            $this->tests_render['optional'][0]['checks'][] = $this->translator->trans($error->getHelpHtml(), [], 'Install');
        }

        foreach ($this->tests_render['required'] as &$category) {
            foreach ($category['checks'] as $id => $check) {
                if (!isset($this->tests['required']['checks'][$id]) || $this->tests['required']['checks'][$id] != 'ok') {
                    $category['success'] = 0;
                }
            }
        }
        unset($category);

        //if sf2 requirement error found, force the required success to false
        if (count($testsRequiredsf2) > 0) {
            $this->tests['required']['success'] = false;
        }

        // If required tests failed, disable next button
        if (!$this->tests['required']['success']) {
            $this->next_button = false;
        }

        $this->displayContent('system');
    }
}
