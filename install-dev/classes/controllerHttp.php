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
    protected $content;

    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * @var string Current step
     */
    public $step;

    /**
     * @var array List of errors
     */
    public $errors = [];

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
    protected $__vars = [];

    /**
     * @var array Configuration
     */
    protected static $config;

    private function initSteps()
    {
        $stepConfig = [
            [
                'name' => 'welcome',
                'displayName' => $this->translator->trans('Choose your language', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpWelcome',
            ],
            [
                'name' => 'license',
                'displayName' => $this->translator->trans('License agreements', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpLicense',
            ],
            [
                'name' => 'system',
                'displayName' => $this->translator->trans('System compatibility', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpSystem',
            ],
            [
                'name' => 'configure',
                'displayName' => $this->translator->trans('Store information', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpConfigure',
            ],
            [
                'name' => 'content',
                'displayName' => $this->translator->trans('Content of your store', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpContent',
            ],
            [
                'name' => 'database',
                'displayName' => $this->translator->trans('System configuration', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpDatabase',
            ],
            [
                'name' => 'process',
                'displayName' => $this->translator->trans('Store installation', [], 'Install'),
                'controllerClass' => 'InstallControllerHttpProcess',
            ],
        ];

        static::$steps = new StepList($stepConfig);
    }

    public function __construct()
    {
        $this->session = InstallSession::getInstance();

        // Set current language
        $this->language = LanguageList::getInstance();
        $detect_language = $this->language->detectLanguage();

        if (empty($this->session->lang)) {
            // Set the en as default fallback in case we can't detect a better one
            $this->session->lang = 'en';
            if (isset($detect_language['primarytag'])
                && in_array($detect_language['primarytag'], $this->language->getIsoList())) {
                $this->session->lang = $detect_language['primarytag'];
            }
        }

        Context::getContext()->language = $this->language->getLanguage($this->session->lang);
        $this->translator = Context::getContext()->getTranslator(true);
        $this->language->setLanguage($this->session->lang);

        if (empty(self::getSteps())) {
            $this->initSteps();
        }

        $this->loadConfiguration();
        $this->init();
    }

    public function setCurrentStep($step)
    {
        $this->step = $step;

        return $this;
    }

    final public static function execute()
    {
        $self = new static();

        $session = InstallSession::getInstance();
        if (!$session->last_step || $session->last_step === 'welcome') {
            \PrestaShop\Autoload\PrestashopAutoload::getInstance()->generateIndex();
        }

        if (empty($session->last_step)) {
            $session->last_step = self::getSteps()->current()->getName();
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
            self::getSteps()->setOffsetFromStepName(Tools::getValue('step'));
            $session->step = self::getSteps()->current()->getName();
        } elseif (!empty($session->step)) {
            self::getSteps()->setOffsetFromStepName($session->step);
        }

        // Validate all steps until current step. If a step is not valid, use it as current step.
        foreach (self::getSteps() as $key => $check_step) {
            // Do not validate current step

            if (self::getSteps()->current() == $check_step) {
                break;
            }

            // no need to validate several time the system step
            if (!(($check_step->getControllerInstance()) instanceof InstallControllerHttpSystem)
                && !$check_step->getControllerInstance()->validate()) {
                self::getSteps()->setOffset($key);
                $session->step = $session->last_step = self::getSteps()->current()->getName();

                break;
            }
        }

        // Submit form to go to next step
        if (Tools::getValue('submitNext')) {
            self::getSteps()->current()->getControllerInstance()->processNextStep();

            // If current step is validated, let's go to next step
            if (self::getSteps()->current()->getControllerInstance()->validate()) {
                self::getSteps()->next();
            }

            // Don't display system step if mandatory requirements is valid
            if (self::getSteps()->current()->getName() == 'system' && self::getSteps()->current()->getControllerInstance()->validate()) {
                self::getSteps()->next();
            }

            $session->step = self::getSteps()->current()->getName();

            // Change last step
            if (self::getSteps()->getOffset() > $self->getStepOffset($session->last_step)) {
                $session->last_step = self::getSteps()->current()->getName();
            }
        }
        // Go to previous step
        elseif (Tools::getValue('submitPrevious') && 0 !== self::getSteps()->getOffset()) {
            self::getSteps()->previous();
            $session->step = self::getSteps()->current()->getName();
        }

        self::getSteps()->current()->getControllerInstance()->process();
        self::getSteps()->current()->getControllerInstance()->display();
    }

    /**
     * Display controller view
     */
    public function display(): void
    {
    }

    /**
     * Validate current step
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Initialize
     */
    public function init(): void
    {
    }

    /**
     * Process installation
     */
    public function process(): void
    {
    }

    /**
     * Process next step
     */
    public function processNextStep(): void
    {
    }

    /**
     * Get steps list
     *
     * @return StepList
     */
    public static function getSteps(): ?StepList
    {
        return static::$steps;
    }

    public function getLastStep()
    {
        return $this->session->last_step;
    }

    /**
     * Find offset of a step by name
     *
     * @param string $step Step name
     *
     * @return int
     */
    public function getStepOffset($step)
    {
        return self::getSteps()->getOffsetFromStepName($step);
    }

    /**
     * Make a HTTP redirection to a step
     *
     * @param string $step
     */
    public function redirect(string $step)
    {
        header('location: index.php?step=' . $step);
        exit;
    }

    /**
     * Check if current step is first step in list of steps
     *
     * @return bool
     */
    public function isFirstStep()
    {
        return self::getSteps()->isFirstStep();
    }

    /**
     * Check if current step is last step in list of steps
     *
     * @return bool
     */
    public function isLastStep()
    {
        return self::getSteps()->isLastStep();
    }

    /**
     * Check is given step is already finished
     *
     * @param string $step
     *
     * @return bool
     */
    public function isStepFinished(string $step): bool
    {
        return $this->getStepOffset($step) < self::getSteps()->getOffset();
    }

    /**
     * Send AJAX response in JSON format {success: bool, message: string}
     *
     * @param bool $success
     * @param string $message
     */
    public function ajaxJsonAnswer(bool $success, $message = ''): void
    {
        if (!$success && empty($message)) {
            $message = print_r(@error_get_last(), true);
        }

        die(json_encode([
            'success' => (bool) $success,
            'message' => $message,
        ]));
    }

    /**
     * Display a template
     *
     * @param string $template Template name
     */
    public function getTemplate(string $template): string
    {
        $path = _PS_INSTALL_PATH_ . 'theme/views/';
        $customPath = _PS_INSTALL_PATH_ . 'theme/custom/';

        if (file_exists($customPath . $template . '.php')) {
            return $this->renderTemplate($customPath, $template);
        }

        if (file_exists($path . $template . '.php')) {
            return $this->renderTemplate($path, $template);
        }

        throw new PrestashopInstallerException("Template '{$template}.php' not found");
    }

    /**
     * Display a hook template
     *
     * @param string $template Template name
     */
    public function getHook(string $template): string
    {
        $path = _PS_INSTALL_PATH_ . 'theme/custom/hooks/';

        if (file_exists($path . $template . '.php')) {
            return $this->renderTemplate($path, $template);
        }

        return '';
    }

    protected function loadConfiguration(): void
    {
        if (self::$config === null) {
            $path = _PS_INSTALL_PATH_ . 'theme/config.php';
            $customPath = _PS_INSTALL_PATH_ . 'theme/custom/config.php';

            if (file_exists($customPath)) {
                self::$config = include $customPath;
                return;
            }

            if (file_exists($path)) {
                self::$config = include $path;
                return;
            }

            throw new PrestashopInstallerException("Config file not found");
        }
    }

    public function getConfig(string $element)
    {
        return self::$config[$element] ?: null;
    }

    public function displayContent(string $content): void
    {
        $this->setContent($this->getTemplate($content));
        echo $this->getTemplate('layout');
    }

    protected function setContent(string $content): void
    {
        $this->content = $content;
    }

    protected function getContent(): string
    {
        return $this->content;
    }

    protected function renderTemplate(string $path, string $template): string
    {
        ob_start();

        include $path . $template . '.php';

        $content = ob_get_contents();
        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        return $content;
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
