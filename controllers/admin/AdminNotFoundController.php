<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AdminNotFoundControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    /**
     * AdminController::initContent() override.
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        $this->errors[] = $this->trans('Page not found', [], 'Admin.Notifications.Error');
        $tpl_vars['controller'] = Tools::getValue('controllerUri', Tools::getValue('controller'));
        $this->context->smarty->assign($tpl_vars);
        parent::initContent();
    }
}
