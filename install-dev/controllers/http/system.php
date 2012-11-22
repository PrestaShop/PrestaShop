<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Step 2 : check system configuration (permissions on folders, PHP version, etc.)
 */
class InstallControllerHttpSystem extends InstallControllerHttp
{
	public $tests = array();

	/**
	 * @var InstallModelSystem
	 */
	public $model_system;

	/**
	 * @see InstallAbstractModel::init()
	 */
	public function init()
	{
		require_once _PS_INSTALL_MODELS_PATH_.'system.php';
		$this->model_system = new InstallModelSystem();
	}

	/**
	 * @see InstallAbstractModel::processNextStep()
	 */
	public function processNextStep()
	{
	}

	/**
	 * Required tests must be passed to validate this step
	 *
	 * @see InstallAbstractModel::validate()
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
		if (!isset($this->tests['required']))
			$this->tests['required'] = $this->model_system->checkRequiredTests();

		if (!isset($this->tests['optional']))
			$this->tests['optional'] = $this->model_system->checkOptionalTests();

		// Generate display array
		$this->tests_render = array(
			'required' => array(
				array(
					'title' => $this->l('PHP parameters:'),
					'checks' => array(
						'phpversion' => $this->l('Is PHP 5.1.2 or later installed ?'),
						'upload' => $this->l('Can upload files ?'),
						'system' => $this->l('Can create new files and folders ?'),
						'gd' => $this->l('Is GD Library installed ?'),
						'mysql_support' => $this->l('Is MySQL support is on ?'),
					)
				),
				array(
					'title' => $this->l('Recursive write permissions on files and folders:'),
					'checks' => array(
						'config_dir' => '~/config/',
						'cache_dir' => '~/cache/',
						'log_dir' => '~/log/',
						'img_dir' => '~/img/',
						'mails_dir' => '~/mails/',
						'module_dir' => '~/modules/',
						'theme_lang_dir' => '~/themes/default/lang/',
						'theme_pdf_lang_dir' => '~/themes/default/pdf/lang/',
						'theme_cache_dir' => '~/themes/default/cache/',
						'translations_dir' => '~/translations/',
						'customizable_products_dir' => '~/upload/',
						'virtual_products_dir' => '~/download/',
						'sitemap' => '~/sitemap.xml',
					)
				),
			),
			'optional' => array(
				array(
					'title' => $this->l('PHP parameters:'),
					'checks' => array(
						'fopen' => $this->l('Can open external URLs ?'),
						'register_globals' => $this->l('Is PHP register global option off (recommended) ?'),
						'gz' => $this->l('Is GZIP compression activated (recommended) ?'),
						'mcrypt' => $this->l('Is Mcrypt extension available (recommended) ?'),
						'magicquotes' => $this->l('Is PHP magic quotes option deactivated (recommended) ?'),
						'dom' => $this->l('Is Dom extension loaded ?'),
						'pdo_mysql' => $this->l('Is PDO MySQL extension loaded ?'),
					)
				),
			),
		);

		// If required tests failed, disable next button
		if (!$this->tests['required']['success'])
			$this->next_button = false;

		$this->displayTemplate('system');
	}
}

