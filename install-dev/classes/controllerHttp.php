<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class InstallControllerHttp
{
    /**
     * @var array List of installer steps
     */
    protected static $steps = array();

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
     * InstallLanguages
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
     * @var InstallAbstractModel
     */
    public $model;

    /**
     * @var array Magic vars
     */
    protected $__vars = array();

    public function __construct()
    {
        $this->session = InstallSession::getInstance();

        // Set current language
        $this->language = InstallLanguages::getInstance();
        $detect_language = $this->language->detectLanguage();

        Context::getContext()->language =  $this->language;
        Context::getContext()->locale =  $this->language->locale;

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

        self::$steps = array(
            'welcome' => $this->translator->trans('Choose your language', array(), 'Install'),
            'license'=> $this->translator->trans('License agreements', array(), 'Install'),
            'system'=> $this->translator->trans('System compatibility', array(), 'Install'),
            'configure'=> $this->translator->trans('Store information', array(), 'Install'),
            'database'=> $this->translator->trans('System configuration', array(), 'Install'),
            'process'=> $this->translator->trans('Store installation', array(), 'Install')
        );

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

        // Include all controllers
        foreach (array_keys(self::$steps) as $step) {
            if (!file_exists(_PS_INSTALL_CONTROLLERS_PATH_.'http/'.$step.'.php')) {
                throw new PrestashopInstallerException("Controller file 'http/{$step}.php' not found");
            }

            require_once _PS_INSTALL_CONTROLLERS_PATH_.'http/'.$step.'.php';
            $classname = 'InstallControllerHttp'.$step;
            $controller = new $classname();
            $controller->setCurrentStep($step);
            self::$instances[$step] = $controller;
        }

        if (!$session->last_step || !in_array($session->last_step, array_keys(self::$steps))) {
            $session->last_step = array_keys(self::$steps)[0];
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
            $current_step = Tools::getValue('step');
            $session->step = $current_step;
        } else {
            $current_step = (isset($session->step)) ? $session->step : array_keys(self::$steps)[0];
        }

        if (!in_array($current_step, array_keys(self::$steps))) {
            $current_step = array_keys(self::$steps)[0];
        }


        // Validate all steps until current step. If a step is not valid, use it as current step.
        foreach (array_keys(self::$steps) as $check_step) {
            // Do not validate current step
            if ($check_step == $current_step) {
                break;
            }

            if (!self::$instances[$check_step]->validate()) {
                $current_step = $check_step;
                $session->step = $current_step;
                $session->last_step = $current_step;
                break;
            }
        }

        // Submit form to go to next step
        if (Tools::getValue('submitNext')) {

            self::$instances[$current_step]->processNextStep();

            // If current step is validated, let's go to next step
            if (self::$instances[$current_step]->validate()) {
                $current_step = self::$instances[$current_step]->findNextStep();
            }
            $session->step = $current_step;

            // Change last step
            if (self::getStepOffset($current_step) > self::getStepOffset($session->last_step)) {
                $session->last_step = $current_step;
            }
        }
        // Go to previous step
        elseif (Tools::getValue('submitPrevious') && $current_step != array_keys(self::$steps)[0]) {
            $current_step = self::$instances[$current_step]->findPreviousStep($current_step);
            $session->step = $current_step;
        }

        self::$instances[$current_step]->process();
        self::$instances[$current_step]->display();
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
        return array(
            'welcome' => $this->translator->trans('Choose your language', array(), 'Install'),
            'license'=> $this->translator->trans('License agreements', array(), 'Install'),
            'system'=> $this->translator->trans('System compatibility', array(), 'Install'),
            'configure'=> $this->translator->trans('Store information', array(), 'Install'),
            'database'=> $this->translator->trans('System configuration', array(), 'Install'),
            'process'=> $this->translator->trans('Store installation', array(), 'Install')
        );
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
        static $flip = null;
        $steps = array_keys(self::$steps);

        if (is_numeric($step)) {
            $step = array_search($steps, $steps);
        }

        if (is_null($flip)) {
            $flip = array_flip($steps);
        }

        return $flip[$step];
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
     * Find previous step
     *
     * @param string $step
     */
    public function findPreviousStep()
    {
        $steps = array_keys(self::$steps);
        return (isset($steps[$this->getStepOffset($this->step) - 1])) ? $steps[$this->getStepOffset($this->step) - 1] : false;
    }

    /**
     * Find next step
     *
     * @param string $step
     */
    public function findNextStep()
    {
        $steps = array_keys(self::$steps);

        $nextStep = (isset($steps[$this->getStepOffset($this->step) + 1])) ? $steps[$this->getStepOffset($this->step) + 1] : false;

        if ($nextStep == 'system' && self::$instances[$nextStep]->validate()) {
            $nextStep = self::$instances[$nextStep]->findNextStep();
        }

        return $nextStep;
    }

    /**
     * Check if current step is first step in list of steps
     *
     * @return bool
     */
    public function isFirstStep()
    {
        return self::getStepOffset($this->step) == 0;
    }

    /**
     * Check if current step is last step in list of steps
     *
     * @return bool
     */
    public function isLastStep()
    {
        return self::getStepOffset($this->step) == (count(self::$steps) - 1);
    }

    /**
     * Check is given step is already finished
     *
     * @param string $step
     * @return bool
     */
    public function isStepFinished($step)
    {
        return self::getStepOffset($step) < self::getStepOffset($this->getLastStep());
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
            $this->phone = $this->language->getInformation('phone', false);
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
        return $this->language->getInformation('documentation');
    }

    /**
     * Get link to tutorial video for this language
     *
     * Enter description here ...
     */
    public function getTutorialLink()
    {
        return $this->language->getInformation('tutorial');
    }

    /**
     * Get link to tailored help for this language
     *
     * Enter description here ...
     */
    public function getTailoredHelp()
    {
        return $this->language->getInformation('tailored_help');
    }

    /**
     * Get link to forum for this language
     *
     * Enter description here ...
     */
    public function getForumLink()
    {
        return $this->language->getInformation('forum');
    }

    /**
     * Get link to blog for this language
     *
     * Enter description here ...
     */
    public function getBlogLink()
    {
        return $this->language->getInformation('blog');
    }

    /**
     * Get link to support for this language
     *
     * Enter description here ...
     */
    public function getSupportLink()
    {
        return $this->language->getInformation('support');
    }

    public function getDocumentationUpgradeLink()
    {
        return $this->language->getInformation('documentation_upgrade', true);
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
