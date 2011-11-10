<?php
include_once('../init.php');
include_once(_PS_ROOT_DIR_.'/config/settings.inc.php');
include_once(_PS_INSTALL_PATH_.'classes/controllerHttp.php');

class SynchronizeController extends InstallControllerHttp
{
	public function validate(){}
	public function display(){}
	public function processNextStep(){}

	/**
	 * @var InstallXmlLoader
	 */
	protected $loader;

	public function displayTemplate($template)
	{
		parent::displayTemplate($template, false, _PS_INSTALL_PATH_.'dev/');
	}

	public function init()
	{
		$this->type = Tools::getValue('type');
		$this->loader = new InstallXmlLoader();
		$languages = array();
		foreach (Language::getLanguages(false) as $language)
			$languages[$language['id_lang']] = $language['iso_code'];
		$this->loader->setLanguages($languages);

		if (Tools::getValue('submit'))
			$this->synchronizeDatabase();

		if ($this->type == 'demo')
			$this->loader->setFixturesPath();
		$this->displayTemplate('index');
	}

	public function synchronizeDatabase()
	{
		if ($this->type == 'demo')
		{
			$this->loader->setDefaultPath();
			$this->loader->generateAllEntityFiles();
			$this->loader->setFixturesPath();
		}

		$tables = isset($_POST['tables']) ? (array)$_POST['tables'] : array();
		$columns = isset($_POST['columns']) ? (array)$_POST['columns'] : array();
		$relations = isset($_POST['relations']) ? (array)$_POST['relations'] : array();
		$ids = isset($_POST['id']) ? (array)$_POST['id'] : array();
		$primaries = isset($_POST['primary']) ? (array)$_POST['primary'] : array();
		$classes = isset($_POST['class']) ? (array)$_POST['class'] : array();
		$sqls = isset($_POST['sql']) ? (array)$_POST['sql'] : array();
		$images = isset($_POST['image']) ? (array)$_POST['image'] : array();

		$entities = array();
		foreach ($tables as $table)
		{
			$config = array();
			if (isset($ids[$table]) && $ids[$table])
				$config['id'] = $ids[$table];

			if (isset($primaries[$table]) && $primaries[$table])
				$config['primary'] = $primaries[$table];

			if (isset($classes[$table]) && $classes[$table])
				$config['class'] = $classes[$table];

			if (isset($sqls[$table]) && $sqls[$table])
				$config['sql'] = $sqls[$table];

			if (isset($images[$table]) && $images[$table])
				$config['image'] = $images[$table];

			$fields = array();
			if (isset($columns[$table]))
			{
				foreach ($columns[$table] as $column)
				{
					$fields[$column] = array();
					if (isset($relations[$table][$column]['check']))
						$fields[$column]['relation'] = $relations[$table][$column];
				}
			}

			$entities[$table] = array(
				'config' => $config,
				'fields' => $fields,
			);
		}
		$this->loader->generateEntityFiles($entities);

		if ($this->type != 'demo')
		{
			$this->loader->setFixturesPath();
			$this->loader->generateAllEntityFiles();
			$this->loader->setDefaultPath();
		}

		$this->errors = $this->loader->getErrors();
	}
}

new SynchronizeController('synchronize');