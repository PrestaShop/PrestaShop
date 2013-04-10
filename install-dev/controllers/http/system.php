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
					'success' => 1,
					'checks' => array(
						'phpversion' => $this->l('PHP 5.1.2 or later is not enabled'),
						'upload' => $this->l('Cannot upload files'),
						'system' => $this->l('Cannot create new files and folders'),
						'gd' => $this->l('GD Library is not installed'),
						'mysql_support' => $this->l('MySQL support is not activated'),
					)
				),
				array(
					'title' => $this->l('Recursive write permissions on files and folders:'),
					'success' => 1,
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
					'success' => $this->tests['optional']['success'],
					'checks' => array(
						'fopen' => $this->l('Cannot open external URLs'),
						'register_globals' => $this->l('PHP register global option is on'),
						'gz' => $this->l('GZIP compression is not activated'),
						'mcrypt' => $this->l('Mcrypt extension is not enabled'),
						'mbstring' => $this->l('Mbstring extension is not enabled'),
						'magicquotes' => $this->l('PHP magic quotes option is enabled'),
						'dom' => $this->l('Dom extension is not loaded'),
						'pdo_mysql' => $this->l('PDO MySQL extension is not loaded'),
					)
				),
			),
		);

		foreach ($this->tests_render['required'] as &$category)
			foreach ($category['checks'] as $id => $check)
				if ($this->tests['required']['checks'][$id] != 'ok')
					$category['success'] = 0;
		
		// If required tests failed, disable next button
		if (!$this->tests['required']['success'])
			$this->next_button = false;

		$this->displayTemplate('system');
	}
}