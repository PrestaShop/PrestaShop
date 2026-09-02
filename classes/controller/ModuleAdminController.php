<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
abstract class ModuleAdminControllerCore extends AdminController
{
    /** @var Module */
    public $module;

    /**
     * @throws PrestaShopException
     */
    public function __construct()
    {
        parent::__construct();

        $this->controller_type = 'moduleadmin';

        $tab = new Tab($this->id);
        if (!$tab->module) {
            throw new PrestaShopException('Admin tab ' . get_class($this) . ' is not a module tab');
        }

        $this->module = Module::getInstanceByName($tab->module);
        if (!$this->module->id) {
            throw new PrestaShopException("Module {$tab->module} not found");
        }
    }

    /**
     * Creates a template object.
     *
     * @param string $tpl_name Template filename
     *
     * @return Smarty_Internal_Template
     */
    public function createTemplate($tpl_name)
    {
        if ($this->viewAccess()) {
            foreach ($this->getTemplateLookupPaths() as $path) {
                if (file_exists($path . $tpl_name)) {
                    return $this->context->smarty->createTemplate($path . $tpl_name);
                }
            }
        }

        return parent::createTemplate($tpl_name);
    }

    /**
     * Get path to back office templates for the module.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/';
    }

    /**
     * @return string[]
     */
    protected function getTemplateLookupPaths()
    {
        $templatePath = $this->getTemplatePath();

        return [
            _PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/admin/',
            $templatePath . $this->override_folder,
            $templatePath,
        ];
    }
}
