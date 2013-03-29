<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class InstallControllerConsole
{
	/**
	 * @var array List of installer steps
	 */
	protected static $steps = array('process');

	protected static $instances = array();

	/**
	 * @var string Current step
	 */
	public $step;

	/**
	 * @var array List of errors
	 */
	public $errors = array();

	/**
	 * @var InstallController
	 */
	public $controller;

	/**
	 * @var InstallSession
	 */
	public $session;

	/**
	 * @var InstallLanguages
	 */
	public $language;

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

	/**
	 * Process form to go to next step
	 */
	abstract public function processNextStep();

	/**
	 * Validate current step
	 */
	abstract public function validate();

	final public static function execute()
	{

		// Include all controllers
		foreach (self::$steps as $step)
		{
			if (!file_exists(_PS_INSTALL_CONTROLLERS_PATH_.'console/'.$step.'.php'))
				throw new PrestashopInstallerException("Controller file 'console/{$step}.php' not found");

			require_once _PS_INSTALL_CONTROLLERS_PATH_.'console/'.$step.'.php';
			$classname = 'InstallControllerConsole'.$step;
			self::$instances[$step] = new $classname($step);
		}

		$datas = Datas::getInstance();

		/* redefine HTTP_HOST  */
		$_SERVER['HTTP_HOST'] = $datas->http_host;

		@date_default_timezone_set($datas->timezone);

		if (!$current_step = $datas->step)
			return false;

		self::$instances[$current_step]->process();
	}

	final public function __construct($step)
	{
		$this->step = $step;
		$this->datas = Datas::getInstance();
		// Set current language
		$this->language = InstallLanguages::getInstance();
		if (!$this->datas->language)
			die('No language defined');
		$this->language->setLanguage($this->datas->language);

		$this->init();
	}

	/**
	 * Initialize model
	 */
	public function init()
	{
	}

	public function printErrors()
	{
		$errors = $this->model_install->getErrors();
		if (count($errors))
		{
			if (!is_array($errors))
				$errors = array($errors);
			echo 'Errors :'."\n";
			foreach ($errors as $error_process)
				foreach ($error_process as $error)
					echo $error."\n";
			die;
		}
	}

	/**
	 * Get translated string
	 *
	 * @param string $str String to translate
	 * @param ... All other params will be used with sprintf
	 * @return string
	 */
	public function l($str)
	{
		$args = func_get_args();
		return call_user_func_array(array($this->language, 'l'), $args);
	}

	public function process()
	{
	}
}
