<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShopBundle\Install\LanguageList;

class InstallControllerHttp
{
    /**
     * @var StepList List of installer steps
     */
    protected static $steps;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var array
     */
    protected static $instances = array();

    /**
     * @var string Current step
     */
    public $step;

    /**
     * @var array List of errors
     */
    public $errors = array();

    public $controller;

    /**
     * @var InstallSession
     */
    public $session;

    /**
     * LanguageList
     */
    public $language;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    public $translator;

    /**
     * @var bool If false, disable next button access
     */
    public $next_button = true;

    /**
     * @var bool If false, disable previous button access
     */
    public $previous_button = true;

    /**
     * @var \PrestaShopBundle\Install\AbstractInstall
     */
    public $model;

    /**
     * @var array Magic vars
     */
    protected $__vars = array();

    private function initSteps()
    {
        $stepConfig = array(
            array(
                'name' => 'welcome',
                'displayName' => $this->translator->trans('Choose your language', array(), 'Install'),
                'controllerClass' => 'InstallControllerHttpWelcome'
            ),
            array(
                'name' => 'license',
                'displayName' => $this->translator->trans('License agreements', array(), 'Install'),
                'controllerClass' => 'InstallControllerHttpLicense'
            ),
            array(
                'name' => 'system',
                'displayName' => $this->translator->trans('System compatibility', array(), 'Install'),
                'controllerClass' => 'InstallControllerHttpSystem'
            ),
            array(
                'name' => 'configure',
                'displayName' => $this->translator->trans('Store information', array(), 'Install'),
                'controllerClass' => 'InstallControllerHttpConfigure'
            ),
            array(
                'name' => 'database',
                'displayName' => $this->translator->trans('System configuration', array(), 'Install'),
                'controllerClass' => 'InstallControllerHttpDatabase'
            ),
            array(
                'name' => 'process',
                'displayName' => $this->translator->trans('Store installation', array(), 'Install'),
                'controllerClass' => 'InstallControllerHttpProcess'
            ),
        );
        self::$steps = new StepList($stepConfig);
    }

    public function __construct()
    {
        $this->session = InstallSession::getInstance();

        // Set current language
        $this->language = LanguageList::getInstance();
        $detect_language = $this->language->detectLanguage();

        if (empty($this->session->lang)) {
            $this->session->lang = $detect_language['primarytag'];
        }

        Context::getContext()->language = $this->language->getLanguage(
            $this->session->lang ?: false
        );

        $this->translator = Context::getContext()->getTranslator();

        if (isset($this->session->lang)) {
            $lang = $this->session->lang;
        } else {
            $lang = (isset($detect_language['primarytag'])) ? $detect_language['primarytag'] : false;
        }

        if (!in_array($lang, $this->language->getIsoList())) {
            $lang = 'en';
        }
        $this->language->setLanguage($lang);

        if (empty(self::$steps)) {
            $this->initSteps();
        }

        $this->init();
    }

    public function setCurrentStep($step)
    {
        $this->step = $step;

        return $this;
    }

    final public static function execute()
    {
        $self = new self();

        if (Tools::getValue('compile_templates')) {
            require_once(_PS_INSTALL_CONTROLLERS_PATH_.'http/smarty_compile.php');
            exit;
        }

        $session = InstallSession::getInstance();
        if (!$session->last_step || $session->last_step == 'welcome') {
            Tools::generateIndex();
        }

        if (empty($session->last_step)) {
            $session->last_step = self::$steps->current()->getName();
        }

        // Set timezone
        if ($session->shop_timezone) {
            try {
                // Search if the session timezone is known in the present zones list.
                // An unknown timezone can be stored here, in case of upgrade of PHP version (change in many timezones, as disappeared US/Eastern for example).
                foreach (DateTimeZone::listAbbreviations() as $abbreviations) {
                    foreach ($abbreviations as $abbreviation) {
                        if ($session->shop_timezone == $abbreviation['timezone_id']) {
                            @date_default_timezone_set($session->shop_timezone);
                            break 2;
                        }
                    }
                }
                // If not know, does not affect PHP settings. Another default setting is forced before.
            } catch (\Exception $e) {
                // for older behavior, keep old way to affect timezone.
                @date_default_timezone_set($session->shop_timezone);
            }
        }

        // Get current step (check first if step is changed, then take it from session)
        if (Tools::getValue('step')) {
            self::$steps->setOffsetFromStepName(Tools::getValue('step'));
            $session->step = self::$steps->current()->getName();
        } elseif (!empty($session->step)) {
            self::$steps->setOffsetFromStepName($session->step);
        }

        // Validate all steps until current step. If a step is not valid, use it as current step.
        foreach (self::$steps as $key => $check_step) {
            // Do not validate current step

            if (self::$steps->current() == $check_step) {
                break;
            }

            if (!$check_step->getControllerInstance()->validate()) {
                self::$steps->setOffset($key);
                $session->step = $session->last_step = self::$steps->current()->getName();
                break;
            }
        }

        // Submit form to go to next step
        if (Tools::getValue('submitNext')) {

            self::$steps->current()->getControllerInstance()->processNextStep();

            // If current step is validated, let's go to next step
            if (self::$steps->current()->getControllerInstance()->validate()) {
                self::$steps->next();
            }
            $session->step = self::$steps->current()->getName();

            // Change last step
            if (self::$steps->getOffset() > self::getStepOffset($session->last_step)) {
                $session->last_step = self::$steps->current()->getName();
            }
        }
        // Go to previous step
        elseif (Tools::getValue('submitPrevious') && 0 !== self::$steps->getOffset()) {
            self::$steps->previous();
            $session->step = self::$steps->current()->getName();
        }

        self::$steps->current()->getControllerInstance()->process();
        self::$steps->current()->getControllerInstance()->display();
    }

    public function init()
    {

    }

    public function process()
    {
    }

    /**
     * Get steps list
     *
     * @return array
     */
    public function getSteps()
    {
        return self::$steps;
    }

    public function getLastStep()
    {
        return $this->session->last_step;
    }

    /**
     * Find offset of a step by name
     *
     * @param string $step Step name
     * @return int
     */
    public static function getStepOffset($step)
    {
        return self::$steps->getOffsetFromStepName($step);
    }

    /**
     * Make a HTTP redirection to a step
     *
     * @param string $step
     */
    public function redirect($step)
    {
        header('location: index.php?step='.$step);
        exit;
    }

    /**
     * Check if current step is first step in list of steps
     *
     * @return bool
     */
    public function isFirstStep()
    {
        return self::$steps->isFirstStep();
    }

    /**
     * Check if current step is last step in list of steps
     *
     * @return bool
     */
    public function isLastStep()
    {
        return self::$steps->isLastStep();
    }

    /**
     * Check is given step is already finished
     *
     * @param string $step
     * @return bool
     */
    public function isStepFinished($step)
    {
        return self::getStepOffset($step) < self::$steps->getOffset();
    }

    /**
     * Get telephone used for this language
     *
     * @return string
     */
    public function getPhone()
    {
        if (InstallSession::getInstance()->support_phone != null) {
            return InstallSession::getInstance()->support_phone;
        }
        if ($this->phone === null) {
            $this->phone = $this->translator->trans('XXXXXXXXXXXXXX', array(), 'Install');
            if ($iframe = Tools::file_get_contents('http://api.prestashop.com/iframe/install.php?lang='.$this->language->getLanguageIso(), false, null, 3)) {
                if (preg_match('/<img.+alt="([^"]+)".*>/Ui', $iframe, $matches) && isset($matches[1])) {
                    $this->phone = $matches[1];
                }
            }
        }
        InstallSession::getInstance()->support_phone = $this->phone;
        return $this->phone;
    }

    /**
     * Get link to documentation for this language
     *
     * Enter description here ...
     */
    public function getDocumentationLink()
    {
        /* Link to translated documentation (if available) */
        return $this->translator->trans('http://doc.prestashop.com/display/PS17/Installing+PrestaShop', array(), 'Install');
    }

    /**
     * Get link to tutorial video for this language
     *
     * Enter description here ...
     */
    public function getTutorialLink()
    {
        /* Link to localized video tutorial (if available) */
        return $this->translator->trans('https://www.youtube.com/watch?v=psz4aIPZZuk', array(), 'Install');
    }

    /**
     * Get link to tailored help for this language
     *
     * Enter description here ...
     */
    public function getTailoredHelp()
    {
        /* Link to support on addons */
        return $this->translator->trans('http://addons.prestashop.com/en/388-support', array(), 'Install');
    }

    /**
     * Get link to forum for this language
     *
     * Enter description here ...
     */
    public function getForumLink()
    {
        /* Link to localized forum */
        return $this->translator->trans('http://www.prestashop.com/forums/', array(), 'Install');
    }

    /**
     * Get link to blog for this language
     *
     * Enter description here ...
     */
    public function getBlogLink()
    {
        return $this->translator->trans('http://www.prestashop.com/blog/', array(), 'Install');
    }

    /**
     * Get link to support for this language
     *
     * Enter description here ...
     */
    public function getSupportLink()
    {
        return $this->translator->trans('https://www.prestashop.com/en/support', array(), 'Install');
    }

    public function getDocumentationUpgradeLink()
    {
        return $this->translator->trans('http://docs.prestashop.com/display/PS16/Updating+PrestaShop', array(), 'Install');
    }

    /**
     * Send AJAX response in JSON format {success: bool, message: string}
     *
     * @param bool $success
     * @param string $message
     */
    public function ajaxJsonAnswer($success, $message = '')
    {
        if (!$success && empty($message)) {
            $message = print_r(@error_get_last(), true);
        }
        die(json_encode(array(
            'success' => (bool)$success,
            'message' => $message,
            // 'memory' => round(memory_get_peak_usage()/1024/1024, 2).' Mo',
        )));
    }

    /**
     * Display a template
     *
     * @param string $template Template name
     * @param bool $get_output Is true, return template html
     * @return string
     */
    public function displayTemplate($template, $get_output = false, $path = null)
    {
        if (!$path) {
            $path = _PS_INSTALL_PATH_.'theme/views/';
        }

        if (!file_exists($path.$template.'.php')) {
            throw new PrestashopInstallerException("Template '{$template}.php' not found");
        }

        if ($get_output) {
            ob_start();
        }

        include($path.$template.'.php');

        if ($get_output) {
            $content = ob_get_contents();
            if (ob_get_level() && ob_get_length() > 0) {
                ob_end_clean();
            }
            return $content;
        }
    }

    public function &__get($varname)
    {
        if (isset($this->__vars[$varname])) {
            $ref = &$this->__vars[$varname];
        } else {
            $null = null;
            $ref = &$null;
        }
        return $ref;
    }

    public function __set($varname, $value)
    {
        $this->__vars[$varname] = $value;
    }

    public function __isset($varname)
    {
        return isset($this->__vars[$varname]);
    }

    public function __unset($varname)
    {
        unset($this->__vars[$varname]);
    }
}
