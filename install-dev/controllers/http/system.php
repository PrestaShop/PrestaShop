<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShopBundle\Install\System;

/**
 * Step 2 : check system configuration (permissions on folders, PHP version, etc.)
 */
class InstallControllerHttpSystem extends InstallControllerHttp implements HttpConfigureInterface
{
    public $tests = array();

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
        $this->tests_render = array(
            'required' => array(
                array(
                    'title' => $this->translator->trans('Required PHP parameters', array(), 'Install'),
                    'success' => 1,
                    'checks' => array(
                        'phpversion' => $this->translator->trans('PHP 5.4 or later is not enabled', array(), 'Install'),
                        'upload' => $this->translator->trans('Cannot upload files', array(), 'Install'),
                        'system' => $this->translator->trans('Cannot create new files and folders', array(), 'Install'),
                        'curl' => $this->translator->trans('cURL extension is not enabled', array(), 'Install'),
                        'gd' => $this->translator->trans('GD library is not installed', array(), 'Install'),
                        'openssl' => $this->translator->trans('PHP OpenSSL extension is not loaded', array(), 'Install'),
                        'pdo_mysql' => $this->translator->trans('PDO MySQL extension is not loaded', array(), 'Install'),
                        'zip' => $this->translator->trans('ZIP extension is not enabled', array(), 'Install'),
                    )
                ),
                array(
                    'title' => $this->translator->trans('Required Apache configuration', array(), 'Install'),
                    'success' => 1,
                    'checks' => array(
                        'apache_mod_rewrite' => $this->translator->trans('Enable the Apache mod_rewrite module', array(), 'Install'),
                    )
                ),
                array(
                    'title' => $this->translator->trans('Files', array(), 'Install'),
                    'success' => 1,
                    'checks' => array(
                        'files' => $this->translator->trans('Not all files were successfully uploaded on your server', array(), 'Install'),
                    )
                ),
                array(
                    'title' => $this->translator->trans('Permissions on files and folders', array(), 'Install'),
                    'success' => 1,
                    'checks' => array(
                        'config_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/config/'), 'Install'),
                        'cache_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/app/cache/'), 'Install'),
                        'log_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/app/logs/'), 'Install'),
                        'img_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/img/'), 'Install'),
                        'mails_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/mails/'), 'Install'),
                        'module_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/modules/'), 'Install'),
                        'theme_lang_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/themes/'._THEME_NAME_.'/lang/'), 'Install'),
                        'theme_pdf_lang_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/themes/'._THEME_NAME_.'/pdf/lang/'), 'Install'),
                        'theme_cache_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/themes/'._THEME_NAME_.'/cache/'), 'Install'),
                        'translations_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/translations/'), 'Install'),
                        'customizable_products_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/upload/'), 'Install'),
                        'virtual_products_dir' => $this->translator->trans('Recursive write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/download/'), 'Install'),
                        'config_sf2_dir' => $this->translator->trans('Write permissions for %user% user on %folder%', array('%user%' => $user, '%folder%' => '~/app/config/'), 'Install'),
                    )
                ),
            ),
            'optional' => array(
                array(
                    'title' => $this->translator->trans('Recommended PHP parameters', array(), 'Install'),
                    'success' => $this->tests['optional']['success'],
                    'checks' => array(
                        'fopen' => $this->translator->trans('Cannot open external URLs', array(), 'Install'),
                        'gz' => $this->translator->trans('GZIP compression is not activated', array(), 'Install'),
                        'mbstring' => $this->translator->trans('Mbstring extension is not enabled', array(), 'Install'),
                        'dom' => $this->translator->trans('Dom extension is not loaded', array(), 'Install'),
                    )
                ),
            ),
        );

        //Inject Sf2 errors to test render required
        foreach ($testsRequiredsf2 as $error) {
            $this->tests_render['required'][2]['checks'][] = $this->translator->trans($error->getHelpHtml(), array(), 'Install');
        }

        //Inject Sf2 optionnal config to test render optional
        foreach ($testsOptionalsf2 as $error) {
            $this->tests_render['optional'][0]['checks'][] = $this->translator->trans($error->getHelpHtml(), array(), 'Install');
        }

        foreach ($this->tests_render['required'] as &$category) {
            foreach ($category['checks'] as $id => $check) {
                if (!isset($this->tests['required']['checks'][$id]) || $this->tests['required']['checks'][$id] != 'ok') {
                    $category['success'] = 0;
                }
            }
        }

        //if sf2 requirement error found, force the required success to false
        if (count($testsRequiredsf2) > 0) {
            $this->tests['required']['success'] = false;
        }

        // If required tests failed, disable next button
        if (!$this->tests['required']['success']) {
            $this->next_button = false;
        }

        $this->displayTemplate('system');
    }
}
